<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Customer ──────────────────────────────────────────────────────
            'customer_id'       => 'nullable|exists:customers,id',
            'email'             => 'required|email',
            'email_news_offers' => 'nullable|integer|in:0,1',

            // ── Products ──────────────────────────────────────────────────────
            'products'                  => 'required|array|min:1',
            'products.*.product_id'     => 'required|integer|exists:products,id',
            'products.*.quantity'       => 'required|integer|min:1',

            // ── Shipping Address ──────────────────────────────────────────────
            // Either provide an existing saved address_id (only for logged-in customers),
            // OR provide all fields inside shipping_address object.
            'address_id'                        => 'nullable|exists:customer_addresses,id',
            'shipping_address'                  => 'required_without:address_id|array',
            'shipping_address.name'             => 'required_without:address_id|string|max:255',
            'shipping_address.phone'            => 'required_without:address_id|string|max:20',
            'shipping_address.address_line1'    => 'required_without:address_id|string|max:255',
            'shipping_address.address_line2'    => 'nullable|string|max:255',
            'shipping_address.city_id'          => 'required_without:address_id|exists:cities,id',
            'shipping_address.state_id'         => 'required_without:address_id|exists:states,id',
            'shipping_address.country_id'       => 'required_without:address_id|exists:countries,id',
            'shipping_address.pincode'          => 'required_without:address_id|string|max:10',
            'shipping_address.save_address'     => 'nullable|boolean',

            // ── Billing Address ───────────────────────────────────────────────
            // billing_same_as_shipping: 1 → copy shipping to billing
            // billing_same_as_shipping: 0 → use billing_address object fields
            'billing_same_as_shipping'              => 'nullable|integer|in:0,1',
            'billing_address'                       => 'required_if:billing_same_as_shipping,0|nullable|array',
            'billing_address.address_line1'         => 'required_if:billing_same_as_shipping,0|nullable|string|max:255',
            'billing_address.address_line2'         => 'nullable|string|max:255',
            'billing_address.city'                  => 'required_if:billing_same_as_shipping,0|nullable|string|max:100',
            'billing_address.state'                 => 'required_if:billing_same_as_shipping,0|nullable|string|max:100',
            'billing_address.country'               => 'required_if:billing_same_as_shipping,0|nullable|string|max:100',
            'billing_address.pincode'               => 'required_if:billing_same_as_shipping,0|nullable|string|max:10',

            // ── Payment ───────────────────────────────────────────────────────
            'payment_method' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists'                            => 'Customer not found.',
            'email.required'                                => 'Email address is required.',
            'email.email'                                   => 'Please provide a valid email address.',
            'products.required'                             => 'At least one product is required.',
            'products.*.product_id.exists'                  => 'One or more selected products do not exist.',
            'products.*.quantity.min'                       => 'Product quantity must be at least 1.',
            'address_id.exists'                             => 'Selected address not found.',
            'shipping_address.required_without'             => 'Shipping address is required when no saved address is selected.',
            'shipping_address.name.required_without'        => 'Full name is required when no saved address is selected.',
            'shipping_address.phone.required_without'       => 'Phone number is required when no saved address is selected.',
            'shipping_address.address_line1.required_without' => 'Address line 1 is required when no saved address is selected.',
            'shipping_address.city_id.required_without'    => 'City is required when no saved address is selected.',
            'shipping_address.state_id.required_without'   => 'State is required when no saved address is selected.',
            'shipping_address.country_id.required_without' => 'Country is required when no saved address is selected.',
            'shipping_address.pincode.required_without'    => 'Pincode is required when no saved address is selected.',
            'billing_address.required_if'                           => 'Billing address object is required when using a different billing address.',
            'billing_address.address_line1.required_if'             => 'Billing address line 1 is required when using a different billing address.',
            'billing_address.city.required_if'                      => 'Billing city is required when using a different billing address.',
            'billing_address.state.required_if'                     => 'Billing state is required when using a different billing address.',
            'billing_address.country.required_if'                   => 'Billing country is required when using a different billing address.',
            'billing_address.pincode.required_if'                   => 'Billing pincode is required when using a different billing address.',
            'payment_method.required'                               => 'Payment method is required.',
        ];
    }
}
