<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $customerId = $this->route('customer');

        if ($this->isMethod('POST')) {
            return [
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
                'email'      => 'required|email|unique:users,email',
                'phone'      => 'nullable|string|max:20',
                'avatar'     => 'nullable|string|max:255',
                'password'   => 'required|string|min:8',
            ];
        }

        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:users,email,' . $customerId,
            'phone'      => 'nullable|string|max:20',
            'avatar'     => 'nullable|string|max:255',
            'password'   => 'sometimes|string|min:8',
        ];
    }
}
