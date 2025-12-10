<?php

namespace App\Policies;

use App\Models\Design;
use App\Models\User;
use App\Exceptions\GeneralException;
use Illuminate\Auth\Access\Response;

class DesignPolicy
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
    public function view(User $user, Design $design): bool
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
    public function update(User $user, Design $design): bool
    {
        if($design->user_id === $user->id){
            return true;
        }
        throw new GeneralException('You are not authorized to update this design', 403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Design $design): bool
    {
        if($design->user_id === $user->id){
            return true;
        }
        throw new GeneralException('You are not authorized to delete this design', 403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Design $design): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Design $design): bool
    {
        return false;
    }
}
