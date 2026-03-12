<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\ProductInventory;
use Illuminate\Http\Request;

class ProductInventoryController extends BaseController
{
    public function index(Request $request)
    {
        $query = ProductInventory::with('product');

        if ($request->search) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        $inventories = $query->latest()->paginate(10);

        return $this->success($inventories, 'Inventory fetched successfully');
    }

    public function stats()
    {
        return $this->success([
            'total_products' => ProductInventory::count(),
            'in_stock' => ProductInventory::where('status', 'in_stock')->count(),
            'low_stock' => ProductInventory::where('status', 'low_stock')->count(),
            'out_of_stock' => ProductInventory::where('status', 'out_of_stock')->count(),
        ]);
    }

    public function increaseStock($id)
    {
        $inventory = ProductInventory::findOrFail($id);
        $inventory->increment('stock');
        $inventory->updateStockStatus();

        return $this->success($inventory, 'Stock increased');
    }

    public function decreaseStock($id)
    {
        $inventory = ProductInventory::findOrFail($id);

        if ($inventory->stock > 0) {
            $inventory->decrement('stock');
        }

        $inventory->updateStockStatus();

        return $this->success($inventory, 'Stock decreased');
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $inventory = ProductInventory::findOrFail($id);

        $inventory->update([
            'stock' => $request->stock
        ]);

        $inventory->updateStockStatus();

        return $this->success($inventory, 'Stock updated successfully');
    }

    public function toggleActive($id)
    {
        $inventory = ProductInventory::findOrFail($id);

        $inventory->update([
            'is_active' => !$inventory->is_active
        ]);

        return $this->success($inventory, 'Status updated');
    }
}
