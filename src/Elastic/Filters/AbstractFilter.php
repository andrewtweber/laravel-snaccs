<?php

namespace Snaccs\Elastic\Filters;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class AbstractFilter
 *
 * @package Snaccs\Elastic\Filters
 */
abstract class AbstractFilter implements Arrayable
{
    /**
     * @return float
     */
    public function minScore(): float
    {
        return 0;
    }
}
