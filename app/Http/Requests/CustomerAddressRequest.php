<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerAddressRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line1' => 'required|string',
            'address_line2' => 'nullable|string',
            'city' => 'required|exists:cities,id',
            'state' => 'required|exists:states,id',
            'country' => 'required|exists:countries,id',
            'pincode' => 'required|string|max:10',
            'is_default' => 'nullable|boolean',
            'type' => 'required|in:home,office'
        ];
    }
}
