<?php

namespace App\Policies;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class FarmPolicy
{
    /**
     * Always grant access to Super Admins.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->can('isSuperAdmin')) {
            return true;
        }
        return null; // Let other policy methods decide
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can attempt to view farms,
        // the controller will scope the query.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Farm $farm): bool
    {
        // User can view the farm if they are a member of it.
        return $user->farms->contains($farm);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Super Admins or Farm Owners can create farms.
        return Gate::allows('isOwnerOrAdmin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Farm $farm): bool
    {
        // User can update the farm if they are a Farm Owner and a member of the farm.
        return $user->user_role_id == 2 && $user->farms->contains($farm);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Farm $farm): bool
    {
        // Only Super Admins can delete, which is handled by the before() method.
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Farm $farm): bool
    {
        // Only Super Admins can restore, which is handled by the before() method.
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Farm $farm): bool
    {
         // Only Super Admins can force delete, which is handled by the before() method.
        return false;
    }
}
