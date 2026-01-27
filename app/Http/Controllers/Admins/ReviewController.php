<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Services\ReviewService;
use App\Http\Resources\ReviewResource;
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
     * Get all reviews (admin)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Review::class);

        $filters = $request->only(['rating', 'search', 'sort_by', 'sort_dir', 'per_page']);
        $reviews = $this->reviewService->getAllReviews($filters);

        return ReviewResource::collection($reviews);
    }

    /**
     * Get review statistics
     */
    public function stats()
    {
        $this->authorize('viewAny', Review::class);

        $stats = $this->reviewService->getReviewStats();

        return response()->json($stats);
    }

    /**
     * Delete a review (admin)
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($review);

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}
