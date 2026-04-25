<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EssenceRequest extends FormRequest
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
            'section_1_title' => 'nullable|string|max:255',
            'section_1_description' => 'nullable|string',
            'section_1_subtitle' => 'nullable|string',
            'section_1_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_1_status' => 'nullable|boolean',
            'section_2_title' => 'nullable|string|max:255',
            'section_2_description' => 'nullable|string',
            'section_2_subtitle' => 'nullable|string',
            'section_2_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_2_status' => 'nullable|boolean',
            'section_3_title' => 'nullable|string|max:255',
            'section_3_description' => 'nullable|string',
            'section_3_subtitle' => 'nullable|string',
            'section_3_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'section_3_status' => 'nullable|boolean',
        ];
    }
}
