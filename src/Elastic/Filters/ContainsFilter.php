<?php

namespace Snaccs\Elastic\Filters;

use Snaccs\Elastic\Types\Coords;

/**
 * Class ContainsFilter
 *
 * @package Snaccs\Elastic\Filters
 */
class ContainsFilter extends AbstractFilter
{
    /**
     * ContainsFilter constructor.
     *
     * @param string $field
     * @param Coords $coords
     */
    public function __construct(
        public string $field,
        public Coords $coords
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'geo_shape' => [
                $this->field => [
                    'shape'    => [
                        'type'        => 'point',
                        'coordinates' => $this->coords->toSimpleArray(),
                    ],
                    'relation' => 'contains',
                ],
            ],
        ];
    }
}
