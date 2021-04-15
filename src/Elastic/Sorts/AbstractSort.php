<?php

namespace Snaccs\Elastic\Sorts;

use Illuminate\Contracts\Support\Arrayable;
use Snaccs\Elastic\Enums\Order;

/**
 * Class AbstractSort
 *
 * @package Snaccs\Elastic\Sorts
 */
abstract class AbstractSort implements Arrayable
{
    public string $order;

    /**
     * AbstractSort constructor.
     *
     * @param string      $field
     * @param string|null $order
     */
    public function __construct(
        public string $field,
        ?string $order = null
    ) {
        $this->order = $order ?? Order::ASC;
    }

    /**
     * @param string $order
     *
     * @return $this
     */
    public function order(string $order): static
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return $this
     */
    public function ascending(): static
    {
        $this->order = Order::ASC;

        return $this;
    }

    /**
     * @return $this
     */
    public function descending(): static
    {
        $this->order = Order::DESC;

        return $this;
    }
}
