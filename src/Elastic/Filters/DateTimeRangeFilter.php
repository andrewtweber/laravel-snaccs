<?php

namespace Snaccs\Elastic\Filters;

use Carbon\Carbon;

/**
 * Class DateTimeRangeFilter
 *
 * @package Snaccs\Elastic\Filters
 */
class DateTimeRangeFilter extends DateFilter
{
    /**
     * DateTimeRangeFilter constructor.
     *
     * @param Carbon $start
     * @param Carbon $end
     */
    public function __construct(
        public Carbon $start,
        public Carbon $end
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'range' => [
                'date_time_range' => [
                    'gte' => $this->start->toDateTimeString(),
                    'lte' => $this->end->toDateTimeString(),
                ],
            ],
        ];
    }
}
