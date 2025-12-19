<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Address;
use App\Models\OrderItems;
use App\Policies\UserPolicy;
use App\Policies\AddressPolicy;
use App\Policies\OrderItemPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(OrderItems::class, OrderItemPolicy::class);
        User::created(function ($user) {
            $user->wallet()->create(['balance' => 0]);
        });

    }
}
