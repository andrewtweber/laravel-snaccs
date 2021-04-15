<?php

namespace Snaccs\Elastic\Filters;

/**
 * Class RangeFilter
 *
 * @package Snaccs\Elastic\Filters
 */
abstract class RangeFilter extends AbstractFilter
{
    public const OPEN       = 'OPEN';       // >  start && <  end
    public const CLOSED     = 'CLOSED';     // >= start && <= end
    public const LEFT_OPEN  = 'LEFT_OPEN';  // >  start && <= end
    public const RIGHT_OPEN = 'RIGHT_OPEN'; // >= start && <  end

    public string $interval;

    /**
     * RangeFilter constructor.
     *
     * @param string      $field
     * @param mixed       $start
     * @param mixed       $end
     * @param string|null $interval
     */
    public function __construct(
        public string $field,
        public mixed $start,
        public mixed $end,
        ?string $interval = null
    ) {
        $this->interval = $interval ?? static::CLOSED;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $left_op = match ($this->interval) {
            static::CLOSED, static::LEFT_OPEN => 'gt',
            default => 'gte'
        };
        $right_op = match ($this->interval) {
            static::CLOSED, static::RIGHT_OPEN => 'lt',
            default => 'lte'
        };

        return [
            'range' => [
                $this->field => [
                    $left_op  => $this->start,
                    $right_op => $this->end,
                ],
            ],
        ];
    }
}
