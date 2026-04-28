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
        'product_ids',
        'customer_name',
        'customer_email',
        'email_news_offers',
        'customer_phone',
        'shipping_address',
        'city',
        'state',
        'country',
        'postal_code',
        'billing_same_as_shipping',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_postal_code',
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
        'product_ids' => 'array',
        'email_news_offers' => 'integer',
        'billing_same_as_shipping' => 'integer',
        'subtotal'   => 'decimal:2',
        'tax'        => 'decimal:2',
        'shipping'   => 'decimal:2',
        'total'      => 'decimal:2',
        'items_count' => 'integer',
        'placed_at'  => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
