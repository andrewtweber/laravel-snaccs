<?php

namespace Snaccs\Elastic\Types;

use InvalidArgumentException;

/**
 * Class Coords
 *
 * @package Snaccs\Elastic\Types
 */
class Coords extends AbstractType
{
    /**
     * Coords constructor.
     *
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct(
        public float $latitude,
        public float $longitude
    ) {
        assert($latitude >= -90 && $latitude <= 90, new InvalidArgumentException("Invalid latitude {$latitude}"));
        assert($longitude >= -180 && $longitude <= 180, new InvalidArgumentException("Invalid longitude {$longitude}"));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'lat' => $this->latitude,
            'lon' => $this->longitude,
        ];
    }

    /**
     * @return array
     */
    public function toSimpleArray(): array
    {
        return [$this->longitude, $this->latitude];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return rtrim(number_format($this->latitude, 6), '0')
            . ',' . rtrim(number_format($this->longitude, 6), '0');
    }
}
