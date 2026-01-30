<?php

namespace App\Http\Services;

use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use App\Enum\StatusOrderEnum;
use App\Exceptions\GeneralException;
use Illuminate\Container\Attributes\Auth;

class ReviewService
{
    /**
     * Create a review for an order
     */
    public function createReview(User $user, Order $order, array $data): Review
    {
        // Validate order status
        if ($order->status !== StatusOrderEnum::COMPLETED) {
            throw new GeneralException('يمكنك فقط تقييم الطلبات المكتملة / You can only review completed orders', 400);
        }

        // Validate order ownership
        if ($order->user_id !== $user->id) {
            throw new GeneralException('لا يمكنك تقييم طلب ليس خاص بك / You cannot review an order that is not yours', 403);
        }

        // Check if review already exists
        if ($order->review) {
            throw new GeneralException('لقد قمت بالفعل بتقييم هذا الطلب / You have already reviewed this order', 400);
        }

        // Validate rating
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            throw new GeneralException('يجب أن يكون التقييم بين 1 و 5 / Rating must be between 1 and 5', 400);
        }

        // Create review
        $review = Review::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return $review->load(['user', 'order']);
    }

    /**
     * Get all reviews (admin)
     */
    public function getAllReviews(array $filters = [])
    {
        $query = Review::with(['user', 'order']);

        // Filter by rating
        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        // Search in comment
        if (isset($filters['search'])) {
            $query->where('comment', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('user', function ($q) use ($filters) {
                      $q->where('name', 'like', '%' . $filters['search'] . '%');
                  });
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get user's reviews
     */
    public function getUserReviews(array $filters = [])
    {
        $user = Auth::user();
        $query = Review::with(['order'])
            ->where('user_id', $user->id);

        // Filter by rating
        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Delete a review (admin)
     */
    public function deleteReview(Review $review): bool
    {
        return $review->delete();
    }

    /**
     * Get review statistics
     */
    public function getReviewStats(): array
    {
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating');

        $ratingDistribution = [
            5 => Review::where('rating', 5)->count(),
            4 => Review::where('rating', 4)->count(),
            3 => Review::where('rating', 3)->count(),
            2 => Review::where('rating', 2)->count(),
            1 => Review::where('rating', 1)->count(),
        ];

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 2),
            'rating_distribution' => $ratingDistribution,
        ];
    }
}
