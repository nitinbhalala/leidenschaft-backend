<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentRequest;
use App\Http\Requests\RefundRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class PaymentController extends BaseController
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            getSetting('razorpay_key'),
            getSetting('razorpay_secret')
        );
    }

    public function createOrder(PaymentRequest $request)
    {
        $data = $request->validated();

        $order = $this->razorpay->order->create([
            'amount' => $data['amount'] * 100,
            'currency' => 'INR',
            'receipt' => uniqid(),
        ]);

        return $this->success($order, "Order created");
    }

    public function verify(Request $request)
    {
        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            $payment = Payment::create([
                'payment_id' => $request->razorpay_payment_id,
                'order_id' => $request->order_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'amount' => $request->amount,
                'status' => 'completed',
                'meta' => $request->all()
            ]);

            return $this->success($payment, "Payment successful");
        } catch (\Exception $e) {
            return $this->error("Payment verification failed", 400);
        }
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

            $refund = $this->razorpay->payment
                ->fetch($payment->payment_id)
                ->refund([
                    'amount' => $refundAmount * 100
                ]);

            $payment->update([
                'status' => 'refunded',
                'refund_id' => $refund->id,
                'refund_amount' => $refundAmount,
                'refunded_at' => now(),
            ]);

            return $this->success($payment, "Refund processed successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function index()
    {
        return $this->success(Payment::latest()->get(), "Payments list");
    }

    public function show($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error("Not found", 404);
        }

        return $this->success($payment);
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->error("Not found", 404);
        }

        $payment->delete();

        return $this->success(null, "Deleted");
    }
}
