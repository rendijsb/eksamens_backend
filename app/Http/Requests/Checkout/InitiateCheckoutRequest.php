<?php

declare(strict_types=1);

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class InitiateCheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address_id' => 'nullable|exists:addresses,id',
            'billing_address_id' => 'nullable|exists:addresses,id',
            'shipping_address' => 'required_without:shipping_address_id|array',
            'shipping_address.name' => 'required_with:shipping_address|string|max:255',
            'shipping_address.phone' => 'required_with:shipping_address|string|max:20',
            'shipping_address.street_address' => 'required_with:shipping_address|string|max:255',
            'shipping_address.apartment' => 'nullable|string|max:50',
            'shipping_address.city' => 'required_with:shipping_address|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'required_with:shipping_address|string|max:20',
            'shipping_address.country' => 'required_with:shipping_address|string|max:100',
            'same_billing_address' => 'required|boolean',
            'billing_address' => 'required_if:same_billing_address,false|array',
            'billing_address.name' => 'required_with:billing_address|string|max:255',
            'billing_address.phone' => 'required_with:billing_address|string|max:20',
            'billing_address.street_address' => 'required_with:billing_address|string|max:255',
            'billing_address.apartment' => 'nullable|string|max:50',
            'billing_address.city' => 'required_with:billing_address|string|max:100',
            'billing_address.state' => 'nullable|string|max:100',
            'billing_address.postal_code' => 'required_with:billing_address|string|max:20',
            'billing_address.country' => 'required_with:billing_address|string|max:100',
            'payment_method' => 'required|string|in:stripe',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['shipping_address_id']) && $this->user()) {
            $shippingAddress = $this->user()->addresses()->find($validated['shipping_address_id']);
            if ($shippingAddress) {
                $validated['shipping_address'] = $shippingAddress->toArray();
            }
        }

        if (isset($validated['billing_address_id']) && $this->user()) {
            $billingAddress = $this->user()->addresses()->find($validated['billing_address_id']);
            if ($billingAddress) {
                $validated['billing_address'] = $billingAddress->toArray();
            }
        }

        return $validated;
    }
}
