<?php

namespace App\Http\Controllers\Admins;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Services\Admins\UserService;

class UserController extends Controller
{

    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        Gate::authorize('adminViewAny', User::class);
        $filters = $request->only(['search', 'is_active','name','email','phone','sort_dir']);
        $users = $this->userService->index($filters);
        return view('admin.users.index',compact('users'));
    }
    public function edit(User $user)
    {
        Gate::authorize('adminUpdate', User::class);
        return view('admin.users.edit', compact('user'));
    }
    public function update(Request $request, User $user)
    {
        Gate::authorize('adminUpdate', User::class);
        $validate = $request->validate([
            'is_active' => 'required|in:0,1',
        ]);
        $this->userService->update($validate, $user);
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
    public function destroy(User $user)
    {
        Gate::authorize('adminDelete', User::class);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
