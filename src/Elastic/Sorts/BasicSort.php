<?php

namespace Snaccs\Elastic\Sorts;

/**
 * Class BasicSort
 *
 * @package Snaccs\Elastic\Sorts
 */
class BasicSort extends AbstractSort
{
    /**
     * BasicSort constructor.
     *
     * @param string $field
     * @param string $order
     */
    public function __construct(
        public string $field,
        public string $order = 'asc'
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            $this->field => [
                'order' => $this->order,
            ],
        ];
    }
}
