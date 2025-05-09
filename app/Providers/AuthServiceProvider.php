<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Example if you add policies later
        // \App\Models\Model::class => \App\Policies\ModelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define custom Gates based on role
        // 0 = Instructor, 1 = Chairperson, 2 = Dean, 3 = Admin, 4 = GE Coordinator
        Gate::define('admin', fn($user) => $user->role === 3);
        Gate::define('chairperson', fn($user) => $user->role === 1);
        Gate::define('dean', fn($user) => $user->role === 2);
        Gate::define('instructor', fn($user) => $user->role === 0);
        Gate::define('ge_coordinator', fn($user) => $user->role === 4);
        
        // Combined gates
        Gate::define('admin-chair', function ($user) {
            return in_array($user->role, [1, 2]); // Chairperson or Dean
        });
        
        // GE Coordinator has similar permissions to chairperson but only for GE subjects
        Gate::define('ge-chair', function ($user) {
            return $user->role === 4 || $user->role === 1; // GE Coordinator or Chairperson
        });
    }
}
