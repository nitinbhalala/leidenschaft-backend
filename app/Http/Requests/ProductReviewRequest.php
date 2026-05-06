<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductReviewRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'customer_id' => [
                'required',
                'exists:customers,id',
                Rule::unique('product_reviews')
                    ->where('product_id', $this->input('product_id')),
            ],
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Selected product is invalid.',
            'customer_id.unique' => 'You have already reviewed this product.',
            'customer_id.required' => 'Customer is required.',
            'customer_id.exists' => 'Selected customer is invalid.',
            'rating.required' => 'Rating is required.',
            'rating.integer' => 'Rating must be a number.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating may not be greater than 5.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title may not be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
        ];
    }
}
