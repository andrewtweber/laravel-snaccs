<?php

namespace Tests;

class HelpersTest extends TestCase
{
    /**
     * @test
     *
     * @param int|null $input
     * @param string   $expected
     *
     * @testWith [null, null]
     *           [0, "0th"]
     *           [1, "1st"]
     *           [2, "2nd"]
     *           [3, "3rd"]
     *           [4, "4th"]
     *           [5, "5th"]
     *           [6, "6th"]
     *           [7, "7th"]
     *           [8, "8th"]
     *           [9, "9th"]
     *           [10, "10th"]
     *           [11, "11th"]
     *           [12, "12th"]
     *           [13, "13th"]
     *           [14, "14th"]
     *           [21, "21st"]
     *           [22, "22nd"]
     *           [23, "23rd"]
     *           [24, "24th"]
     *           [100, "100th"]
     *           [101, "101st"]
     *           [202, "202nd"]
     *           [303, "303rd"]
     *           [404, "404th"]
     *           [511, "511th"]
     *           [612, "612th"]
     *           [1013, "1013th"]
     *           [2014, "2014th"]
     *           [3121, "3121st"]
     */
    public function ordinal($input, $expected)
    {
        $this->assertSame($expected, ordinal($input));
    }
}
