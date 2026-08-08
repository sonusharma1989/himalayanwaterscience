<?php

namespace Hws\Storefront\Providers;

use Illuminate\Support\ServiceProvider;

class StorefrontServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/api-routes.php');
    }
}
