<?php

namespace App\Http\Services\Users;

use App\Exceptions\GeneralException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function show()
    {
        return Auth::user();
    }

    public function update(array $data)
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            if (isset($data['profile_image'])) {
                $data['profile_image'] = $this->handleProfileImage($user, $data['profile_image']);
            }

            $user->update($data);
            DB::commit();

            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update user profile: ' . $e->getMessage());
        }
    }
    public function destroy()
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $this->deleteProfileImage($user);
            $user->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to delete user profile: ' . $e->getMessage());
        }
    }
    private function handleProfileImage(User $user, $image): string
    {
        // Delete old image if exists
        $this->deleteProfileImage($user);

        // Upload new image
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('user_images', $imageName, 'public');

        if (!$imagePath) {
            throw new \Exception('Failed to upload profile image');
        }

        return $imagePath;
    }
    private function deleteProfileImage(User $user): void
    {
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }
    }
}
