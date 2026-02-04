<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class NotificationController extends Controller
{
    /**
     * Update FCM token for the authenticated user.
     * This also clears the same token from other users to prevent
     * notifications being sent to wrong users on the same browser/device.
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();
        $newToken = $request->fcm_token;

        // Clear this FCM token from any other users
        // This is important when different users log in on the same browser/device
        User::where('fcm_token', $newToken)
            ->where('id', '!=', $user->id)
            ->update(['fcm_token' => null]);

        // Update the current user's FCM token
        $user->update(['fcm_token' => $newToken]);

        Log::info('FCM token updated', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'token_prefix' => substr($newToken, 0, 20) . '...',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully',
        ]);
    }

    /**
     * Get all notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
