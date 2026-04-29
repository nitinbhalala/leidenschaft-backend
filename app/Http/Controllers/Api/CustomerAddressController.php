<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomerAddress;
use App\Http\Requests\CustomerAddressRequest;

class CustomerAddressController extends BaseController
{
    public function index($customer_id)
    {
        $addresses = CustomerAddress::with(['country', 'state', 'city'])
            ->where('customer_id', $customer_id)
            ->latest()
            ->get();

        return $this->success($addresses, "Addresses fetched successfully");
    }

    public function store(CustomerAddressRequest $request)
    {
        $data = $request->validated();

        if (isset($data['is_default']) && $data['is_default'] == 1) {
            CustomerAddress::where('customer_id', $data['customer_id'])
                ->update(['is_default' => 0]);
        }

        $address = CustomerAddress::create($data);

        return $this->success(
            $address->load(['country', 'state', 'city']),
            "Address created successfully",
            201
        );
    }

    public function show($id)
    {
        $address = CustomerAddress::with(['country', 'state', 'city'])->find($id);

        if (!$address) {
            return $this->error("Address not found", 404);
        }

        return $this->success($address, "Address fetched successfully");
    }

    public function update(CustomerAddressRequest $request, $id)
    {
        $address = CustomerAddress::find($id);

        if (!$address) {
            return $this->error("Address not found", 404);
        }

        $data = $request->validated();

        if (isset($data['is_default']) && $data['is_default'] == 1) {
            CustomerAddress::where('customer_id', $address->customer_id)
                ->where('id', '!=', $id)
                ->update(['is_default' => 0]);
        }

        $address->update($data);

        return $this->success(
            $address->load(['country', 'state', 'city']),
            "Address updated successfully"
        );
    }

    public function destroy($id)
    {
        $address = CustomerAddress::find($id);

        if (!$address) {
            return $this->error("Address not found", 404);
        }

        /* if ($address->is_default == 1) {
            return $this->error("Please set another address as default before deleting this one", 400);
        } */

        $address->delete();

        return $this->success(null, "Address deleted successfully");
    }

    public function setDefault($id)
    {
        $address = CustomerAddress::find($id);

        if (!$address) {
            return $this->error("Address not found", 404);
        }

        CustomerAddress::where('customer_id', $address->customer_id)
            ->update(['is_default' => 0]);

        $address->update(['is_default' => 1]);

        return $this->success($address, "Address set as default successfully");
    }
}
