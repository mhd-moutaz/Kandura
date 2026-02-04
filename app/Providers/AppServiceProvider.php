<?php

namespace App\Providers;

use App\Models\Design;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItems;
use App\Observers\DesignObserver;
use App\Observers\OrderObserver;
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

        // Observe Design model
        Design::observe(DesignObserver::class);

        // Observe Order model
        Order::observe(OrderObserver::class);

        User::created(function ($user) {
            $user->wallet()->create(['balance' => 0]);
        });

    }
}
