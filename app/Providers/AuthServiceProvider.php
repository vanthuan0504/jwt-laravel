<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        Gate::define('isAdmin', function(User $user) {
            return $user->role === 'ADMIN';
        });
        Gate::define('isSupervisor', function(User $user) {
            return $user->role === 'SUPERVISOR';
        });
        Gate::define('isStaff', function(User $user) {
            return $user->role === 'STAFF';
        });
    }
}
