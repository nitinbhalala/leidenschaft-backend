<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductReview;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductReviewRequest;

class ProductReviewController extends Controller
{
    public function index($product_id)
    {
        $reviews = ProductReview::where('product_id', $product_id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    public function store(ProductReviewRequest $request)
    {
        $review = ProductReview::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Review added successfully',
            'data' => $review
        ], 201);
    }

    public function destroy(ProductReview $productReview)
    {
        $productReview->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }
}
