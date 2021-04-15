<?php

namespace Snaccs\Elastic\Filters;

use Snaccs\Elastic\Types\Coords;

/**
 * Class RadiusFilter
 *
 * @package Snaccs\Elastic\Filters
 */
class RadiusFilter extends AbstractFilter
{
    /**
     * RadiusFilter constructor.
     *
     * @param Coords $coords
     * @param float  $radius
     * @param string $units
     * @param string $field
     */
    public function __construct(
        public string $field,
        public Coords $coords,
        public float $radius,
        public string $units = 'miles',
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'geo_distance' => [
                'distance'   => $this->radius . $this->units,
                $this->field => $this->coords->toArray(),
            ],
        ];
    }
}
