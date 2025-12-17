<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrderItems;
use App\Exceptions\GeneralException;

class OrderItemPolicy
{
    /**
     * Determine whether the user can update the order item.
     */
    public function update(User $user, OrderItems $orderItem): bool
    {
        if ($orderItem->order->user_id === $user->id) {
            return true;
        }
        throw new GeneralException('You are not authorized to update this order item.', 403);
    }

    /**
     * Determine whether the user can delete the order item.
     */
    public function delete(User $user, OrderItems $orderItem): bool
    {
        if ($orderItem->order->user_id === $user->id) {
            return true;
        }
        throw new GeneralException('You are not authorized to delete this order item.', 403);
    }
}
