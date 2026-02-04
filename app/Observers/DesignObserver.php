<?php

namespace App\Observers;

use App\Models\Design;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItems;
use App\Notifications\DesignNotification;
use App\Enum\UserRoleEnum;
use Illuminate\Support\Facades\Log;

class DesignObserver
{
    /**
     * Handle the Design "created" event.
     */
    public function created(Design $design): void
    {
        // Load the user relationship to get creator info
        $design->load('user');

        // Get all admin and super admin users with FCM tokens
        // Then filter only those who have the 'view all designs' permission
        $admins = User::whereIn('role', [UserRoleEnum::ADMIN, UserRoleEnum::SUPER_ADMIN])
            ->whereNotNull('fcm_token') // Only notify users with FCM tokens
            ->get()
            ->filter(function ($admin) {
                return $admin->hasPermissionTo('view all designs');
            });


        foreach ($admins as $admin) {
            Log::info('Admin eligible for design notification', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'admin_permissions' => $admin->getAllPermissions()->pluck('name')->toArray(),
            ]);
        }
        // Send notification to all admins
        foreach ($admins as $admin) {
            $admin->notify(new DesignNotification($design, 'design_created'));
        }
    }

    /**
     * Handle the Design "updated" event.
     * When design state changes to false, remove it from all pending orders (carts)
     */
    public function updated(Design $design): void
    {
        // Check if state changed from true to false
        if ($design->isDirty('state') && $design->getOriginal('state') == true && $design->state == false) {
            $this->removeDesignFromPendingOrders($design);
        }
    }

    /**
     * Remove design from all pending orders (carts)
     */
    private function removeDesignFromPendingOrders(Design $design): void
    {
        // Get all order items with this design in pending orders
        $orderItems = OrderItems::where('design_id', $design->id)
            ->whereHas('order', function ($query) {
                $query->where('status', 'pending');
            })
            ->get();

        foreach ($orderItems as $orderItem) {
            $order = $orderItem->order;

            // Detach design options
            $orderItem->designOptions()->detach();

            // Delete the order item
            $orderItem->delete();

            // Check if order has remaining items
            $remainingItems = $order->orderItems()->count();

            if ($remainingItems === 0) {
                // Delete empty order
                $order->delete();

                Log::info('Pending order deleted because design was deactivated', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'design_id' => $design->id,
                ]);
            } else {
                // Update order total
                $total = $order->orderItems()->sum('total_price');
                $order->update(['total' => $total]);

                Log::info('Order item removed from cart because design was deactivated', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'design_id' => $design->id,
                    'remaining_items' => $remainingItems,
                ]);
            }
        }

        if ($orderItems->count() > 0) {
            Log::info('Design deactivated - removed from pending orders', [
                'design_id' => $design->id,
                'affected_order_items' => $orderItems->count(),
            ]);
        }
    }
}

