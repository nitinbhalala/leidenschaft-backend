<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ProductReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends BaseController
{
    public function index($product_id)
    {
        $reviewCollection = ProductReview::where('product_id', $product_id)
            ->with('customer:id,name,avatar')
            ->latest()
            ->get();

        $reviews = $reviewCollection->map(function ($review) {
            $data = $review->toArray();
            $data['customer_name'] = $review->customer?->name;
            unset($data['customer']);
            return $data;
        });

        return $this->success([
            'total_reviews' => $reviewCollection->count(),
            'avg_rating'    => $reviewCollection->count() > 0
                ? round($reviewCollection->avg('rating'), 1)
                : 0,
            'reviews'       => $reviews,
        ], "Reviews fetched successfully");
    }

    public function store(ProductReviewRequest $request)
    {
        $review = ProductReview::create($request->validated());

        OrderItem::where('product_id', $request->product_id)
            ->whereHas('order', function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id)
                    ->where('status', 'delivered');
            })
            ->whereNull('review_id')
            ->first()
            ?->update(['review_id' => $review->id]);

        return $this->success($review, "Review added successfully", 201);
    }

    public function destroy(ProductReview $productReview)
    {
        $productReview->delete();

        return $this->success(null, "Review deleted successfully");
    }

    public function reviewsBySlug($slug, Request $request)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $reviewCollection = ProductReview::where('product_id', $product->id)
            ->with('customer:id,name')
            ->latest()
            ->paginate($request->input('per_page', 10));

        $reviews = $reviewCollection->getCollection()->map(function ($review) {
            $data = $review->toArray();
            $data['customer_name'] = $review->customer?->name;
            unset($data['customer']);
            return $data;
        });

        return $this->success([
            'total_reviews' => ProductReview::where('product_id', $product->id)->count(),
            'avg_rating'    => ProductReview::where('product_id', $product->id)->avg('rating')
                ? round(ProductReview::where('product_id', $product->id)->avg('rating'), 1)
                : 0,
            'pagination'    => [
                'current_page' => $reviewCollection->currentPage(),
                'last_page'    => $reviewCollection->lastPage(),
                'per_page'     => $reviewCollection->perPage(),
                'total'        => $reviewCollection->total(),
            ],
            'reviews'       => $reviews,
        ], "Reviews fetched successfully");
    }
}
