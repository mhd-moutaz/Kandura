<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderNotification;
use App\Models\User;
class OrderObserver
{

    public function created(Order $order): void
    {
        // Load necessary relationships
        $order->load(['orderItems.design.user']);

        // Get all admins (users with admin or super_admin role)
        $admins = User::role(['admin', 'super_admin'])->get();

        // Notify all admins
        foreach ($admins as $admin) {
            $admin->notify(new OrderNotification($order, 'order_created'));
        }

        // Get unique design creators from order items
        $designCreators = collect();
        foreach ($order->orderItems as $item) {
            if ($item->design && $item->design->user) {
                $designCreators->push($item->design->user);
            }
        }

        // Notify design creators (remove duplicates)
        $designCreators->unique('id')->each(function ($creator) use ($order) {
            $creator->notify(new OrderNotification($order, 'order_created'));
        });
    }

    public function updated(Order $order): void
    {
        // Check if status has changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // Load user relationship
            $order->load('user');

            $notificationData = [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => $order->user?->id,
            ];

            // Notify all admins (DB + FCM)
            $admins = User::role(['admin', 'super_admin'])
            ->get()
            ->filter(function ($admin) {
                return $admin->hasPermissionTo('view all order');
            });
            
            foreach ($admins as $admin) {
                $admin->notify(new OrderNotification($order, 'order_status_changed', $notificationData));
            }

            // Notify order owner (DB only)
            if ($order->user) {
                $order->user->notify(new OrderNotification($order, 'order_status_changed', $notificationData));
            }
        }
    }


}
