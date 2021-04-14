<?php

namespace Snaccs\Elastic\Sorts;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class AbstractSort
 *
 * @package Snaccs\Elastic\Sorts
 */
abstract class AbstractSort implements Arrayable
{
    public string $order = 'asc';

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
        $this->order = 'asc';

        return $this;
    }

    /**
     * @return $this
     */
    public function descending(): static
    {
        $this->order = 'desc';

        return $this;
    }
}
