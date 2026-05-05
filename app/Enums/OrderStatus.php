<?php

namespace App\Enums;

class OrderStatus
{
    const PENDING    = 'pending';
    const PROCESSING = 'processing';
    const SHIPPED    = 'shipped';
    const DELIVERED  = 'delivered';
    const CANCELLED  = 'cancelled';
    const FAILED     = 'failed';

    const PROGRESSION = [
        'pending',
        'processing',
        'shipped',
        'delivered'
    ];

    private static array $messages = [
        'pending'    => 'Your order has been placed. Payment is pending.',
        'processing' => 'Payment confirmed. Your order is now being processed.',
        'shipped'    => 'Your order has been shipped and is on its way.',
        'delivered'  => 'Your order has been delivered successfully.',
        'cancelled'  => 'Your order has been cancelled.',
        'failed'     => 'Order failed. Please try placing a new order.',
    ];

    public static function message(string $status): string
    {
        return self::$messages[$status] ?? ucfirst($status);
    }

    public static function progressionIndex(string $status): int|false
    {
        return array_search($status, self::PROGRESSION);
    }
}
