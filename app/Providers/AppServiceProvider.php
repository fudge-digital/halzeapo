<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Hanya role MARKETING yang bisa membuat Purchase Order
        Gate::define('create-po', function ($user) {
            return $user->role === 'MARKETING';
        });

        // Role MARKETING & FINANCE bisa lihat detail PO
        Gate::define('view-po-detail', function ($user) {
            return in_array($user->role, ['MARKETING', 'FINANCE']);
        });

        Gate::define('finance-actions', function ($user) {
            return $user->role === 'FINANCE';
        });

        // Gate untuk Production
        Gate::define('production-actions', function ($user) {
            return $user->role === 'PRODUKSI';
        });

        // Gate untuk Shipping
        Gate::define('shipping-actions', function ($user) {
            return $user->role === 'SHIPPER';
        });
    }
}
