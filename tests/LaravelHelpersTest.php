<?php

namespace Snaccs\Tests;

use Illuminate\Support\Facades\Queue;

/**
 * Class LaravelHelpersTest
 *
 * @package Snaccs\Tests
 */
class LaravelHelpersTest extends LaravelTestCase
{
    /**
     * @test
     */
    public function dispatch_with_delay()
    {
        Queue::fake();

        dispatch_with_delay(new TestJob(), 15, 0);

        Queue::assertPushed(TestJob::class, function ($job) {
            dd($job);
        });
    }
}
