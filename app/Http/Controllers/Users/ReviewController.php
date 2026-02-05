<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Services\Users\ReviewService;
use App\Http\Requests\Users\CreateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Order;
use App\Models\Review;
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
        $filters = $request->only(['rating', 'per_page']);
        $reviews = $this->reviewService->index($filters);
        return $this->success(ReviewResource::collection($reviews));
    }

    /**
     * Create a review for an order
     */
    public function store(CreateReviewRequest $request, Order $order)
    {
        $this->authorize('create', \App\Models\Review::class);

        $user = $request->user();
        $review = $this->reviewService->store($user, $order, $request->validated());

        return $this->success(new ReviewResource($review));
    }

    /**
     * Delete user's own review
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $this->reviewService->deleteReview($review);

        return $this->success([
            'message' => 'Review deleted successfully'
        ]);
    }
}
