<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTheLookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('id') !== null;

        return [
            'image'         => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' : 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'product_ids'   => $isUpdate ? 'nullable|array|min:1' : 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'position'      => [$isUpdate ? 'nullable' : 'required', 'array', 'min:1', function ($attribute, $value, $fail) {
                if ($value === null) return;
                $positions = array_values($value);
                if (count($positions) !== count(array_unique($positions))) {
                    $fail('Each position label must be unique. Duplicate positions are not allowed.');
                }
            }],
            'status'        => 'nullable|integer|min:0|max:1',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'          => 'Image is required.',
            'image.image'             => 'The file must be an image.',
            'image.mimes'             => 'Image must be jpg, jpeg, png, or webp format.',
            'image.max'               => 'Image may not be greater than 5MB.',
            'product_ids.required'    => 'Product IDs are required.',
            'product_ids.array'       => 'Product IDs must be an array.',
            'product_ids.min'         => 'At least one product ID is required.',
            'product_ids.*.integer'   => 'Each product ID must be a number.',
            'product_ids.*.exists'    => 'One or more selected products are invalid.',
            'position.required'       => 'Position is required.',
            'position.array'          => 'Position must be an object/array.',
            'position.min'            => 'At least one position entry is required.',
            'status.integer'          => 'Status must be 0 or 1.',
            'status.min'              => 'Status must be at least 0.',
            'status.max'              => 'Status may not be greater than 1.',
        ];
    }
}
