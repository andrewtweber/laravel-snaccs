<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\Latitude;

/**
 * Class LatitudeTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class LatitudeTest extends LaravelTestCase
{
    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith [null]
     *           [""]
     *           ["0"]
     *           ["0.000"]
     *           [0]
     *           ["-90"]
     *           [-90]
     *           ["-89.99"]
     *           [-89.99]
     *           ["90"]
     *           [90]
     *           [12.515]
     *           [-34.7137259014]
     */
    public function passes(mixed $value)
    {
        $rule = new Latitude();

        $this->assertTrue($rule->passes('latitude', $value));
    }

    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith ["@"]
     *           [" "]
     *           ["-90.01"]
     *           [-90.01]
     *           ["90.01"]
     *           [90.01]
     *           ["asdf"]
     *           [100.6235156]
     *           [-132.550]
     */
    public function fails(mixed $value)
    {
        $rule = new Latitude();

        $this->assertFalse($rule->passes('latitude', $value));
    }
}
