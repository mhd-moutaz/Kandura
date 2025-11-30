<?php

namespace App\Http\Services\Users;

use App\Exceptions\GeneralException;
use App\Models\User;
use App\Enum\UserRoleEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function register(array $data)
    {
        DB::beginTransaction();
        try {
            $data['role'] = UserRoleEnum::USER;
            if (isset($data['profile_image'])) {
                $imageName = time() . '_' . uniqid() . '.' . $data['profile_image']->getClientOriginalExtension();
                $imagePath = $data['profile_image']->storeAs('user_images',$imageName, 'public');
                $data['profile_image'] = $imagePath;
            }
            $user = User::create($data);
            $user->assignRole(UserRoleEnum::USER);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Registration failed: '.$e->getMessage(), 400);
        }
        return $user;
    }
    public function login(array $data)
    {

        try {
            if (!Auth::attempt($data) || Auth::user()->is_active == 0) {
                throw new GeneralException('The provided credentials are incorrect or the account is inactive.', 401);
            }
            $user = User::where('email', $data['email'])->first();
            $token = $user->createToken('auth_token')->accessToken;
            return $token;
        } catch (\Exception $e) {
            throw new GeneralException('Login failed: '.$e->getMessage(), 400);
        }

    }
    public function logout($user)
    {
        try{
            $user->tokens()->delete();
        } catch (\Exception $e) {
            throw new GeneralException('Logout failed: ', 400);
        }
    }
}
