<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Address;
use App\Enum\UserRoleEnum;
use App\Exceptions\GeneralException;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class AddressPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address): bool
    {
        if($user->id === $address->user_id){
            return true;
        }
        throw new GeneralException('You are not authorized to update this address', 403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): bool
    {
        if($user->id === $address->user_id){
            return true;
        }
        throw new GeneralException('You are not authorized to delete this address', 403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Address $address): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Address $address): bool
    {
        return false;
    }
}
