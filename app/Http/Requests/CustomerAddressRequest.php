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

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required.',
            'customer_id.exists' => 'Selected customer is invalid.',
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'phone.required' => 'Phone is required.',
            'phone.string' => 'Phone must be a string.',
            'phone.max' => 'Phone may not be greater than 15 characters.',
            'address_line1.required' => 'Address line 1 is required.',
            'address_line1.string' => 'Address line 1 must be a string.',
            'address_line2.string' => 'Address line 2 must be a string.',
            'city.required' => 'City is required.',
            'city.exists' => 'Selected city is invalid.',
            'state.required' => 'State is required.',
            'state.exists' => 'Selected state is invalid.',
            'country.required' => 'Country is required.',
            'country.exists' => 'Selected country is invalid.',
            'pincode.required' => 'Pincode is required.',
            'pincode.string' => 'Pincode must be a string.',
            'pincode.max' => 'Pincode may not be greater than 10 characters.',
            'is_default.boolean' => 'Default field must be true or false.',
            'type.required' => 'Type is required.',
            'type.in' => 'Type must be home or office.',
        ];
    }
}
