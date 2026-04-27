<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interior extends Model
{
    use HasFactory;

    protected $table = 'interior';

    protected $fillable = [
        'section_1_title',
        'section_1_subtitle',
        'section_1_side_text',
        'section_1_title_description',
        'section_1_subtitle_description',
        'section_1_image',
        'section_1_status',
        'section_2_title',
        'section_2_subtitle',
        'section_2_title_description',
        'section_2_subtitle_description',
        'section_2_items',
        'section_2_process_steps',
        'section_2_image',
        'section_2_status',
        'section_3_title',
        'section_3_side_text',
        'section_3_description',
        'section_3_items',
        'section_3_status',
        'section_4_items',
        'section_4_status',
        'section_5_title',
        'section_5_description',
        'section_5_points',
        'section_5_status',
    ];

    protected $casts = [
        'section_1_status'        => 'integer',
        'section_2_items'         => 'array',
        'section_2_process_steps' => 'array',
        'section_2_status'        => 'integer',
        'section_3_items'         => 'array',
        'section_3_status'        => 'integer',
        'section_4_items'        => 'array',
        'section_4_status'        => 'integer',
        'section_5_points'        => 'array',
        'section_5_status'        => 'integer',
    ];

    protected array $imageFields = [
        'section_1_image',
        'section_2_image',
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
