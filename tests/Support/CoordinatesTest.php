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
    public function formatters()
    {
        $coords = new Coordinates(10, -20);

        // String cast shows whole numbers as integers
        $this->assertSame('10,-20', (string)$coords);
        $this->assertSame([-20.0, 10.0], $coords->toPair());
        $this->assertSame(['lat' => 10.0, 'lon' => -20.0], $coords->toArray());

        $coords = new Coordinates(10.000, -20.000);

        // String cast trims trailing 0s in decimal places
        $this->assertSame('10,-20', (string)$coords);
        $this->assertSame([-20.0, 10.0], $coords->toPair());
        $this->assertSame(['lat' => 10.0, 'lon' => -20.0], $coords->toArray());

        $coords = new Coordinates('10.200', '-20.130');

        // String cast trims trailing 0s in decimal places
        $this->assertSame('10.2,-20.13', (string)$coords);
        $this->assertSame([-20.13, 10.2], $coords->toPair());
        $this->assertSame(['lat' => 10.2, 'lon' => -20.13], $coords->toArray());

        $coords = new Coordinates(10.123456489, -20.123456489);

        // String cast is rounded down to 6 decimal points
        $this->assertSame('10.123456,-20.123456', (string)$coords);
        $this->assertSame([-20.123456489, 10.123456489], $coords->toPair());
        $this->assertSame(['lat' => 10.123456489, 'lon' => -20.123456489], $coords->toArray());

        $coords = new Coordinates(10.123456789, -20.123456789);

        // String cast is rounded up to 6 decimal points
        $this->assertSame('10.123457,-20.123457', (string)$coords);
        $this->assertSame([-20.123456789, 10.123456789], $coords->toPair());
        $this->assertSame(['lat' => 10.123456789, 'lon' => -20.123456789], $coords->toArray());
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
