<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\City;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class CheckoutController extends BaseController
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            getSetting('razorpay_key'),
            getSetting('razorpay_secret')
        );
    }

    public function processCheckout(CheckoutRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $customerId = $request->customer_id;
                $cartItems = Cart::where('customer_id', $customerId)->get();

                if ($cartItems->isEmpty()) {
                    return $this->error("Your cart is empty", 400);
                }

                $addressData = $this->handleAddress($request);

                $totals = $this->calculateTotals($cartItems);

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'customer_id' => $customerId,
                    'customer_name' => $addressData['name'],
                    'customer_email' => $request->email,
                    'customer_phone' => $addressData['phone'],
                    'shipping_address' => $addressData['address_line1'] . ($addressData['address_line2'] ? ', ' . $addressData['address_line2'] : ''),
                    'city' => $addressData['city_name'],
                    'state' => $addressData['state_name'],
                    'country' => $addressData['country_name'],
                    'postal_code' => $addressData['pincode'],
                    'items_count' => $cartItems->sum('quantity'),
                    'subtotal' => $totals['subtotal'],
                    'tax' => $totals['tax'],
                    'shipping' => $totals['shipping'],
                    'total' => $totals['total'],
                    'payment_method' => $request->payment_method,
                    'status' => 'pending',
                ]);

                foreach ($cartItems as $item) {
                    $product = Product::find($item->product_id);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'customer_id' => $customerId,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $product->price,
                        'total' => $product->price * $item->quantity,
                    ]);
                }

                $razorpayOrder = null;
                if ($request->payment_method === 'razorpay') {
                    $razorpayOrder = $this->razorpay->order->create([
                        'amount' => $totals['total'] * 100,
                        'currency' => 'INR',
                        'receipt' => 'order_' . $order->id,
                    ]);

                    $order->update([
                        'payment_reference' => $razorpayOrder->id
                    ]);
                }

                return $this->success([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'razorpay_order_id' => $razorpayOrder ? $razorpayOrder->id : null,
                    'amount' => $totals['total'],
                    'currency' => 'INR'
                ], "Checkout initiated successfully");
            });
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    private function handleAddress(CheckoutRequest $request)
    {
        if ($request->address_id) {
            $address = CustomerAddress::with(['city', 'state', 'country'])->find($request->address_id);
            return [
                'name' => $address->name,
                'phone' => $address->phone,
                'address_line1' => $address->address_line1,
                'address_line2' => $address->address_line2,
                'city_name' => $address->city->name,
                'state_name' => $address->state->name,
                'country_name' => $address->country->name,
                'pincode' => $address->pincode,
            ];
        }

        if ($request->save_address) {
            $newAddress = CustomerAddress::create([
                'customer_id' => $request->customer_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city_id' => $request->city_id,
                'state_id' => $request->state_id,
                'country_id' => $request->country_id,
                'pincode' => $request->pincode,
                'is_default' => !CustomerAddress::where('customer_id', $request->customer_id)->exists(),
            ]);
        }

        $city = City::find($request->city_id);
        $state = State::find($request->state_id);
        $country = Country::find($request->country_id);

        return [
            'name' => $request->name,
            'phone' => $request->phone,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city_name' => $city->name,
            'state_name' => $state->name,
            'country_name' => $country->name,
            'pincode' => $request->pincode,
        ];
    }

    private function calculateTotals($cartItems)
    {
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            $subtotal += $product->price * $item->quantity;
        }

        $tax = $subtotal * 0.18;
        $shipping = $subtotal > 5000 ? 0 : 150;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $subtotal + $tax + $shipping
        ];
    }
}
