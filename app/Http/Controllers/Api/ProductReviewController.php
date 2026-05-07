<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ProductReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Exception;
use Illuminate\Http\Request;

class ProductReviewController extends BaseController
{
    public function index($product_id, Request $request)
    {
        $reviewCollection = ProductReview::where('product_id', $product_id)
            ->with('customer:id,name,avatar')
            ->latest()
            ->paginate($request->input('per_page', 10));

        $reviews = $reviewCollection->getCollection()->map(function ($review) {
            $data = $review->toArray();
            $data['customer_name'] = $review->customer?->name;
            unset($data['customer']);
            return $data;
        });

        return $this->success([
            'total_reviews' => ProductReview::where('product_id', $product_id)->count(),
            'avg_rating'    => ProductReview::where('product_id', $product_id)->avg('rating')
                ? round(ProductReview::where('product_id', $product_id)->avg('rating'), 1)
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

    public function toggleStatus($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update review status.', 403);
            }

            $review = ProductReview::find($id);

            if (!$review) {
                return $this->error('Review not found', 404);
            }

            $review->update([
                'status' => !$review->status
            ]);

            return $this->success($review, 'Review status updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
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
            ->where('status', 1)
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
