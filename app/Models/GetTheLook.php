<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetTheLook extends Model
{
    use HasFactory;

    protected $table = 'get_the_look';

    protected $fillable = [
        'image',
        'product_ids',
        'position',
        'status',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'position'    => 'array',
        'status'      => 'integer',
    ];

    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($data['image'])) {
            $data['image'] = asset('storage/' . $data['image']);
        }

        return $data;
    }
}
