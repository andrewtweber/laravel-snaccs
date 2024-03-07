<?php

namespace Snaccs\Tests\Casts;

use Snaccs\Casts\Time;
use Snaccs\Tests\TestCase;

/**
 * Class TimeTest
 *
 * @package Snaccs\Tests\Casts
 */
class TimeTest extends TestCase
{
    /**
     * @test
     */
    public function get_value()
    {
        $cast = new Time();

        $this->assertNull($cast->get(null, "", null, []));
        $this->assertSame("12:00:00", $cast->get(null, "", "12:00:00", []));
    }

    /**
     * @test
     */
    public function get_value_with_format()
    {
        $cast = new Time('H:i');

        $this->assertNull($cast->get(null, "", null, []));
        $this->assertSame("12:00", $cast->get(null, "", "12:00:00", []));
    }

    /**
     * @test
     *
     * @param string|null $time
     * @param string|null $expected
     *
     * @testWith [null,          null]
     *           ["0:00",        "00:00:00"]
     *           ["12:00",       "12:00:00"]
     *           ["12:00 am",    "00:00:00"]
     *           ["12:00 pm",    "12:00:00"]
     *           ["11:00:00 am", "11:00:00"]
     *           ["11:00:00 pm", "23:00:00"]
     *           ["1:23:45",     "01:23:45"]
     */
    public function set_value(?string $time, ?string $expected)
    {
        $cast = new Time();

        $this->assertSame($expected, $cast->set(null, "", $time, []));
    }
}
