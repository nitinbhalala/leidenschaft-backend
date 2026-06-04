<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminderEmails extends Command
{
    protected $signature   = 'reminders:payment';
    protected $description = 'Send daily payment reminder emails for failed/pending payments (max 4 days)';

    public function handle(): int
    {
        $payments = Payment::query()
            ->whereNotIn('status', ['captured', 'completed'])
            ->whereNull('reminder_completed_at')
            ->where('reminder_sent_count', '<', 4)
            ->where(function ($q) {
                $q->whereNull('last_reminder_sent_at')
                    ->orWhere('last_reminder_sent_at', '<=', now()->subHours(23));
            })
            ->where('created_at', '<=', now()->subHours(2))
            ->with('order.items.product.images')
            ->get();

        $sent = 0;

        foreach ($payments as $payment) {
            $order = $payment->order;

            if (!$order || !$order->customer_email) {
                continue;
            }

            $products = $order->items
                ->filter(fn($item) => $item->product !== null)
                ->map(fn($item) => (object) [
                    'name'     => $item->product->name,
                    'price'    => $item->price,
                    'quantity' => $item->quantity,
                    'image'    => $this->resolveImageUrl($item->product->images->first()?->image),
                ]);

            if ($products->isEmpty()) {
                $productIds = $order->product_ids ?? [];
                $dbProducts = Product::with('images')
                    ->whereIn('id', $productIds)
                    ->get();

                $products = $dbProducts->map(fn($p) => (object) [
                    'name'     => $p->name,
                    'price'    => $p->price,
                    'quantity' => 1,
                    'image'    => $this->resolveImageUrl($p->images->first()?->image),
                ]);
            }

            $reminderDay = $payment->reminder_sent_count + 1;

            try {
                Mail::to($order->customer_email)
                    ->send(new PaymentReminderMail($order, $products, $reminderDay));

                $newCount = $payment->reminder_sent_count + 1;

                $payment->update([
                    'reminder_sent_count'   => $newCount,
                    'last_reminder_sent_at' => now(),
                    'reminder_completed_at' => $newCount >= 4 ? now() : null,
                ]);

                $sent++;
                $this->info("Sent reminder {$reminderDay}/4 → {$order->customer_email} (Order: {$order->order_number})");
            } catch (\Exception $e) {
                $this->error("Failed for {$order->order_number}: {$e->getMessage()}");
            }
        }

        $this->info("Done. {$sent} reminder email(s) sent.");

        return self::SUCCESS;
    }

    private function resolveImageUrl(?string $image): ?string
    {
        if (!$image) return null;

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $clean = ltrim(preg_replace('#^(storage/|public/)#', '', $image), '/');

        return config('app.url') . '/storage/' . $clean;
    }
}
