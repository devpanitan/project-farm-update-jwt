<?php

namespace App\Policies;

use App\Models\IotDevice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IotDevicePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Super Admins can do anything.
        if ($user->isSuperAdmin()) {
            return true;
        }
 
        return null; // let other methods decide
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only Super Admins can view a raw list of all devices.
        // Other users should get devices through the farm they have access to.
        return false; 
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IotDevice $iotDevice): bool
    {
        // User can view the device if they are a member of the farm it belongs to.
        return $user->isMemberOfFarm($iotDevice->farm_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Farm Owners can create devices. Super Admin is handled by before().
        return $user->isFarmOwner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IotDevice $iotDevice): bool
    {
        // User can update if they are a Farm Owner of the farm the device belongs to.
        return $user->isFarmOwner() && $user->isMemberOfFarm($iotDevice->farm_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IotDevice $iotDevice): bool
    {
        // User can delete if they are a Farm Owner of the farm the device belongs to.
        return $user->isFarmOwner() && $user->isMemberOfFarm($iotDevice->farm_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, IotDevice $iotDevice): bool
    {
        // Logic for restoring a soft-deleted device.
        return $user->isFarmOwner() && $user->isMemberOfFarm($iotDevice->farm_id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, IotDevice $iotDevice): bool
    {
        // Logic for permanently deleting a device.
        return $user->isFarmOwner() && $user->isMemberOfFarm($iotDevice->farm_id);
    }
}
