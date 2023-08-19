<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use App\Repositories\Interfaces\CategoryRepositoryInterface;
// use App\Repositories\CategoryRepositoryClass;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->bind(CategoryRepositoryInterface::class, CategoryRepositoryClass::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
