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
        $formatting = realpath(__DIR__ . '/../../config/formatting.php');
        $system = realpath(__DIR__ . '/../../config/system.php');
        $views = realpath(__DIR__ . '/../../resources/views');

        if ($this->app instanceof LaravelApplication) {
            $this->loadViewsFrom($views, 'snaccs');

            $this->publishes([
                $formatting => config_path('formatting.php'),
                $system     => config_path('system.php'),
                $views      => resource_path('views/vendor/snaccs'),
            ]);
        /* @phpstan-ignore-next-line */
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('formatting');
            $this->app->configure('system');
        }

        $this->mergeConfigFrom($formatting, 'formatting');
        $this->mergeConfigFrom($system, 'system');
    }
}
