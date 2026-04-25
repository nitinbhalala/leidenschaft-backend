<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductReview;
use App\Http\Requests\ProductReviewRequest;

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

        return $this->success($review, "Review added successfully", 201);
    }

    public function destroy(ProductReview $productReview)
    {
        $productReview->delete();

        return $this->success(null, "Review deleted successfully");
    }
}
