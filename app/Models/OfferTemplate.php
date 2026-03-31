<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
