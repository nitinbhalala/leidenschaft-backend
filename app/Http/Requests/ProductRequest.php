<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $productId = $this->route('id');

        return [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($productId),
            ],
            'sku'  => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'delivery_returns' => 'nullable|string',
            'material_dimensions' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|min:0|max:1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_image_ids' => 'nullable|array',
            'delete_image_ids.*' => 'integer|exists:product_images,id',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category is invalid.',
            'sub_category_id.exists' => 'Selected sub category is invalid.',
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'name.unique' => 'This product name already exists.',
            'sku.string' => 'SKU must be a string.',
            'sku.max' => 'SKU may not be greater than 100 characters.',
            'description.string' => 'Description must be a string.',
            'delivery_returns.string' => 'Delivery & returns must be a string.',
            'material_dimensions.string' => 'Material & dimensions must be a string.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'stock.integer' => 'Stock must be a number.',
            'stock.min' => 'Stock must be at least 0.',
            'status.integer' => 'Status must be a number.',
            'status.min' => 'Status must be at least 0.',
            'status.max' => 'Status may not be greater than 1.',
            'images.array' => 'Images must be an array.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be jpg, jpeg, png, or webp format.',
            'images.*.max' => 'Each image may not be greater than 2MB.',
            'delete_image_ids.array' => 'Delete image ids must be an array.',
            'delete_image_ids.*.integer' => 'Each delete image id must be a number.',
            'delete_image_ids.*.exists' => 'Selected image to delete is invalid.',
        ];
    }
}
