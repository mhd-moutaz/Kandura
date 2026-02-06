<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Services\Users\NotificationService;
use App\Http\Resources\Users\NotificationResource;
use App\Http\Requests\Users\UpdateFcmTokenRequest;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications();

        return $this->success(
            NotificationResource::collection($notifications),
            'Notifications retrieved successfully'
        );
    }

 

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $this->notificationService->markAllAsRead();

            return $this->success(
                null,
                'All notifications marked as read successfully'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }



    /**
     * Update FCM token for push notifications
     */
    public function updateFcmToken(UpdateFcmTokenRequest $request)
    {
        try {
            $this->notificationService->updateFcmToken($request->fcm_token);

            return $this->success(
                null,
                'FCM token updated successfully'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }
}
