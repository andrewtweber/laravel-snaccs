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
        $source = realpath(__DIR__.'/../../config/formatting.php');

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$source => config_path('formatting.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('formatting');
        }

        $this->mergeConfigFrom($source, 'formatting');
    }
}
