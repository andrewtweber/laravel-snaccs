<?php

namespace Snaccs\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Snaccs\Providers\SnaccsServiceProvider;

/**
 * Class LaravelTestCase
 *
 * @package Snaccs\Tests
 */
abstract class LaravelTestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return [
            SnaccsServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
    }
}
