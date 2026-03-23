<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends BaseController
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);

        return $this->success($orders, "Orders fetched successfully");
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $data = $request->validated();

            $data['order_number'] = 'ORD-' . strtoupper(uniqid());
            $data['placed_at'] = now();

            $order = Order::create($data);

            return $this->success($order, "Order created successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        return $this->success($order, "Order fetched successfully");
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        $order->update($request->validated());

        return $this->success($order, "Order updated successfully");
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        $order->delete();

        return $this->success(null, "Order deleted successfully");
    }
}
