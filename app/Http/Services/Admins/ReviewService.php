<?php

namespace App\Http\Services\Admins;

use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use App\Enum\StatusOrderEnum;
use App\Exceptions\GeneralException;
use Illuminate\Container\Attributes\Auth;

class ReviewService
{
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

        return [
            'total_reviews' => $totalReviews,
        ];
    }
}
