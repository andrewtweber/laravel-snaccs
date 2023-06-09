<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\Longitude;

/**
 * Class LongitudeTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class LongitudeTest extends LaravelTestCase
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
     *           ["-180"]
     *           [-180]
     *           ["-179.99"]
     *           [-179.99]
     *           ["180"]
     *           [180]
     *           [12.515]
     *           [-34.7137259014]
     */
    public function passes(mixed $value)
    {
        $rule = new Longitude();

        $this->assertTrue($rule->passes('longitude', $value));
    }

    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith ["@"]
     *           [" "]
     *           ["-180.01"]
     *           [-180.01]
     *           ["180.01"]
     *           [180.01]
     *           ["asdf"]
     *           [200.6235156]
     *           [-232.550]
     */
    public function fails(mixed $value)
    {
        $rule = new Longitude();

        $this->assertFalse($rule->passes('longitude', $value));
    }
}
