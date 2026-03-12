<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductRequest;
use Exception;

class ProductController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        try {

            $products = Product::with(['category', 'images'])
                ->latest()
                ->get();

            return $this->success($products);
        } catch (Exception $e) {
            return $this->error();
        }
    }

    public function store(ProductRequest $request)
    {
        try {

            $this->authorize('create', Product::class);

            $product = DB::transaction(function () use ($request) {

                $product = Product::create([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price
                ]);

                if ($request->hasFile('images')) {

                    foreach ($request->file('images') as $image) {

                        $path = $image->store("products/" . $product->id, 'public');

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => $path
                        ]);
                    }
                }

                return $product->load('images');
            });

            return $this->success(
                $product,
                'Product created successfully',
                201
            );
        } catch (Exception $e) {
            return $this->error();
        }
    }

    public function show(Product $product)
    {
        try {

            $product->load(['category', 'images']);

            return $this->success($product);
        } catch (Exception $e) {
            return $this->error();
        }
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {

            $this->authorize('update', $product);

            $product = DB::transaction(function () use ($request, $product) {

                $product->update([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price
                ]);

                if ($request->hasFile('images')) {

                    foreach ($product->images as $img) {
                        Storage::disk('public')->delete($img->image);
                        $img->delete();
                    }

                    foreach ($request->file('images') as $image) {

                        $path = $image->store("products/" . $product->id, 'public');

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => $path
                        ]);
                    }
                }

                return $product->load('images');
            });

            return $this->success(
                $product,
                'Product updated successfully'
            );
        } catch (Exception $e) {
            return $this->error();
        }
    }

    public function destroy(Product $product)
    {
        try {

            $this->authorize('delete', $product);

            DB::transaction(function () use ($product) {

                foreach ($product->images as $img) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }

                $product->delete();
            });

            return $this->success(null, 'Product deleted successfully');
        } catch (Exception $e) {
            return $this->error();
        }
    }
}
