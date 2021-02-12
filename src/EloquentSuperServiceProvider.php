<?php

namespace Dive\EloquentSuper;

use Illuminate\Support\ServiceProvider;

class EloquentSuperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/eloquent-super.php' => config_path('eloquent-super.php'),
            ], 'config');
        }
    }
}
