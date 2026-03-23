<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'city',
        'state',
        'country',
        'postal_code',
        'items_count',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'payment_method',
        'payment_id',
        'payment_reference',
        'status',
        'placed_at',
    ];

    protected $casts = [
        'subtotal'   => 'decimal:2',
        'tax'        => 'decimal:2',
        'shipping'   => 'decimal:2',
        'total'      => 'decimal:2',
        'items_count' => 'integer',
        'placed_at'  => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
