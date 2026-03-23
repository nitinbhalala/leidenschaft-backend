<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerController extends BaseController
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);

        return $this->success($customers, "Customers fetched successfully");
    }

    public function store(CustomerRequest $request)
    {
        try {
            $data = $request->validated();

            $data['password'] = Hash::make($data['password']);

            $customer = Customer::create($data);

            return $this->success($customer, "Customer created successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error("Customer not found", 404);
        }

        return $this->success($customer, "Customer fetched successfully");
    }

    public function update(CustomerRequest $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error("Customer not found", 404);
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $customer->update($data);

        return $this->success($customer, "Customer updated successfully");
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->error("Customer not found", 404);
        }

        $customer->delete();

        return $this->success(null, "Customer deleted successfully");
    }
}
