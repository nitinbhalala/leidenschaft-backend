<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'token',
        'token_expires_at',
        'refresh_token',
        'refresh_token_expires_at',
        'password_reset_token',
        'password_reset_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'refresh_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAvatarAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
