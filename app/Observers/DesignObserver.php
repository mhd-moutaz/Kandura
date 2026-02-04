<?php

namespace App\Observers;

use App\Models\Design;
use App\Models\User;
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

        // Get all admin and super admin users
        $admins = User::whereIn('role', [UserRoleEnum::ADMIN, UserRoleEnum::SUPER_ADMIN])
            ->whereNotNull('fcm_token') // Only notify users with FCM tokens
            ->permission('view all designs')
            ->get();


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
}

