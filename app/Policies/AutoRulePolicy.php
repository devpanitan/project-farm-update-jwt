<?php

namespace App\Policies;

use App\Models\AutoRule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AutoRulePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Users should query rules based on a specific farm or device.
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AutoRule $autoRule): bool
    {
        // User can view the rule if they are a member of the farm 
        // where the associated device is registered.
        return $user->isMemberOfFarm($autoRule->iotDevice->farm_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only farm owners can create new auto rules.
        return $user->isFarmOwner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AutoRule $autoRule): bool
    {
        // Only the owner of the farm can update a rule.
        return $user->isOwnerOfFarm($autoRule->iotDevice->farm_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AutoRule $autoRule): bool
    {
        // Only the owner of the farm can delete a rule.
        return $user->isOwnerOfFarm($autoRule->iotDevice->farm_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AutoRule $autoRule): bool
    {
        return $user->isOwnerOfFarm($autoRule->iotDevice->farm_id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AutoRule $autoRule): bool
    {
        return $user->isOwnerOfFarm($autoRule->iotDevice->farm_id);
    }
}
