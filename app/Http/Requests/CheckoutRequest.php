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
            // ── Customer ──────────────────────────────────────────────────────
            'customer_id'       => 'required|exists:customers,id',
            'email'             => 'required|email',
            'email_news_offers' => 'nullable|integer|in:0,1',

            // ── Products ──────────────────────────────────────────────────────
            'products'                   => 'required|array|min:1',
            'products.*.product_id'      => 'required|integer|exists:products,id',
            'products.*.quantity'        => 'required|integer|min:1',

            // ── Shipping Address ──────────────────────────────────────────────
            // Either provide an existing saved address_id,
            // OR provide all individual address fields.
            'address_id'    => 'nullable|exists:customer_addresses,id',
            'name'          => 'required_without:address_id|string|max:255',
            'phone'         => 'required_without:address_id|string|max:20',
            'address_line1' => 'required_without:address_id|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city_id'       => 'required_without:address_id|exists:cities,id',
            'state_id'      => 'required_without:address_id|exists:states,id',
            'country_id'    => 'required_without:address_id|exists:countries,id',
            'pincode'       => 'required_without:address_id|string|max:10',
            'save_address'  => 'nullable|boolean',

            // ── Billing Address ───────────────────────────────────────────────
            // billing_same_as_shipping: true  → copy shipping address to billing
            // billing_same_as_shipping: false → use separate billing fields below
            'billing_same_as_shipping' => 'nullable|integer|in:0,1',

            // Required only when using a different billing address
            'billing_address_line1' => 'required_if:billing_same_as_shipping,0|nullable|string|max:255',
            'billing_address_line2' => 'nullable|string|max:255',
            'billing_city'          => 'required_if:billing_same_as_shipping,0|nullable|string|max:100',
            'billing_state'         => 'required_if:billing_same_as_shipping,0|nullable|string|max:100',
            'billing_country'       => 'required_if:billing_same_as_shipping,0|nullable|string|max:100',
            'billing_pincode'       => 'required_if:billing_same_as_shipping,0|nullable|string|max:10',

            // ── Payment ───────────────────────────────────────────────────────
            'payment_method' => 'required|string',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'customer_id.required'          => 'Customer ID is required.',
            'customer_id.exists'            => 'Customer not found.',
            'email.required'                => 'Email address is required.',
            'email.email'                   => 'Please provide a valid email address.',
            'products.required'             => 'At least one product is required.',
            'products.*.product_id.exists'  => 'One or more selected products do not exist.',
            'products.*.quantity.min'       => 'Product quantity must be at least 1.',
            'address_id.exists'             => 'Selected address not found.',
            'name.required_without'         => 'Full name is required when no saved address is selected.',
            'phone.required_without'        => 'Phone number is required when no saved address is selected.',
            'address_line1.required_without' => 'Address line 1 is required when no saved address is selected.',
            'city_id.required_without'      => 'City is required when no saved address is selected.',
            'state_id.required_without'     => 'State is required when no saved address is selected.',
            'country_id.required_without'   => 'Country is required when no saved address is selected.',
            'pincode.required_without'      => 'Pincode is required when no saved address is selected.',
            'billing_address_line1.required_if' => 'Billing address is required when using a different billing address.',
            'billing_city.required_if'          => 'Billing city is required when using a different billing address.',
            'billing_state.required_if'         => 'Billing state is required when using a different billing address.',
            'billing_country.required_if'       => 'Billing country is required when using a different billing address.',
            'billing_pincode.required_if'       => 'Billing pincode is required when using a different billing address.',
            'payment_method.required'           => 'Payment method is required.',
        ];
    }
}
