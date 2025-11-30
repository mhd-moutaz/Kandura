<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Services\Users\UserService;
use App\Http\Resources\Users\UserResource;
use App\Http\Requests\Users\UpdateProfileRequest;



class UserController extends Controller
{
    protected $userService;
    public function __construct( UserService $userService)
    {
        $this->userService = $userService;
    }
    public function show()
    {
        Gate::authorize('view', User::class);
        $user = $this->userService->show();
        return $this->success(new UserResource($user),  'User retrieved successfully',200);
    }

    public function update(UpdateProfileRequest $request)
    {
        Gate::authorize('update', User::class);
        $user = $this->userService->update($request->validated());
        return $this->success(new UserResource($user),  'User updated successfully',200);
    }

    public function destroy()
    {
        Gate::authorize('delete', User::class);
        $this->userService->destroy();
        return $this->success(null, 'User deleted successfully', 200);
    }

}
