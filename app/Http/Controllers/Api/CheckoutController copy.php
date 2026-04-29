<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\State;
use Razorpay\Api\Api;
use App\Models\OrderItem;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CheckoutRequest;

class CheckoutController extends BaseController
{
    private ?Api $razorpay = null;

    private function razorpay(): Api
    {
        if ($this->razorpay === null) {
            $key    = getSetting('razorpay_key_id');
            $secret = getSetting('razorpay_key_secret');

            if (empty($key) || empty($secret)) {
                throw new \RuntimeException('Razorpay payment gateway is not configured.');
            }

            $this->razorpay = new Api($key, $secret);
        }

        return $this->razorpay;
    }

    public function processCheckout(CheckoutRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $customerId      = $request->customer_id;
                $requestProducts = collect($request->products);
                $productIds      = $requestProducts->pluck('product_id')->toArray();

                $cartProductIds = Cart::where('customer_id', $customerId)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();

                $missingIds = array_diff($productIds, $cartProductIds);
                if (!empty($missingIds)) {
                    return $this->error("Products not found in cart: " . implode(', ', $missingIds), 400);
                }

                $addressData = $this->handleShippingAddress($request);
                $billingData = $this->handleBillingAddress($request, $addressData);
                $totals      = $this->calculateTotals($requestProducts);

                $order = Order::create([
                    'order_number'             => 'ORD-' . strtoupper(uniqid()),
                    'customer_id'              => $customerId,
                    'product_ids'              => $productIds,
                    'customer_name'            => $addressData['name'],
                    'customer_email'           => $request->email,
                    'email_news_offers'        => $request->boolean('email_news_offers', false),
                    'customer_phone'           => $addressData['phone'],
                    'shipping_address'         => $addressData['address_line1'] . ($addressData['address_line2'] ? ', ' . $addressData['address_line2'] : ''),
                    'city'                     => $addressData['city_name'],
                    'state'                    => $addressData['state_name'],
                    'country'                  => $addressData['country_name'],
                    'postal_code'              => $addressData['pincode'],
                    'billing_same_as_shipping' => $billingData['billing_same_as_shipping'],
                    'billing_address'          => $billingData['billing_address'],
                    'billing_city'             => $billingData['billing_city'],
                    'billing_state'            => $billingData['billing_state'],
                    'billing_country'          => $billingData['billing_country'],
                    'billing_postal_code'      => $billingData['billing_postal_code'],
                    'items_count'              => $requestProducts->sum('quantity'),
                    'subtotal'                 => $totals['subtotal'],
                    'tax'                      => $totals['tax'],
                    'shipping'                 => $totals['shipping'],
                    'total'                    => $totals['total'],
                    'payment_method'           => $request->payment_method,
                    'status'                   => 'pending',
                ]);

                foreach ($requestProducts as $item) {
                    $product = Product::find($item['product_id']);
                    OrderItem::create([
                        'order_id'    => $order->id,
                        'customer_id' => $customerId,
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'price'       => $product->price,
                        'total'       => $product->price * $item['quantity'],
                    ]);
                }

                $responseData = [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                    'amount'       => $totals['total'],
                    'currency'     => 'INR',
                ];

                if ($request->payment_method === 'razorpay') {
                    $paymentLink = $this->razorpay()->paymentLink->create([
                        'amount'          => (int) ($totals['total'] * 100),
                        'currency'        => 'INR',
                        'description'     => 'Order ' . $order->order_number,
                        'customer'        => [
                            'name'    => $addressData['name'],
                            'email'   => $request->email,
                            'contact' => $addressData['phone'],
                        ],
                        'notify'          => ['sms' => false, 'email' => false],
                        'reminder_enable' => false,
                        'notes'           => [
                            'order_id'     => (string) $order->id,
                            'order_number' => $order->order_number,
                        ],
                        'callback_url'    => url('/api/payment/callback'),
                        'callback_method' => 'get',
                    ]);

                    $order->update(['payment_reference' => $paymentLink->id]);

                    Payment::create([
                        'payment_id'        => 'PAY-PENDING-' . $order->id,
                        'order_id'          => $order->id,
                        'razorpay_order_id' => $paymentLink->id,
                        'method'            => 'razorpay',
                        'amount'            => $totals['total'],
                        'currency'          => 'INR',
                        'status'            => 'pending',
                    ]);

                    $responseData['payment_url'] = $paymentLink->short_url;
                }

                return $this->success($responseData, "Checkout initiated successfully");
            });
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    private function handleShippingAddress(CheckoutRequest $request): array
    {
        if ($request->address_id) {
            $address = CustomerAddress::with(['city', 'state', 'country'])->find($request->address_id);
            return [
                'name'          => $address->name,
                'phone'         => $address->phone,
                'address_line1' => $address->address_line1,
                'address_line2' => $address->address_line2,
                'city_name'     => $address->city->name,
                'state_name'    => $address->state->name,
                'country_name'  => $address->country->name,
                'pincode'       => $address->pincode,
            ];
        }

        if ($request->save_address) {
            CustomerAddress::create([
                'customer_id'   => $request->customer_id,
                'name'          => $request->name,
                'phone'         => $request->phone,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city_id'       => $request->city_id,
                'state_id'      => $request->state_id,
                'country_id'    => $request->country_id,
                'pincode'       => $request->pincode,
                'is_default'    => !CustomerAddress::where('customer_id', $request->customer_id)->exists(),
            ]);
        }

        $city    = City::find($request->city_id);
        $state   = State::find($request->state_id);
        $country = Country::find($request->country_id);

        return [
            'name'          => $request->name,
            'phone'         => $request->phone,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city_name'     => $city->name,
            'state_name'    => $state->name,
            'country_name'  => $country->name,
            'pincode'       => $request->pincode,
        ];
    }

    private function handleBillingAddress(CheckoutRequest $request, array $shippingData): array
    {
        $sameAsShipping = $request->boolean('billing_same_as_shipping', true);

        if ($sameAsShipping) {
            return [
                'billing_same_as_shipping' => true,
                'billing_address'          => $shippingData['address_line1'] . ($shippingData['address_line2'] ? ', ' . $shippingData['address_line2'] : ''),
                'billing_city'             => $shippingData['city_name'],
                'billing_state'            => $shippingData['state_name'],
                'billing_country'          => $shippingData['country_name'],
                'billing_postal_code'      => $shippingData['pincode'],
            ];
        }

        return [
            'billing_same_as_shipping' => false,
            'billing_address'          => $request->billing_address_line1 . ($request->billing_address_line2 ? ', ' . $request->billing_address_line2 : ''),
            'billing_city'             => $request->billing_city,
            'billing_state'            => $request->billing_state,
            'billing_country'          => $request->billing_country,
            'billing_postal_code'      => $request->billing_pincode,
        ];
    }

    private function calculateTotals($requestProducts): array
    {
        $subtotal = 0;
        foreach ($requestProducts as $item) {
            $product   = Product::find($item['product_id']);
            $subtotal += $product->price * $item['quantity'];
        }

        $tax      = $subtotal * 0.18;
        $shipping = $subtotal > 5000 ? 0 : 150;

        return [
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'shipping' => $shipping,
            'total'    => $subtotal + $tax + $shipping,
        ];
    }
}
