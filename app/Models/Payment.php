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
        'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];
}
