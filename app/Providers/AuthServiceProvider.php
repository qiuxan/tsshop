<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
//         'App\Models\Order' => 'App\Policies\OrderPolicy',

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //

        // Use Gate::guessPolicyNamesUsing function to find the customised policy
        Gate::guessPolicyNamesUsing(function ($class) {
            //
            //class_basename is a laravel default helper functions to ge the short name of a class
            // eg: give it class  \App\Models\User 会and it will return User
            return '\\App\\Policies\\'.class_basename($class).'Policy';
        });
    }
}
