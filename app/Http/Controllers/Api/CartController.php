<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Http\Requests\AddToCartRequest;

class CartController extends BaseController
{
    public function getCart($customer_id)
    {
        try {
            $cartItems = Cart::with('product')
                ->where('customer_id', $customer_id)
                ->get()
                ->map(function ($item) {
                    $product = $item->product;
                    return [
                        'cart_id'    => $item->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'product'    => $product ? [
                            'id'     => $product->id,
                            'name'   => $product->name,
                            'slug'   => $product->slug,
                            'sku'    => $product->sku,
                            'price'  => $product->price,
                            'stock'  => $product->stock,
                            'images' => $product->images,
                        ] : null,
                        'subtotal' => $product ? $product->price * $item->quantity : 0,
                    ];
                });

            $total = $cartItems->sum('subtotal');

            return $this->success([
                'items'       => $cartItems,
                'items_count' => $cartItems->sum('quantity'),
                'total'       => $total,
            ], "Cart items fetched successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function addToCart(AddToCartRequest $request)
    {
        try {
            $cart = Cart::where('customer_id', $request->customer_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cart) {

                $cart->quantity = $cart->quantity + ($request->quantity ?? 1);
                $cart->save();

                return $this->success($cart, "Cart quantity updated");
            }

            $cart = Cart::create([
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity ?? 1
            ]);

            return $this->success($cart, "Product added to cart");
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function updateCart(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cart = Cart::findOrFail($id);
            $cart->quantity = $request->quantity;
            $cart->save();

            return $this->success($cart, "Cart quantity updated");
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->delete();

            return $this->success(null, "Cart item removed");
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
