<?php

namespace App\Http\Controllers\Api;

use App\Models\Wishlist;
use App\Http\Requests\AddToWishlistRequest;

class WishlistController extends BaseController
{
    public function addToWishlist(AddToWishlistRequest $request)
    {
        try {
            $wishlist = Wishlist::where('customer_id', $request->customer_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($wishlist) {
                return $this->success($wishlist, "Product already in wishlist");
            }

            $wishlist = Wishlist::create([
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
            ]);

            return $this->success($wishlist, "Product added to wishlist");
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $wishlist = Wishlist::findOrFail($id);
            $wishlist->delete();

            return $this->success(null, "Wishlist item removed");
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
