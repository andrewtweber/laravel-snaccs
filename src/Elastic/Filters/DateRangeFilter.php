<?php

namespace Snaccs\Elastic\Filters;

use Carbon\Carbon;

/**
 * Class DateRangeFilter
 *
 * @package Snaccs\Elastic\Filters
 */
class DateRangeFilter extends RangeFilter
{
    /**
     * DateRangeFilter constructor.
     *
     * @param string $field
     * @param Carbon $start
     * @param Carbon $end
     */
    public function __construct(string $field, Carbon $start, Carbon $end)
    {
        parent::__construct($field, $start->toDateString(), $end->toDateString());
    }
}
