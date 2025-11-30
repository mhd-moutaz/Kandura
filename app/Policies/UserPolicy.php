<?php

namespace App\Policies;

use App\Enum\UserRoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use function Symfony\Component\Translation\t;

class UserPolicy
{
    use HandlesAuthorization,AuthorizesRequests;

    public function viewAny(User $user): bool
    {
        return false;
    }
    public function adminViewAny(User $user): bool
    {
        return $user->hasRole(UserRoleEnum::ADMIN);
    }
    public function view(User $user): bool
    {
        return $user->hasRole(UserRoleEnum::USER);
    }
    public function create(User $user): bool
    {
        return false;
    }
    public function update(User $user): bool
    {
        return $user->hasRole(UserRoleEnum::USER);
    }
    public function adminUpdate(User $user): bool
    {
        return $user->hasRole(UserRoleEnum::ADMIN);
    }
    public function delete(User $user): bool
    {
        return $user->hasRole(UserRoleEnum::USER);
    }
    public function adminDelete(User $user): bool
    {
        return $user->hasRole(UserRoleEnum::ADMIN);
    }

}
