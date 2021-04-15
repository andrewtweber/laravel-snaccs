<?php

namespace Snaccs\Elastic;

use Illuminate\Http\Request;

/**
 * Class Config
 *
 * @package Snaccs\Elastic
 */
class Config
{
    public array $default_sorts = [];

    public array $filters = [];

    /**
     * Config constructor.
     *
     * @param int   $offset
     * @param int   $per_page
     * @param float $min_score
     */
    public function __construct(
        public int $offset = 0,
        public int $per_page = 10,
        public float $min_score = 0
    ) {
    }

    /**
     * @param array $default_sorts
     *
     * @return $this
     */
    public function defaultSorts(array $default_sorts): static
    {
        $this->default_sorts = $default_sorts;

        return $this;
    }

    /**
     * @param array $filters
     *
     * @return $this
     */
    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setFromRequest(Request $request): static
    {
        $per_page = $request->get('per_page', $this->per_page);

        // Pagination or infinite scrolling.
        if ($request->filled('page')) {
            $this->offset = (($request->get('page') - 1) * $per_page);
        } else {
            $this->offset = $request->get('start', 0);
        }

        // Sorting.
        if ($request->filled('sort')) {
            $this->default_sorts = [
                $request->get('sort') => $request->get('order', 'asc'),
            ];
        }

        return $this;
    }
}
