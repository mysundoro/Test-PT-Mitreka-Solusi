<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Configuration;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Superadmin') ? true : null;
        });

        // Check if the configuration table exists before querying it
        try {
            // Check if the configurations table exists before querying it
            if (Schema::hasTable('configurations')) {
                $configuration = Configuration::where('key', 'timezone')->first();

                if ($configuration && $configuration->value) {
                    date_default_timezone_set($configuration->value);
                }
            }
        } catch (\Exception $e) {
            // Handle exception or log the error if necessary
        }
    }
}
