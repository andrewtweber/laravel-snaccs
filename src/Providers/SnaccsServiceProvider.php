<?php

namespace Snaccs\Providers;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * Class SnaccsServiceProvider
 *
 * @package Snaccs\Providers
 */
class SnaccsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $source = realpath(__DIR__.'/../../config/money.php');

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$source => config_path('money.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('money');
        }

        $this->mergeConfigFrom($source, 'money');
    }
}
