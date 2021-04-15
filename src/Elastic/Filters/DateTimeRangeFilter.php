<?php

namespace Snaccs\Elastic\Filters;

use Carbon\Carbon;

/**
 * Class DateTimeRangeFilter
 *
 * @package Snaccs\Elastic\Filters
 */
class DateTimeRangeFilter extends RangeFilter
{
    /**
     * DateTimeRangeFilter constructor.
     *
     * @param string $field
     * @param Carbon $start
     * @param Carbon $end
     */
    public function __construct(string $field, Carbon $start, Carbon $end)
    {
        parent::__construct($field, $start->toDateTimeString(), $end->toDateTimeString());
    }
}
