<?php

namespace Snaccs\Elastic\Sorts;

use Snaccs\Elastic\Types\Coords;

/**
 * Class DistanceSort
 *
 * @package Snaccs\Elastic\Sorts
 */
class DistanceSort extends AbstractSort
{
    // 'arc' = more accurate
    // 'plane' = faster
    public const TYPE_ARC = 'arc';
    public const TYPE_PLANE = 'plane';

    /**
     * DistanceSort constructor.
     *
     * @param Coords $coords
     * @param string $units
     */
    public function __construct(
        public Coords $coords,
        public string $units = 'miles'
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_geo_distance' => [
                'coords'          => (string)$this->coords,
                'order'           => $this->order,
                'unit'            => $this->units,
                'mode'            => 'min',
                'distance_type'   => static::TYPE_ARC,
                'ignore_unmapped' => true,
            ],
        ];
    }
}
