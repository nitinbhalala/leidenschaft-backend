<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'order_id',
        'razorpay_order_id',
        'method',
        'amount',
        'currency',
        'status',
        'refund_id',
        'refund_amount',
        'refunded_at',
        'meta',
        'reminder_sent_count',
        'last_reminder_sent_at',
        'reminder_completed_at',
    ];

    protected $casts = [
        'meta'                  => 'array',
        'reminder_sent_count'   => 'integer',
        'last_reminder_sent_at' => 'datetime',
        'reminder_completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
