<?php

namespace App\Http\Controllers\Admins;

use App\Enum\UserRoleEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Admins\AuthService;
use App\Exceptions\GeneralException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function loginView()
    {
        return view("admin.login");
    }

    public function login(LoginRequest $request)
    {
        $attr = $request->validated();
        try {
            $user = $this->authService->login($attr);
            if ($user->role !== UserRoleEnum::ADMIN && $user->role !== UserRoleEnum::SUPER_ADMIN) {
                return redirect()->route('login')->with('error', 'You do not have permission to access the admin panel.');
            }
            Auth::login($user);
            return redirect()->route('home');
        } catch (GeneralException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
