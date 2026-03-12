<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'category_id',
        'status',
        'excerpt',
        'content',
        'views'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
