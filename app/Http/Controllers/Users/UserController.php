<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $user = $this->userService->show();
        return $this->success(new UserResource($user),  'User retrieved successfully',200);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->userService->update($request->validated());
        return $this->success(new UserResource($user),  'User updated successfully',200);
    }

    public function destroy()
    {
        $this->userService->destroy();
        return $this->success(null, 'User deleted successfully', 200);
    }

}
