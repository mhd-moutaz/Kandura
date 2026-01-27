<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Services\ReviewService;
use App\Http\Requests\Users\CreateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Get user's reviews
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['rating', 'per_page']);

        $reviews = $this->reviewService->getUserReviews($user, $filters);

        return ReviewResource::collection($reviews);
    }

    /**
     * Create a review for an order
     */
    public function store(CreateReviewRequest $request, Order $order)
    {
        $this->authorize('create', \App\Models\Review::class);

        $user = $request->user();
        $review = $this->reviewService->createReview($user, $order, $request->validated());

        return new ReviewResource($review);
    }

    /**
     * Delete user's own review
     */
    public function destroy(Request $request, $reviewId)
    {
        $review = \App\Models\Review::findOrFail($reviewId);
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($review);

        return response()->json([
            'message' => 'تم حذف التقييم بنجاح / Review deleted successfully'
        ]);
    }
}
