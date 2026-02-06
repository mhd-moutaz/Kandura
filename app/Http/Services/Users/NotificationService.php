<?php

namespace App\Http\Services\Users;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\GeneralException;

class NotificationService
{
    /**
     * Get all notifications for the authenticated user
     */
    public function getUserNotifications()
    {
        $user = Auth::user();

        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

  

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return true;
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        return $user->unreadNotifications()->count();
    }

    /**
     * Update FCM token
     */
    public function updateFcmToken(string $fcmToken)
    {
        $user = Auth::user();

        $user->update(['fcm_token' => $fcmToken]);

        return $user;
    }
}
