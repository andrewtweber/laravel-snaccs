<?php

namespace Snaccs\Elastic\Sorts;

use Snaccs\Elastic\Enums\Order;

/**
 * Class BestMatch
 *
 * @package Snaccs\Elastic\Sorts
 */
class BestMatch extends BasicSort
{
    /**
     * BestMatch constructor.
     */
    public function __construct()
    {
        parent::__construct('_score', Order::DESC);
    }
}
