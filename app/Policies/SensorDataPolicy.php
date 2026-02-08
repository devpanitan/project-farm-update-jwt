<?php

namespace App\Policies;

use App\Models\SensorData;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SensorDataPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Super Admins can do anything, including deleting.
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
        // Allow any authenticated user to attempt to view sensor data.
        // The controller will then scope the query to only the farms they have access to.
        // If they have no farms, the query will correctly return an empty result.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SensorData $sensorData): bool
    {
        // User can view the sensor data if they are a member of the farm
        // that the associated IoT device belongs to.
        // We need to access the farm through the iotDevice relationship.
        return $user->isMemberOfFarm($sensorData->iotDevice->farm_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // For manual data entry, only Farm Owners should be able to create sensor data.
        // Automatic ingestion from devices might use a different authentication mechanism (e.g., API keys).
        return $user->isFarmOwner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SensorData $sensorData): bool
    {
        // Sensor data should be immutable. No one should be able to update it.
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SensorData $sensorData): bool
    {
        // Only Super Admins can delete sensor data, which is handled by the before() method.
        // Regular users, including Farm Owners, cannot delete historical data.
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SensorData $sensorData): bool
    {
        // Only Super Admins can restore, handled by before().
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SensorData $sensorData): bool
    {
        // Only Super Admins can permanently delete, handled by before().
        return false;
    }
}
