<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductReview;
use App\Http\Requests\ProductReviewRequest;

class ProductReviewController extends BaseController
{
    public function index($product_id)
    {
        $reviews = ProductReview::where('product_id', $product_id)
            ->latest()
            ->get();

        return $this->success($reviews, "Reviews fetched successfully");
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
