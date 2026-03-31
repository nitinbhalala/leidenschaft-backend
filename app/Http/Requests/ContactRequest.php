<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'contact_method' => 'nullable|array',
            'contact_method.*' => 'in:email,phone,sms',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:1000',
            'message' => 'required|string|max:2000'
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'first_name.max' => 'First name may not be greater than 255 characters.',
            'last_name.string' => 'Last name must be a string.',
            'last_name.max' => 'Last name may not be greater than 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'contact_method' => 'nullable|array',
            'contact_method.*' => 'in:email,phone,sms',
            'phone.string' => 'Phone must be a string.',
            'phone.max' => 'Phone may not be greater than 20 characters.',
            'subject.required' => 'Subject is required.',
            'subject.string' => 'Subject must be a string.',
            'subject.max' => 'Subject may not be greater than 1000 characters.',
            'message.required' => 'Comment is required.',
            'message.string' => 'Comment must be a string.',
            'message.max' => 'Comment may not be greater than 2000 characters.',
        ];
    }
}
