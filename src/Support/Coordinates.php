<?php

namespace Snaccs\Support;

use PhpGeoMath\Model\Polar3dPoint;

/**
 * Class Coordinates
 *
 * @package Snaccs\Support
 */
class Coordinates
{
    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
    }

    /**
     * @param Coordinates  $other
     * @param DistanceUnit $unit
     *
     * @return float
     */
    public function distanceFrom(Coordinates $other, DistanceUnit $unit = DistanceUnit::Miles): float
    {
        $point1 = new Polar3dPoint($this->latitude, $this->longitude, $unit->radius());
        $point2 = new Polar3dPoint($other->latitude, $other->longitude, $unit->radius());

        return $point2->calcGeoDistanceToPoint($point1);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'lat' => $this->latitude,
            'lon' => $this->longitude,
        ];
    }

    /**
     * @return array
     */
    public function toPair(): array
    {
        return [$this->longitude, $this->latitude];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return rtrim(rtrim(number_format($this->latitude, 6), '0'), '.') . ',' .
            rtrim(rtrim(number_format($this->longitude, 6), '0'), '.');
    }
}
