<?php

namespace App\Providers;

use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        // Horizon authentication is dealt with by the web admin guard
        Gate::define('viewHorizon', function (User $user) {
            return $user->hasRole(RoleKeys::WEB_TEAM);
        });
    }
}
