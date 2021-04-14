<?php

namespace Snaccs\Elastic\Filters;

use Carbon\Carbon;

/**
 * Class DateRangeFilter
 *
 * @package Snaccs\Elastic\Filters
 */
class DateRangeFilter extends DateFilter
{
    /**
     * DateRangeFilter constructor.
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
                'date_range' => [
                    'gte' => $this->start->toDateString(),
                    'lte' => $this->end->toDateString(),
                ],
            ],
        ];
    }
}
