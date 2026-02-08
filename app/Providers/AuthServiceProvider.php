<?php

namespace App\Providers;

use App\Models\ActuatorCommand;
use App\Models\AutoRule;
use App\Models\Farm;
use App\Models\IotDevice;
use App\Models\SensorData;
use App\Models\User;
use App\Policies\ActuatorCommandPolicy;
use App\Policies\AutoRulePolicy;
use App\Policies\FarmPolicy;
use App\Policies\IotDevicePolicy;
use App\Policies\SensorDataPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Farm::class => FarmPolicy::class,
        IotDevice::class => IotDevicePolicy::class,
        SensorData::class => SensorDataPolicy::class,
        ActuatorCommand::class => ActuatorCommandPolicy::class,
        AutoRule::class => AutoRulePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate for Super Admin
        Gate::define('isSuperAdmin', function(User $user) {
            return $user->user_role_id == 1;
        });

        // Gate for Farm Owner
        Gate::define('isFarmOwner', function(User $user) {
            return $user->user_role_id == 2;
        });

        // Gate for Farm Worker
        Gate::define('isFarmWorker', function(User $user) {
            return $user->user_role_id == 3;
        });

        // Gate for Farm Owner or Super Admin (convenience gate)
        Gate::define('isOwnerOrAdmin', function(User $user) {
            return in_array($user->user_role_id, [1, 2]);
        });
    }
}
