<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RetryPaymentController extends Controller
{
    private function razorpay(): Api
    {
        $key    = getSetting('razorpay_key_id');
        $secret = getSetting('razorpay_key_secret');

        if (empty($key) || empty($secret)) {
            abort(503, 'Payment gateway is not configured.');
        }

        return new Api($key, $secret);
    }

    public function redirect(Request $request)
    {
        $frontendUrl = env('FRONTEND_URL', config('app.url'));

        $order = Order::where('order_number', $request->query('order_number'))
            ->with(['payment', 'items.product'])
            ->first();

        if (!$order) {
            return redirect($frontendUrl . '/payment/failed?reason=order_not_found');
        }

        $payment = $order->payment;

        if (!$payment) {
            return redirect($frontendUrl . '/payment/failed?reason=payment_not_found');
        }

        // Already paid — send to success
        if (in_array($payment->status, ['completed', 'captured'])) {
            return redirect($frontendUrl . '/payment/success?order_id=' . $order->id);
        }

        try {
            $paymentLink = $this->razorpay()->paymentLink->create([
                'amount'          => (int) ($order->total * 100),
                'currency'        => 'INR',
                'description'     => 'Order ' . $order->order_number . ' (Retry)',
                'customer'        => [
                    'name'    => $order->customer_name,
                    'email'   => $order->customer_email,
                    'contact' => $order->customer_phone ?? '',
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

            $payment->update([
                'razorpay_order_id' => $paymentLink->id,
                'payment_id'        => 'PAY-RETRY-' . $order->id . '-' . time(),
                'status'            => 'pending',
            ]);

            $order->update([
                'payment_reference' => $paymentLink->id,
                'status'            => OrderStatus::PENDING,
            ]);

            return redirect($paymentLink->short_url);
        } catch (\Exception $e) {
            return redirect($frontendUrl . '/payment/failed?reason=gateway_error');
        }
    }
}
