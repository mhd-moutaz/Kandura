<?php

namespace App\Http\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function show()
    {
        return Auth::user();
    }

    public function update(array $data)
    {
        $user = User::find(Auth::id());
        if (isset($data['profile_image'])) {
            Storage::disk('public')->delete($user->profile_image);
            $imageName = time() . '_' . uniqid() . '.' . $data['profile_image']->getClientOriginalExtension();
            $imagePath = $data['profile_image']->storeAs('user_images', $imageName, 'public');
            $data['profile_image'] = $imagePath;
        }
        $user->update($data);
        return $user;
    }
    public function destroy()
    {
        $user = User::find(Auth::id());
        Storage::disk('public')->delete($user->profile_image);
        $user->delete();
    }
}
