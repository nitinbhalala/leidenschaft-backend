<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Name is required.',
            'name.max'       => 'Name may not be greater than 100 characters.',
            'phone.max'      => 'Phone may not be greater than 20 characters.',
            'avatar.image'   => 'Avatar must be an image.',
            'avatar.mimes'   => 'Avatar must be a file of type: jpg, jpeg, png, gif, webp.',
            'avatar.max'     => 'Avatar may not be greater than 2MB.',
        ];
    }
}
