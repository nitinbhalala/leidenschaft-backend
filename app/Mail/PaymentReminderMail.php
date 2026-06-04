<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $retryUrl;

    public function __construct(
        public Order $order,
        public $products,
        public int $reminderDay
    ) {
        // Signed URL valid for 7 days — safe to click even on day-4 email
        $this->retryUrl = URL::temporarySignedRoute(
            'payment.retry',
            now()->addDays(7),
            ['order_number' => $order->order_number]
        );
    }

    public function envelope(): Envelope
    {
        $subjects = [
            1 => "You left something behind — Complete your order ({$this->order->order_number})",
            2 => "Your selection is still waiting — {$this->order->order_number}",
            3 => "Don't miss out — Your order is reserved ({$this->order->order_number})",
            4 => "Final reminder — Complete your Leidenschaft order ({$this->order->order_number})",
        ];

        return new Envelope(
            subject: $subjects[$this->reminderDay] ?? $subjects[1],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
        );
    }
}
