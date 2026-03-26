<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'views' => 'nullable|integer|min:0',
            'excerpt' => 'nullable|string',
            'content' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title may not be greater than 255 characters.',
            'author.required' => 'Author is required.',
            'author.string' => 'Author must be a string.',
            'author.max' => 'Author may not be greater than 255 characters.',
            'category_id.exists' => 'Selected category is invalid.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be draft, published, or scheduled.',
            'views.integer' => 'Views must be a number.',
            'views.min' => 'Views must be at least 0.',
            'excerpt.string' => 'Excerpt must be a string.',
            'content.required' => 'Content is required.',
            'content.string' => 'Content must be a string.',
        ];
    }
}
