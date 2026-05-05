<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        // Section 1 - Leidenschaft
        'section_1_title',
        'section_1_description',
        'section_1_bt_text',
        'section_1_image',
        'section_1_slider_text',
        'section_1_status',

        // Section 2 - Best Selling
        'section_2_title',
        'section_2_subtitle',
        'section_2_bt_text',
        'section_2_second_title',
        'section_2_second_subtitle',
        'section_2_status',

        // Section 3 - Explore Categories
        'section_3_title',
        'section_3_subtitle',
        'section_3_description',
        'section_3_status',

        // Section 4 - Our Creations
        'section_4_title',
        'section_4_subtitle',
        'section_4_status',

        // Section 5 - Beauty in Imperfection
        'section_5_title',
        'section_5_description',
        'section_5_status',

        // Section 6 - Series & Collections
        'section_6_title',
        'section_6_subtitle',
        'section_6_description',
        'section_6_status',

        // Section 7 - Custom Design
        'section_7_title',
        'section_7_subtitle',
        'section_7_description',
        'section_7_bt_text',
        'section_7_img_1',
        'section_7_img_2',
        'section_7_img_3',
        'section_7_img_4',
        'section_7_status',

        // Section 8 - About Us
        'section_8_title',
        'section_8_description',
        'section_8_img_1',
        'section_8_img_2',
        'section_8_status',

        // Section 9 - New Section
        'section_9_title',
        'section_9_subtitle',
        'section_9_status',
    ];

    protected $casts = [
        'section_1_status' => 'integer',
        'section_2_status' => 'integer',
        'section_3_status' => 'integer',
        'section_4_status' => 'integer',
        'section_5_status' => 'integer',
        'section_6_status' => 'integer',
        'section_7_status' => 'integer',
        'section_8_status' => 'integer',
        'section_9_status' => 'integer',
    ];

    protected array $imageFields = [
        'section_1_image',
        'section_7_img_1',
        'section_7_img_2',
        'section_7_img_3',
        'section_7_img_4',
        'section_8_img_1',
        'section_8_img_2',
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
