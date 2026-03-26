<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'key' => 'required|string|max:255',
            'value' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Key is required.',
            'key.string' => 'Key must be a string.',
            'key.max' => 'Key may not be greater than 255 characters.',
            'value.string' => 'Value must be a string.',
        ];
    }
}
