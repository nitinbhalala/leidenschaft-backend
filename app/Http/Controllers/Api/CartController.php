<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Http\Requests\AddToCartRequest;

class CartController extends BaseController
{
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
