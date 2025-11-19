<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Tu podstaw swoje mapowanie modeli do polityk (jeśli masz)
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
