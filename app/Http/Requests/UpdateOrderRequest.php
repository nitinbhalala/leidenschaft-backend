<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'items_count' => 'sometimes|integer|min:1',
            'subtotal' => 'sometimes|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'total' => 'sometimes|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled,failed'
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.string' => 'Customer name must be a string.',
            'customer_name.max' => 'Customer name may not be greater than 255 characters.',
            'customer_email.email' => 'Customer email must be a valid email address.',
            'customer_email.max' => 'Customer email may not be greater than 255 characters.',
            'customer_phone.string' => 'Customer phone must be a string.',
            'customer_phone.max' => 'Customer phone may not be greater than 20 characters.',
            'shipping_address.string' => 'Shipping address must be a string.',
            'city.string' => 'City must be a string.',
            'city.max' => 'City may not be greater than 100 characters.',
            'state.string' => 'State must be a string.',
            'state.max' => 'State may not be greater than 100 characters.',
            'country.string' => 'Country must be a string.',
            'country.max' => 'Country may not be greater than 100 characters.',
            'postal_code.string' => 'Postal code must be a string.',
            'postal_code.max' => 'Postal code may not be greater than 20 characters.',
            'items_count.integer' => 'Items count must be a number.',
            'items_count.min' => 'Items count must be at least 1.',
            'subtotal.numeric' => 'Subtotal must be a number.',
            'subtotal.min' => 'Subtotal must be at least 0.',
            'tax.numeric' => 'Tax must be a number.',
            'tax.min' => 'Tax must be at least 0.',
            'shipping.numeric' => 'Shipping must be a number.',
            'shipping.min' => 'Shipping must be at least 0.',
            'total.numeric' => 'Total must be a number.',
            'total.min' => 'Total must be at least 0.',
            'payment_method.string' => 'Payment method must be a string.',
            'payment_method.max' => 'Payment method may not be greater than 100 characters.',
            'status.in' => 'Status must be pending, processing, shipped, delivered, cancelled, or failed.',
        ];
    }
}
