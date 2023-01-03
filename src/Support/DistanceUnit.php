<?php

namespace Snaccs\Support;

use PhpGeoMath\Model\Polar3dPoint;

/**
 * Enum DistanceUnit
 *
 * @package Snaccs\Support
 */
enum DistanceUnit: string
{
    case Miles = 'miles';
    case Kilometers = 'kilometers';
    case Meters = 'meters';

    /**
     * @return float
     */
    public function radius(): float
    {
        return match ($this) {
            self::Miles => Polar3dPoint::EARTH_RADIUS_IN_MILES,
            self::Kilometers => Polar3dPoint::EARTH_RADIUS_IN_METERS / 1000,
            self::Meters => Polar3dPoint::EARTH_RADIUS_IN_METERS,
        };
    }

    /**
     * @return string
     */
    public function suffix(): string
    {
        return match ($this) {
            self::Miles => 'mi',
            self::Kilometers => 'km',
            self::Meters => 'm',
        };
    }
}
