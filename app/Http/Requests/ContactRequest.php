<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'contact_method' => 'nullable|in:email,phone,sms',
            'phone' => 'nullable|string|max:20',
            'comment' => 'required|string|max:2000'
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'comment.required' => 'Comment is required'
        ];
    }
}
