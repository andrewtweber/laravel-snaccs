<?php

namespace Snaccs\Elastic\Sorts;

use Snaccs\Elastic\Enums\DistanceType;
use Snaccs\Elastic\Enums\Order;
use Snaccs\Elastic\Types\Coords;

/**
 * Class DistanceSort
 *
 * @package Snaccs\Elastic\Sorts
 */
class DistanceSort extends AbstractSort
{
    /**
     * DistanceSort constructor.
     *
     * @param Coords $coords
     * @param string $units
     * @param string $field
     */
    public function __construct(
        public string $field,
        public Coords $coords,
        public string $units = 'miles',
    ) {
        parent::__construct($field, Order::ASC);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_geo_distance' => [
                $this->field      => (string)$this->coords,
                'order'           => $this->order,
                'unit'            => $this->units,
                'mode'            => 'min',
                'distance_type'   => DistanceType::ARC,
                'ignore_unmapped' => true,
            ],
        ];
    }
}
