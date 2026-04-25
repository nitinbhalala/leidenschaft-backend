<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Section 1 - Leidenschaft
            |--------------------------------------------------------------------------
            */
            'section_1_title' => 'nullable|string|max:255',
            'section_1_description' => 'nullable|string',
            'section_1_bt_text' => 'nullable|string|max:255',
            'section_1_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_1_slider_text' => 'nullable|string',
            'section_1_status' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | Section 2 - Best Selling
            |--------------------------------------------------------------------------
            */
            'section_2_title' => 'nullable|string|max:255',
            'section_2_subtitle' => 'nullable|string',
            'section_2_bt_text' => 'nullable|string|max:255',
            'section_2_second_title' => 'nullable|string|max:255',
            'section_2_second_subtitle' => 'nullable|string',
            'section_2_status' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | Section 3 - Explore Categories
            |--------------------------------------------------------------------------
            */
            'section_3_title' => 'nullable|string|max:255',
            'section_3_subtitle' => 'nullable|string|max:255',
            'section_3_description' => 'nullable|string',
            'section_3_status' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | Section 4 - Our Creations
            |--------------------------------------------------------------------------
            */
            'section_4_title' => 'nullable|string|max:255',
            'section_4_subtitle' => 'nullable|string|max:255',
            'section_4_status' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | Section 5 - Beauty in Imperfection
            |--------------------------------------------------------------------------
            */
            'section_5_title' => 'nullable|string|max:255',
            'section_5_description' => 'nullable|string',
            'section_5_status' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | Section 6 - Series & Collections
            |--------------------------------------------------------------------------
            */
            'section_6_title' => 'nullable|string|max:255',
            'section_6_subtitle' => 'nullable|string',
            'section_6_description' => 'nullable|string',
            'section_6_status' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | Section 7 - Custom Design
            |--------------------------------------------------------------------------
            */
            'section_7_title' => 'nullable|string|max:255',
            'section_7_subtitle' => 'nullable|string',
            'section_7_description' => 'nullable|string',
            'section_7_bt_text' => 'nullable|string|max:255',
            'section_7_img_1' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_7_img_2' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_7_img_3' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_7_img_4' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_7_status' => 'nullable|boolean',

            /*
            |--------------------------------------------------------------------------
            | Section 8 - About Us
            |--------------------------------------------------------------------------
            */
            'section_8_title' => 'nullable|string|max:255',
            'section_8_description' => 'nullable|string',
            'section_8_img_1' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_8_img_2' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_8_status' => 'nullable|boolean',
        ];
    }
}
