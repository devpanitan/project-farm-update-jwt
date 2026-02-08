<?php

namespace App\Policies;

use App\Models\ActuatorCommand;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActuatorCommandPolicy
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
        // Do not allow fetching all commands in the system at once.
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActuatorCommand $actuatorCommand): bool
    {
        // User can view the command if they are a member of the farm 
        // where the associated device is registered.
        return $user->isMemberOfFarm($actuatorCommand->iotDevice->farm_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only farm owners can issue new commands.
        return $user->isFarmOwner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActuatorCommand $actuatorCommand): bool
    {
        // Commands are records of action and should be immutable.
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActuatorCommand $actuatorCommand): bool
    {
        // Commands should not be deleted to maintain a historical log.
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActuatorCommand $actuatorCommand): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ActuatorCommand $actuatorCommand): bool
    {
        return false;
    }
}
