<?php

namespace App\Models;

use App\Models\ScenePin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scene extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_url',
        'status'
    ];

    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($data['image_url'])) {
            $data['image_url'] = asset('public/storage/' . $data['image_url']);
        }

        return $data;
    }

    public function pins()
    {
        return $this->hasMany(ScenePin::class)->orderBy('sort_order');
    }

    public function pinsWithProducts()
    {
        return $this->pins()->with('product');
    }
}
