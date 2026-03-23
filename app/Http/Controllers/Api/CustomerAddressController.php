<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomerAddress;
use App\Http\Requests\CustomerAddressRequest;

class CustomerAddressController extends BaseController
{
    public function index()
    {
        $addresses = CustomerAddress::with(['country', 'state', 'city'])->latest()->get();

        return $this->success($addresses, "Addresses fetched successfully");
    }

    public function store(CustomerAddressRequest $request)
    {
        $address = CustomerAddress::create($request->validated());

        return $this->success($address, "Address created successfully", 201);
    }

    public function show(CustomerAddress $customerAddress)
    {
        return $this->success($customerAddress, "Address fetched successfully");
    }

    public function update(CustomerAddressRequest $request, CustomerAddress $customerAddress)
    {
        $customerAddress->update($request->validated());

        return $this->success($customerAddress, "Address updated successfully");
    }

    public function destroy(CustomerAddress $customerAddress)
    {
        $customerAddress->delete();

        return $this->success(null, "Address deleted successfully");
    }
}
