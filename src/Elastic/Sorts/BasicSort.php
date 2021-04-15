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
