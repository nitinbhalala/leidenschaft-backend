<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InteriorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Section 1 - Interior
            'section_1_title'               => 'nullable|string|max:255',
            'section_1_subtitle'            => 'nullable|string',
            'section_1_side_text'           => 'nullable|string',
            'section_1_title_description'   => 'nullable|string',
            'section_1_subtitle_description' => 'nullable|string',
            'section_1_image'               => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_1_status'              => 'nullable|boolean',

            // Section 2 - Material-Led Design
            'section_2_title'               => 'nullable|string|max:255',
            'section_2_subtitle'            => 'nullable|string',
            'section_2_title_description'   => 'nullable|string',
            'section_2_subtitle_description' => 'nullable|string',
            'section_2_items'               => 'nullable',
            'section_2_process_steps'       => 'nullable',
            'section_2_image'               => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_2_status'              => 'nullable|boolean',

            // Section 3 - Gallery
            'section_3_title'               => 'nullable|string|max:255',
            'section_3_side_text'           => 'nullable|string',
            'section_3_description'         => 'nullable|string',
            'section_3_items'               => 'nullable',
            'section_3_status'              => 'nullable|boolean',

            // Section 4 - Showcase
            'section_4_items'               => 'nullable',
            'section_4_status'              => 'nullable|boolean',

            // Section 5 - Why Choose
            'section_5_title'               => 'nullable|string|max:255',
            'section_5_description'         => 'nullable|string',
            'section_5_points'              => 'nullable',
            'section_5_status'              => 'nullable|boolean',
        ];
    }
}
