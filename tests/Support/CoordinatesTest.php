<?php

namespace Snaccs\Tests\Support;

use Snaccs\Support\Coordinates;
use Snaccs\Support\DistanceUnit;
use Snaccs\Tests\TestCase;

/**
 * Class CoordinatesTest
 *
 * @package Snaccs\Tests\Support
 */
class CoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function string_casting()
    {
        // String cast shows whole numbers as integers
        $coords = new Coordinates(10, -20);
        $this->assertSame('10,-20', (string)$coords);

        // String cast trims trailing 0s in decimal places
        $coords = new Coordinates(10.000, -20.000);
        $this->assertSame('10,-20', (string)$coords);

        // String cast trims trailing 0s in decimal places
        $coords = new Coordinates('10.200', '-20.130'); // @phpstan-ignore-line argument.type
        $this->assertSame('10.2,-20.13', (string)$coords);

        // Rounds to 6th decimal place and then truncates trailing zeroes
        $coords = new Coordinates(100.0000001, -49.9999999);
        $this->assertEquals("100,-50", (string)$coords);

        // Rounds to 6th decimal place, nothing to truncate
        $coords = new Coordinates(100.000001, -49.999999);
        $this->assertEquals("100.000001,-49.999999", (string)$coords);

        // Rounds down to 6 decimal points
        $coords = new Coordinates(10.123456489, -20.123456489);
        $this->assertSame('10.123456,-20.123456', (string)$coords);

        // Rounds up to 6 decimal points
        $coords = new Coordinates(10.123456789, -20.123456789);
        $this->assertSame('10.123457,-20.123457', (string)$coords);
    }

    /**
     * @test
     */
    public function array_conversion()
    {
        $coords = new Coordinates(10, -20);
        $this->assertSame([-20.0, 10.0], $coords->toPair());
        $this->assertSame(['lat' => 10.0, 'lon' => -20.0], $coords->toArray());

        $coords = new Coordinates(10.123456489, -20.123456489);
        $this->assertSame([-20.123456489, 10.123456489], $coords->toPair());
        $this->assertSame(['lat' => 10.123456489, 'lon' => -20.123456489], $coords->toArray());
    }

    /**
     * @test
     */
    public function distance_calculation()
    {
        // Sears Tower
        $start = new Coordinates(41.8789, -87.6359);
        // Hancock Tower
        $end = new Coordinates(41.8988, -87.6229);

        // Distance in miles = 1.5289
        $this->assertEquals(1.53, round($start->distanceFrom($end), 2));
        $this->assertEquals(1.53, round($start->distanceFrom($end, DistanceUnit::Miles), 2));

        // Distance in km = 2.4606
        $this->assertEquals(2.46, round($start->distanceFrom($end, DistanceUnit::Kilometers), 2));

        // Distance in meters = 2,460.572
        $this->assertEquals(2461, round($start->distanceFrom($end, DistanceUnit::Meters)));
    }
}
