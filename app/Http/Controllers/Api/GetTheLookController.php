<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\GetTheLookRequest;
use App\Models\GetTheLook;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GetTheLookController extends BaseController
{
    public function index()
    {
        try {
            $admin = request()->attributes->get('admin');

            $query = GetTheLook::query();

            if (!$admin) {
                $query->where('status', 1);
            }

            $items = $query->latest()->get()->map(function ($item) {
                $data = $item->toArray();
                $data['products'] = Product::with(['images'])
                    ->whereIn('id', $item->product_ids ?? [])
                    ->where('status', 1)
                    ->get();
                return $data;
            });

            return $this->success($items, 'Get the look items fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function customerIndex()
    {
        try {
            $items = GetTheLook::where('status', 1)->latest()->get()->map(function ($item) {
                $position = $item->position ?? [];

                $products = Product::with(['images', 'inventory'])
                    ->whereIn('id', $item->product_ids ?? [])
                    ->where('status', 1)
                    ->get()
                    ->map(function ($product) use ($position) {
                        $data             = $product->toArray();
                        $data['position'] = $position[(string) $product->id] ?? null;
                        return $data;
                    });

                return [
                    'id'       => $item->id,
                    'image'    => $item->toArray()['image'],
                    'products' => $products,
                ];
            });

            return $this->success($items, 'Get the look items fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            $item = GetTheLook::find($id);

            if (!$item) {
                return $this->error('Item not found', 404);
            }

            if (!$admin && $item->status != 1) {
                return $this->error('Item not found', 404);
            }

            $data = $item->toArray();
            $data['products'] = Product::with(['images'])
                ->whereIn('id', $item->product_ids ?? [])
                ->where('status', 1)
                ->get();

            return $this->success($data, 'Get the look item fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(GetTheLookRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can create get the look items.', 403);
            }

            $path = $request->file('image')->store('get-the-look', 'public');

            $item = GetTheLook::create([
                'image'       => $path,
                'product_ids' => $request->product_ids,
                'position'    => $request->position,
                'status'      => $request->status ?? 1,
            ]);

            return $this->success($item, 'Get the look item created successfully', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(GetTheLookRequest $request, $id)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update get the look items.', 403);
            }

            $item = GetTheLook::find($id);

            if (!$item) {
                return $this->error('Item not found', 404);
            }

            $updateData = [];

            if ($request->hasFile('image')) {
                if ($item->getRawOriginal('image') && Storage::disk('public')->exists($item->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($item->getRawOriginal('image'));
                }
                $updateData['image'] = $request->file('image')->store('get-the-look', 'public');
            }

            if ($request->has('product_ids')) {
                $updateData['product_ids'] = $request->product_ids;
            }

            if ($request->has('position')) {
                $updateData['position'] = $request->position;
            }

            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }

            $item->update($updateData);

            return $this->success($item->fresh(), 'Get the look item updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can delete get the look items.', 403);
            }

            $item = GetTheLook::find($id);

            if (!$item) {
                return $this->error('Item not found', 404);
            }

            if ($item->getRawOriginal('image') && Storage::disk('public')->exists($item->getRawOriginal('image'))) {
                Storage::disk('public')->delete($item->getRawOriginal('image'));
            }

            $item->delete();

            return $this->success(null, 'Get the look item deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update get the look items.', 403);
            }

            $item = GetTheLook::find($id);

            if (!$item) {
                return $this->error('Item not found', 404);
            }

            $item->update(['status' => $item->status == 1 ? 0 : 1]);

            return $this->success($item->fresh(), 'Status updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
