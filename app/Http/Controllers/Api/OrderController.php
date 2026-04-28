<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function customerOrders(Request $request)
    {
        $customer = $request->attributes->get('customer');

        $query = Order::with([
            'items.product:id,name,slug,price',
            'items.product.images',
            'payment:id,order_id,payment_id,method,amount,status',
        ])
            ->where('customer_id', $customer->id)
            ->latest();

        $status = $request->input('status', 'all');

        if ($status === 'active_order') {
            $query->whereIn('status', ['pending', 'processing', 'shipped']);
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate($request->input('per_page', 10));

        return $this->success($orders, "Orders fetched successfully");
    }

    public function customerShow(Request $request, $id)
    {
        $customer = $request->attributes->get('customer');

        $order = Order::with([
            'items.product:id,name,slug,price',
            'items.product.images',
            'payment',
        ])
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        return $this->success($order, "Order fetched successfully");
    }

    public function cancel(Request $request, $id)
    {
        $customer = $request->attributes->get('customer');

        $order = Order::with('payment')->where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return $this->error("Order cannot be cancelled once it has been shipped", 422);
        }

        $previousStatus = $order->status;
        $order->update(['status' => 'cancelled']);

        $refundStatus = null;

        // If payment was already made, trigger Razorpay refund
        if ($previousStatus === 'processing' && $order->payment && $order->payment->status === 'completed') {
            try {
                $key    = getSetting('razorpay_key_id');
                $secret = getSetting('razorpay_key_secret');

                if ($key && $secret) {
                    $razorpay = new \Razorpay\Api\Api($key, $secret);

                    $refund = $razorpay->payment
                        ->fetch($order->payment->payment_id)
                        ->refund(['amount' => (int) ($order->payment->amount * 100)]);

                    $order->payment->update([
                        'status'        => 'refunded',
                        'refund_id'     => $refund->id,
                        'refund_amount' => $order->payment->amount,
                        'refunded_at'   => now(),
                    ]);

                    $refundStatus = 'initiated';
                }
            } catch (\Exception $e) {
                $refundStatus = 'failed';
            }
        }

        $response = ['order_id' => $order->id, 'status' => 'cancelled'];

        if ($refundStatus) {
            $response['refund'] = $refundStatus === 'initiated'
                ? 'Refund initiated successfully'
                : 'Order cancelled but refund failed. Please contact support.';
        }

        return $this->success($response, "Order cancelled successfully");
    }

    public function index(Request $request)
    {
        $query = Order::with([
            'items.product:id,name,slug,price',
            'payment:id,order_id,payment_id,method,amount,status',
        ])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->paginate($request->input('per_page', 15));

        return $this->success($orders, "Orders fetched successfully");
    }

    public function show($id)
    {
        $order = Order::with([
            'items.product:id,name,slug,price',
            'items.product.images',
            'payment',
        ])
            ->find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        return $this->success($order, "Order fetched successfully");
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::with('payment')->find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        $previousStatus = $order->status;
        $order->update($request->validated());

        $refundStatus = null;

        // Trigger refund if admin is cancelling a paid order
        if (
            $request->input('status') === 'cancelled' &&
            $previousStatus !== 'cancelled' &&
            $order->payment &&
            $order->payment->status === 'completed'
        ) {
            try {
                $key    = getSetting('razorpay_key_id');
                $secret = getSetting('razorpay_key_secret');

                if ($key && $secret) {
                    $razorpay = new \Razorpay\Api\Api($key, $secret);

                    $refund = $razorpay->payment
                        ->fetch($order->payment->payment_id)
                        ->refund(['amount' => (int) ($order->payment->amount * 100)]);

                    $order->payment->update([
                        'status'        => 'refunded',
                        'refund_id'     => $refund->id,
                        'refund_amount' => $order->payment->amount,
                        'refunded_at'   => now(),
                    ]);

                    $refundStatus = 'initiated';
                }
            } catch (\Exception $e) {
                $refundStatus = 'failed';
            }
        }

        $response = Order::with([
                'items.product:id,name,slug,price',
                'items.product.images',
                'payment',
            ])->find($id);

        if ($refundStatus === 'initiated') {
            return $this->success($response, "Order cancelled and refund initiated successfully");
        }

        if ($refundStatus === 'failed') {
            return $this->success($response, "Order cancelled but refund failed. Please process manually.");
        }

        return $this->success($response, "Order updated successfully");
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        $order->delete();

        return $this->success(null, "Order deleted successfully");
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $data                  = $request->validated();
            $data['order_number']  = 'ORD-' . strtoupper(uniqid());
            $data['placed_at']     = now();

            $order = Order::create($data);

            return $this->success($order, "Order created successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
