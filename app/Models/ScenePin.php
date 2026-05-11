<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScenePin extends Model
{
    use HasFactory;

    protected $fillable = [
        'scene_id',
        'product_id',
        'x_percent',
        'y_percent',
        'sort_order'
    ];

    protected $casts = [
        'x_percent' => 'float',
        'y_percent' => 'float',
    ];

    public function scene()
    {
        return $this->belongsTo(Scene::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
