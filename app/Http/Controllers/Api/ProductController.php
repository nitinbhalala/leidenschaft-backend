<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductInventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'subCategory', 'images']);

            $admin = request()->attributes->get('admin');

            if (!$admin) {
                $query->where('status', 1);

                if ($request->category) {
                    $query->whereHas('category', function ($q) use ($request) {
                        $q->where('slug', $request->category)
                            ->orWhere('name', $request->category);
                    });

                    if ($request->subCategory) {
                        $query->whereHas('subCategory', function ($q) use ($request) {
                            $q->where('slug', $request->subCategory)
                                ->orWhere('name', $request->subCategory);
                        });
                    }
                }
            }

            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            $perPage = $request->per_page ?? 10;
            $products = $query->latest()->paginate($perPage);

            return $this->success($products, 'Products fetched successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can create products.', 403);
            }

            $product = DB::transaction(function () use ($request) {

                $product = Product::create([
                    'category_id'         => $request->category_id,
                    'sub_category_id'     => $request->sub_category_id,
                    'name'                => $request->name,
                    'sku'                 => $request->sku,
                    'description'         => $request->description,
                    'delivery_returns'    => $request->delivery_returns,
                    'material_dimensions' => $request->material_dimensions,
                    'price'               => $request->price,
                    'stock'               => $request->stock ?? 0,
                    'status'              => $request->status ?? 1,
                ]);

                ProductInventory::create([
                    'product_id'          => $product->id,
                    'stock'               => $product->stock,
                    'low_stock_threshold' => $request->low_stock_threshold ?? 5,
                    'is_active'           => $product->status == 1,
                ])->updateStockStatus();

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $path = $image->store("products/{$product->id}", 'public');

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image'      => $path,
                        ]);
                    }
                }

                return $product->load(['category', 'subCategory', 'images']);
            });

            return $this->success($product, 'Product created successfully', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function customerShow(Product $product)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin && $product->status != 1) {
                return $this->error('Product not found', 404);
            }

            $product->load(['category', 'subCategory', 'images', 'reviews.customer:id,name']);

            $reviews = $product->reviews->map(function ($review) {
                $data = $review->toArray();
                $data['customer_name'] = $review->customer?->name;
                unset($data['customer']);
                return $data;
            });

            $data                  = $product->toArray();
            $data['reviews']       = $reviews;
            $data['total_reviews'] = $product->reviews->count();
            $data['avg_rating']    = $data['total_reviews'] > 0
                ? round($product->reviews->avg('rating'), 1)
                : 0;

            return $this->success($data, 'Product fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            $product = Product::find($id);

            if (!$admin && $product->status != 1) {
                return $this->error('Product not found', 404);
            }

            $product->load(['category', 'subCategory', 'images', 'reviews.customer:id,name']);

            $reviews = $product->reviews->map(function ($review) {
                $data = $review->toArray();
                $data['customer_name'] = $review->customer?->name;
                unset($data['customer']);
                return $data;
            });

            $data                  = $product->toArray();
            $data['reviews']       = $reviews;
            $data['total_reviews'] = $product->reviews->count();
            $data['avg_rating']    = $data['total_reviews'] > 0
                ? round($product->reviews->avg('rating'), 1)
                : 0;

            return $this->success($data, 'Product fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update products.', 403);
            }

            $product = Product::find($id);

            if (!$product) {
                return $this->error('Product not found', 404);
            }

            $product = DB::transaction(function () use ($request, $product) {

                $product->update([
                    'category_id'         => $request->category_id,
                    'sub_category_id'     => $request->sub_category_id,
                    'name'                => $request->name,
                    'sku'                 => $request->sku,
                    'description'         => $request->description,
                    'delivery_returns'    => $request->delivery_returns,
                    'material_dimensions' => $request->material_dimensions,
                    'price'               => $request->price,
                    'stock'               => $request->stock ?? $product->stock,
                    'status'              => $request->status ?? $product->status,
                ]);

                $inventory = ProductInventory::where('product_id', $product->id)->first();

                if ($inventory) {
                    $inventory->update([
                        'stock'     => $product->stock,
                        'is_active' => $product->status == 1,
                    ]);

                    $inventory->updateStockStatus();
                } else {
                    $inventory = ProductInventory::create([
                        'product_id'          => $product->id,
                        'stock'               => $product->stock,
                        'low_stock_threshold' => 5,
                        'is_active'           => $product->status == 1,
                    ]);

                    $inventory->updateStockStatus();
                }

                // Delete Images
                if ($request->has('delete_image_ids') && is_array($request->delete_image_ids)) {
                    $images = ProductImage::where('product_id', $product->id)
                        ->whereIn('id', $request->delete_image_ids)
                        ->get();

                    foreach ($images as $img) {
                        if ($img->image && Storage::disk('public')->exists($img->image)) {
                            Storage::disk('public')->delete($img->image);
                        }
                        $img->delete();
                    }
                }

                // Upload New Images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $path = $image->store("products/{$product->id}", 'public');

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image'      => $path,
                        ]);
                    }
                }

                return $product->load(['category', 'subCategory', 'images']);
            });

            return $this->success($product, 'Product updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can delete products.', 403);
            }

            $product = Product::find($id);

            DB::transaction(function () use ($product) {

                ProductInventory::where('product_id', $product->id)->delete();

                foreach ($product->images as $img) {
                    Storage::disk('public')->delete($img->getRawOriginal('image'));
                    $img->delete();
                }

                Storage::disk('public')->deleteDirectory("products/{$product->id}");

                $product->delete();
            });

            return $this->success(null, 'Product deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /* public function search(Request $request)
    {
        try {
            $query = Product::with(['category', 'subCategory', 'images'])
                ->where('status', 1);

            // Keyword search
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhereHas('category', function ($cq) use ($q) {
                            $cq->where('name', 'like', "%{$q}%");
                        });
                });
            }

            // Filter by category slug or id
            if ($request->filled('category')) {
                $query->whereHas('category', function ($cq) use ($request) {
                    $cq->where('slug', $request->category)
                        ->orWhere('id', $request->category);
                });
            }

            // Filter by sub-category slug or id
            if ($request->filled('sub_category')) {
                $query->whereHas('subCategory', function ($cq) use ($request) {
                    $cq->where('slug', $request->sub_category)
                        ->orWhere('id', $request->sub_category);
                });
            }

            // Price range filter
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // In-stock filter
            if ($request->boolean('in_stock')) {
                $query->where('stock', '>', 0);
            }

            // Sort
            $sortBy    = in_array($request->sort_by, ['name', 'price', 'created_at']) ? $request->sort_by : 'created_at';
            $sortOrder = $request->sort_order === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $perPage  = $request->per_page ?? 12;
            $products = $query->paginate($perPage);

            return $this->success($products, 'Products fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    } */

    public function toggleActive($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update products.', 403);
            }

            return DB::transaction(function () use ($id) {
                $product = Product::findOrFail($id);

                $product->update([
                    'status' => $product->status == 1 ? 0 : 1,
                ]);

                $inventory = ProductInventory::where('product_id', $product->id)->first();
                if ($inventory) {
                    $inventory->update([
                        'is_active' => $product->status == 1,
                    ]);
                }

                $product->load(['category', 'subCategory', 'images']);

                return $this->success($product, 'Product status updated successfully');
            });
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Product::with(['category', 'subCategory', 'images', 'inventory'])
                ->where('status', 1);

            // Keyword search
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhereHas('category', function ($cq) use ($q) {
                            $cq->where('name', 'like', "%{$q}%");
                        });
                });
            }

            // Category filter
            if ($request->filled('category')) {
                $query->whereHas('category', function ($cq) use ($request) {
                    $cq->where('slug', $request->category)
                        ->orWhere('id', $request->category);
                });
            }

            // Sub-category filter
            if ($request->filled('sub_category')) {
                $query->whereHas('subCategory', function ($cq) use ($request) {
                    $cq->where('slug', $request->sub_category)
                        ->orWhere('id', $request->sub_category);
                });
            }

            // Price range filter
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // ✅ Availability filter via product_inventories table
            $availability = $request->availability;
            if ($availability === 'in_stock') {
                $query->whereHas('inventory', function ($iq) {
                    $iq->where('is_active', 1)
                        ->where('status', 'in_stock');
                });
            } elseif ($availability === 'out_of_stock') {
                $query->whereHas('inventory', function ($iq) {
                    $iq->where('status', 'out_of_stock');
                });
            }
            // 'all' or missing = no filter

            // Sort options
            $sortBy = $request->sort_by ?? 'featured';

            match ($sortBy) {
                'name_asc'     => $query->orderBy('name', 'asc'),
                'name_desc'    => $query->orderBy('name', 'desc'),
                'price_asc'    => $query->orderBy('price', 'asc'),
                'price_desc'   => $query->orderBy('price', 'desc'),
                'date_asc'     => $query->orderBy('created_at', 'asc'),
                'date_desc'    => $query->orderBy('created_at', 'desc'),
                'best_selling' => $query->withCount('orders')->orderBy('orders_count', 'desc'),
                'relevant'     => $request->filled('q')
                    ? $query->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END, created_at DESC", ["{$request->q}%"])
                    : $query->orderBy('created_at', 'desc'),
                default        => $query->orderBy('created_at', 'desc'),
            };

            $perPage  = $request->per_page ?? 12;
            $products = $query->paginate($perPage);

            return $this->success($products, 'Products fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
