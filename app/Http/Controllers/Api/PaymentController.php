<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Requests\RefundRequest;
use App\Mail\OrderInvoiceMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class PaymentController extends BaseController
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

    public function callback(Request $request)
    {
        $paymentLinkId = $request->query('razorpay_payment_link_id');
        $paymentId     = $request->query('razorpay_payment_id');
        $referenceId   = $request->query('razorpay_payment_link_reference_id');
        $status        = $request->query('razorpay_payment_link_status');
        $signature     = $request->query('razorpay_signature');

        $frontendUrl = env('FRONTEND_URL', config('app.url'));

        if ($status !== 'paid') {
            return redirect($frontendUrl . '/payment/failed');
        }

        try {
            $secret    = getSetting('razorpay_key_secret');
            $payload   = $paymentLinkId . '|' . $referenceId . '|' . $status . '|' . $paymentId;
            $generated = hash_hmac('sha256', $payload, $secret);

            if (!hash_equals($generated, (string) $signature)) {
                return redirect($frontendUrl . '/payment/failed');
            }
        } catch (\Exception $e) {
            return redirect($frontendUrl . '/payment/failed');
        }

        DB::transaction(function () use ($paymentLinkId, $paymentId) {
            $payment = Payment::where('razorpay_order_id', $paymentLinkId)->first();

            if (!$payment) {
                return;
            }

            $payment->update([
                'payment_id'            => $paymentId,
                'status'                => 'completed',
                'reminder_completed_at' => now(),
            ]);

            $order = Order::find($payment->order_id);
            if ($order) {
                $order->update([
                    'payment_id' => $paymentId,
                    'status'     => OrderStatus::PROCESSING,
                    'placed_at'  => now(),
                ]);
                $order->appendOrderStatus(OrderStatus::PROCESSING);

                Cart::where('customer_id', $order->customer_id)
                    ->whereIn('product_id', $order->product_ids ?? [])
                    ->delete();
            }
        });

        $payment = Payment::where('razorpay_order_id', $paymentLinkId)->first();

        if ($payment && $payment->order_id) {
            try {
                $order = Order::with([
                    'items.product:id,name,sku,slug,price',
                    'items.product.images',
                    'payment',
                ])->find($payment->order_id);

                if ($order && $order->customer_email) {
                    Mail::to($order->customer_email)
                        ->send(new OrderInvoiceMail($order));
                }
            } catch (\Exception $e) {
                // Invoice email failure should not block redirect
            }
        }

        return redirect($frontendUrl . '/payment/success?order_id=' . ($payment?->order_id ?? ''));
    }

    public function refund(RefundRequest $request, $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error("Payment not found", 404);
        }

        if ($payment->status !== 'completed') {
            return $this->error("Only completed payments can be refunded");
        }

        try {
            $refundAmount = $request->amount ?? $payment->amount;

            $refund = $this->razorpay()->payment
                ->fetch($payment->payment_id)
                ->refund(['amount' => (int) ($refundAmount * 100)]);

            $payment->update([
                'status'        => 'refunded',
                'refund_id'     => $refund->id,
                'refund_amount' => $refundAmount,
                'refunded_at'   => now(),
            ]);

            return $this->success($payment, "Refund processed successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function index(Request $request)
    {
        $payments = Payment::with([
            'order:id,order_number,customer_id,customer_name,customer_email,customer_phone,total,status,placed_at',
            'order.customer:id,name,email,phone',
        ])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->success($payments, "Payments list fetched successfully");
    }

    public function show($id)
    {
        $payment = Payment::with([
            'order:id,order_number,customer_id,customer_name,customer_email,customer_phone,shipping_address,city,state,country,postal_code,subtotal,tax,shipping,total,payment_method,status,placed_at',
            'order.customer:id,name,email,phone',
            'order.items.product:id,name,slug,price',
            'order.items.product.images',
        ])
            ->find($id);

        if (!$payment) {
            return $this->error("Not found", 404);
        }

        return $this->success($payment, "Payment details fetched successfully");
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error("Not found", 404);
        }

        $payment->delete();

        return $this->success(null, "Payment deleted successfully.");
    }
}
