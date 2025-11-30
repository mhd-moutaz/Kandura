<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Global\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Services\Users\AuthService;
use App\Http\Resources\Users\UserResource;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        return $this->success(new UserResource($user), "User registered successfully");
    }
    public function login(LoginRequest $request)
    {
        $token = $this->authService->login($request->validated());
        return $this->success(['token' => $token], "User logged in successfully");
    }
    public function logout(User $user)
    {
        $this->authService->logout($user);
        return $this->success(null, "User logged out successfully");
    }
}
