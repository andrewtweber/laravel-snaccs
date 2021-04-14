<?php

namespace Snaccs\Elastic\Sorts;

/**
 * Class BestMatch
 *
 * @package Snaccs\Elastic\Sorts
 */
class BestMatch extends AbstractSort
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_score' => [
                'order' => 'desc',
            ],
        ];
    }
}
