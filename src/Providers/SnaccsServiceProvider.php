<?php

namespace Snaccs\Providers;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Snaccs\Elastic\Elastic;

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
        $formatting = realpath(__DIR__.'/../../config/formatting.php');
        $system = realpath(__DIR__.'/../../config/system.php');

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([
                $formatting => config_path('formatting.php'),
                $system => config_path('system.php'),
            ]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('formatting');
            $this->app->configure('system');
        }

        $this->mergeConfigFrom($formatting, 'formatting');
        $this->mergeConfigFrom($system, 'system');

        $this->app->instance(Elastic::class, new Elastic());
    }
}
