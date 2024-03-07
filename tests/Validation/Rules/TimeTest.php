<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\Time;

/**
 * Class TimeTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class TimeTest extends LaravelTestCase
{
    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith [null]
     *           [""]
     *           ["0:00"]
     *           ["0:00:00"]
     *           ["0:00:00.00"]
     *           ["00:00"]
     *           ["00:00:00"]
     *           ["12:00"]
     *           ["12:00:00"]
     *           ["13:00"]
     *           ["13:00:00"]
     *           ["23:59"]
     *           ["23:59:59"]
     *           ["24:00"]
     *           ["12:00 am"]
     *           ["12:00 pm"]
     *           ["12:00:00 AM"]
     *           ["12:00:00 PM"]
     */
    public function passes(mixed $value)
    {
        $rule = new Time();

        $this->assertTrue($rule->passes('time', $value));
    }

    /**
     * @test
     *
     * Personally think these values should *not* be accepted, however, we want to allow anything that
     * Carbon::createFromTimeString() will successfully parse.
     *
     * @param string|null $value
     *
     * @testWith ["-1"]
     *           ["0"]
     *           ["13"]
     *           ["24"]
     *           ["24:01"]
     *           ["24:59"]
     *           ["24:00:01"]
     *           ["24:59:59"]
     */
    public function passesOdd(mixed $value)
    {
        $rule = new Time();

        $this->assertTrue($rule->passes('time', $value));
    }

    /**
     * @test
     *
     * Personally think these values *should* be accepted, however, we want to reject anything that
     * Carbon::createFromTimeString() will fail to parse.
     *
     * @param string|null $value
     *
     * @testWith ["12 am"]
     *           ["12 pm"]
     */
    public function failsOdd(mixed $value)
    {
        $rule = new Time();

        $this->assertFalse($rule->passes('time', $value));
    }

    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith ["@"]
     *           [" "]
     *           ["000:00"]
     *           ["0:00:00:00"]
     *           ["asdf"]
     *           ["25"]
     *           ["0 am"]
     *           ["0:00 AM"]
     *           ["0 pm"]
     *           ["0:00 PM"]
     *           ["13 am"]
     *           ["13 pm"]
     *           ["25:00"]
     *           ["25:00:01"]
     */
    public function fails(mixed $value)
    {
        $rule = new Time();

        $this->assertFalse($rule->passes('time', $value));
    }
}
