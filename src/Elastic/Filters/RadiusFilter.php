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
     */
    public function __construct(
        public string $field,
        public Coords $coords,
        public $radius,
        public string $units = 'miles'
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'geo_distance' => [
                'distance' => $this->radius . $this->units,
                'coords'   => $this->coords->toArray(),
            ],
        ];
    }
}
