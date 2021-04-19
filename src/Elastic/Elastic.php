<?php

namespace Snaccs\Elastic;

use Elasticquent\ElasticquentResultCollection;
use Illuminate\Support\Str;
use Snaccs\Elastic\Filters\AbstractFilter;
use Snaccs\Elastic\Filters\BasicFilter;
use Snaccs\Elastic\Filters\KeywordFilter;
use Snaccs\Elastic\Sorts\AbstractSort;
use Snaccs\Elastic\Sorts\BasicSort;
use Snaccs\Elastic\Sorts\BestMatch;
use Snaccs\Elastic\Sorts\DistanceSort;
use Snaccs\Elastic\Sorts\RandomSort;
use Snaccs\Elastic\Types\Coords;

/**
 * Class Elastic
 *
 * @package Snaccs\Elastic
 */
class Elastic
{
    /**
     * Globally registered filters.
     *
     * @var array
     */
    protected array $filters = [];

    /**
     * Globally registered sorts.
     *
     * @var array
     */
    protected array $sorts = [];

    /**
     * @var Query
     */
    protected Query $query;

    /**
     * Elastic constructor.
     */
    public function __construct()
    {
        $this->registerFilter('keyword', KeywordFilter::class);

        $this->registerSort('best_match', new BestMatch());
        $this->registerSort('random', new RandomSort()); // @todo seed
    }

    /**
     * @param string         $key
     * @param AbstractFilter $filter
     */
    public function registerFilter(string $key, AbstractFilter $filter)
    {
        $this->filters[$key] = $filter;
    }

    /**
     * @param string       $key
     * @param AbstractSort $sort
     */
    public function registerSort(string $key, AbstractSort $sort)
    {
        $this->sorts[$key] = $sort;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        $model = $this->model;

        foreach ($this->config->filters as $key => $value) {
            if (! in_array($key, $model->allowedFilters())) {
                continue;
            }
            if ($value === null || $value === '') {
                // TODO: are you sure about this?
                continue;
            }

            $filter = $this->filters[$key] ?? new BasicFilter($key, $value);

            $this->query->addFilter($filter);
        }

        // TODO: date filters should be limited to 1
        // TODO: rating, created_at should default order to descending
        // TODO: if KeywordFilter overrides all other filters, should also force BestMatch sorting

        $sorts = [];
        if (! isset($sorts[$sort])) {
            $sorts[] = new BasicSort($sort, $order);
        }
        if ($sort !== $this->default_sort) {
            $sorts[] = new BasicSort($this->default_sort);
        }
        if ($sort !== $this->secondary_sort) {
            $sorts[] = new BasicSort($this->secondary_sort);
        }

        $sorts = [];
        foreach ($sorts as $sort) {
            $sorts = array_merge($sorts, $sort->toArray());
        }

        return $this->query->toArray();
    }

    /**
     * @param Indexable $model
     * @param Config    $config
     *
     * @return Query
     */
    public function query(Indexable $model, Config $config): Query
    {
        return new Query($model, $config);
    }
}
