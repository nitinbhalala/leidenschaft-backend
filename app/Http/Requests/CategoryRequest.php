<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('id');

        return [
            'parent_id' => 'nullable|exists:categories,id',
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'page' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'description' => 'nullable|string',
            'show_in_navbar' => 'nullable|integer|min:0|max:1',
            'status' => 'nullable|integer|min:0|max:1',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'parent_id.exists' => 'Selected parent category is invalid.',
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 191 characters.',
            'name.unique' => 'This category name already exists.',
            'page.required' => 'Name is required.',
            'page.string' => 'Name must be a string.',
            'image.image' => 'Image must be an image file.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, webp.',
            'image.max' => 'Image may not be greater than 5MB.',
            'description.string' => 'Description must be a string.',
            'status.integer' => 'Status must be a number.',
            'status.min' => 'Status must be at least 0.',
            'status.max' => 'Status may not be greater than 1.',
            'show_in_navbar.integer' => 'Show in navbar must be a number.',
            'show_in_navbar.min' => 'Show in navbar must be at least 0.',
            'show_in_navbar.max' => 'Show in navbar may not be greater than 1.',
        ];
    }
}
