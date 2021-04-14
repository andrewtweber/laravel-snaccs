<?php

namespace Snaccs\Tests\Elastic\Types;

use InvalidArgumentException;
use Snaccs\Elastic\Types\Coords;
use Snaccs\Tests\TestCase;

/**
 * Class CoordsTest
 *
 * @package Snaccs\Tests\Elastic\Types
 */
class CoordsTest extends TestCase
{
    /**
     * @test
     */
    public function invalid_min_latitude()
    {
        $coords = new Coords(-90, 0);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid latitude -90.01");

        $coords = new Coords(-90.01, 0);
    }

    /**
     * @test
     */
    public function invalid_max_latitude()
    {
        $coords = new Coords(90, 0);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid latitude 90.01");

        $coords = new Coords(90.01, 0);
    }

    /**
     * @test
     */
    public function invalid_min_longitude()
    {
        $coords = new Coords(0, -180);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid longitude -180.01");

        $coords = new Coords(0, -180.01);
    }

    /**
     * @test
     */
    public function invalid_max_longitude()
    {
        $coords = new Coords(0, 180);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid longitude 180.01");

        $coords = new Coords(0, 180.01);
    }

    /**
     * @test
     */
    public function to_array()
    {
        $coords = new Coords(41.8781, -87.6298);

        $this->assertSame([
            'lat' => 41.8781,
            'lon' => -87.6298,
        ], $coords->toArray());
    }

    /**
     * @test
     */
    public function to_simple_array()
    {
        $coords = new Coords(41.8781, -87.6298);

        $this->assertSame([-87.6298, 41.8781], $coords->toSimpleArray());
    }

    /**
     * @test
     */
    public function to_string()
    {
        $coords = new Coords(41.8781, -87.6298);
        $this->assertSame("41.8781,-87.6298", (string)$coords);

        $coords = new Coords(10.000001, 20);
        $this->assertSame("10.000001,20", (string)$coords);

        $coords = new Coords(10.0000001, 20);
        $this->assertSame("10,20", (string)$coords);
    }
}
