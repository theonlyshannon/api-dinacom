<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\AuthRepositoryInterface;
use App\Repositories\AuthRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    }




    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
