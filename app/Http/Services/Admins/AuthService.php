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
        if (Auth::attempt($data)) {
            $user = User::find(Auth::id());
            $user->access_token = $user->createToken("API Token")->accessToken;
            return $user;
        }
        throw new GeneralException("Invalid credentials", 401);
    }
}
