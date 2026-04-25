<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'email' => 'required|email',
            'address_id' => 'nullable|exists:customer_addresses,id',
            
            // New Address fields (required if address_id is null)
            'name' => 'required_without:address_id|string|max:255',
            'phone' => 'required_without:address_id|string|max:20',
            'address_line1' => 'required_without:address_id|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city_id' => 'required_without:address_id|exists:cities,id',
            'state_id' => 'required_without:address_id|exists:states,id',
            'country_id' => 'required_without:address_id|exists:countries,id',
            'pincode' => 'required_without:address_id|string|max:10',
            
            'save_address' => 'nullable|boolean',
            'payment_method' => 'required|string',
        ];
    }
}
