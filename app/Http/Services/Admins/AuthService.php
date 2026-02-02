<?php

namespace App\Http\Services\Admins;

use App\Models\User;
use App\Enum\UserRoleEnum;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Auth;



class AuthService
{
    public function login(array $data)
    {
        if (Auth::attempt($data) ) {
            $user = User::find(Auth::id());
            if($user->is_active == 0){
                Auth::logout();
                throw new GeneralException('Your account is deactivated. Please contact support.', 403);
            }
            $user->access_token = $user->createToken("API Token")->accessToken;
            return $user;
        }
        throw new GeneralException("Invalid credentials", 401);
    }
}
