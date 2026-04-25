<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Essence extends Model
{
    use HasFactory;

    protected $table = 'essence';

    protected $fillable = [
        'section_1_title',
        'section_1_description',
        'section_1_subtitle',
        'section_1_image',
        'section_1_status',
        'section_2_title',
        'section_2_description',
        'section_2_subtitle',
        'section_2_image',
        'section_2_status',
        'section_3_title',
        'section_3_description',
        'section_3_subtitle',
        'section_3_image',
        'section_3_status',
    ];

    protected $casts = [
        'section_1_status' => 'integer',
        'section_2_status' => 'integer',
        'section_3_status' => 'integer',
    ];

    protected array $imageFields = [
        'section_1_image',
        'section_2_image',
        'section_3_image',
    ];

    public function toArray(): array
    {
        $data = parent::toArray();

        foreach ($this->imageFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = asset('storage/' . $data[$field]);
            }
        }

        return $data;
    }
}
