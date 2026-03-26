<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\ProductInventory;
use Illuminate\Http\Request;

class ProductInventoryController extends BaseController
{
    public function index(Request $request)
    {
        $query = ProductInventory::with(['product.images', 'product.category', 'product.subCategory']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })
                    ->orWhereHas('product.category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 10;
        $inventories = $query->latest()->paginate($perPage);

        return $this->success($inventories, 'Inventory fetched successfully');
    }

    public function show($id)
    {
        $inventory = ProductInventory::with(['product.images', 'product.category', 'product.subCategory'])
            ->findOrFail($id);

        return $this->success($inventory, 'Inventory item fetched successfully');
    }

    public function stats()
    {
        return $this->success([
            'total_products' => ProductInventory::count(),
            'in_stock' => ProductInventory::where('status', 'in_stock')->count(),
            'low_stock' => ProductInventory::where('status', 'low_stock')->count(),
            'out_of_stock' => ProductInventory::where('status', 'out_of_stock')->count(),
        ], 'Stats fetched successfully');
    }

    public function updateStockUniversal(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_inventories,product_id',
            'quantity' => 'required|integer',
        ]);

        $inventory = ProductInventory::where('product_id', $request->product_id)->firstOrFail();

        $inventory->stock = $request->quantity;

        if ($inventory->stock < 0) {
            $inventory->stock = 0;
        }

        $inventory->updateStockStatus();

        $inventory->load(['product.images', 'product.category', 'product.subCategory']);

        return $this->success($inventory, 'Stock updated successfully');
    }

    public function toggleActive($id)
    {
        $inventory = ProductInventory::findOrFail($id);

        $inventory->update([
            'is_active' => !$inventory->is_active,
        ]);

        $inventory->load(['product.images', 'product.category', 'product.subCategory']);

        return $this->success($inventory, 'Status updated');
    }
}
