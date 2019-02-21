<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Laravel\Passport;
// use App\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Test database connection
        // try {
        //     DB::connection()->getPdo();
        // } catch (\Exception $e) {
        //     die("Could not connect to the database.  Please check your configuration. error:" . $e );
        // }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Passport::ignoreMigrations();
    }
}
