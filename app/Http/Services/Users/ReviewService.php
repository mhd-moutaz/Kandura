<?php

namespace App\Http\Services\Users;

use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Enum\StatusOrderEnum;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Auth;


class ReviewService
{
    /**
     * Get user's reviews
     */
    public function index(array $filters = [])
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
     * Create a review for an order
     */
    public function store(User $user, Order $order, array $data): Review
    {
        // Validate order status
        if ($order->status !== StatusOrderEnum::COMPLETED) {
            throw new GeneralException( 'You can only review completed orders', 400);
        }

        // Validate order ownership
        if ($order->user_id !== $user->id) {
            throw new GeneralException('You cannot review an order that is not yours', 403);
        }

        // Check if review already exists
        if ($order->review) {
            throw new GeneralException('You have already reviewed this order', 400);
        }

        // Validate rating
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            throw new GeneralException('Rating must be between 1 and 5', 400);
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
     * Delete a review (admin)
     */
    public function deleteReview(Review $review): bool
    {
        return $review->delete();
    }

}
