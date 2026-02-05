<?php

namespace App\Policies;

use App\Constants\PermissionConstants;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any reviews.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionConstants::VIEW_ALL_REVIEWS, 'web');
    }

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, Review $review): bool
    {
        // User can view their own review or admin can view all
        return $user->id === $review->user_id ||
               $user->hasPermissionTo(PermissionConstants::VIEW_ALL_REVIEWS, 'web');
    }

    /**
     * Determine whether the user can create reviews.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionConstants::CREATE_REVIEW, 'api');
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, Review $review): bool
    {
        // User can delete their own review or admin can delete any
        return $user->id === $review->user_id ||
               $user->hasPermissionTo(PermissionConstants::DELETE_REVIEW_ADMIN, 'web');
    }
}
