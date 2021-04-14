<?php

namespace App\Services;

use App\Support\ElasticFilter;
use App\Support\Indexable;
use Carbon\Carbon;
use Elasticquent\ElasticquentResultCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Snaccs\Elastic\Filters\BasicFilter;
use Snaccs\Elastic\Sorts\BasicSort;
use Snaccs\Elastic\Sorts\BestMatch;
use Snaccs\Elastic\Sorts\DistanceSort;
use Snaccs\Elastic\Sorts\RandomSort;
use Snaccs\Elastic\Types\Coords;

/**
 * Class Elastic
 *
 * @package App\Services
 */
class Elastic
{
    /**
     * @var string
     */
    protected $class_name;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $highlight;

    /**
     * @var string[]
     */
    protected $allowed_filters = [];

    /**
     * @var int
     */
    protected $per_page;

    /**
     * @var array
     */
    protected $queries;

    /**
     * Requested sort may override default sort
     *
     * @var string
     */
    protected $sort;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var string
     */
    protected $default_sort = 'name';

    /**
     * @var string
     */
    protected $secondary_sort = null;

    /**
     * @var int
     */
    protected $min_score = 0;

    /**
     * @var string
     */
    protected $aggregate_by = null;

    /**
     * Elastic constructor
     *
     * @param string $class_name
     * @param int    $per_page
     */
    public function __construct($class_name, Request $request = null, $per_page = 32)
    {
        $this->class_name = $class_name;
        $this->per_page = $per_page;

        if ($request) {
            $this->setConfigFromRequest($request);
        }
    }

    /**
     * TODO: allow multiple e.g. last_name, first_name for coaches
     * name, city for rinks
     *
     * @param array $default_sorts
     *
     * @return $this
     */
    public function defaultSort(array $default_sorts)
    {
        if (! $this->sort) {
            $this->sort = $default_sort;
        }
        $this->default_sort = $default_sort;
        $this->secondary_sort = $secondary_sort;

        return $this;
    }

    /**
     * @param int $value
     */
    protected function applyBasicFilter($key, $value)
    {
        $filter = (new BasicFilter($key, $value));

        // TODO: make this more flexible
        if ($key === 'tags') {
            $filter->matchAll();
        } else {
            $filter->matchAny();
        }

        $result = $filter->toArray();

        if ($result !== null) {
            $this->queries['filter'][] = $result;
        }
    }

    /**
     * @param string[] $allowed_filters
     *
     * @return Elastic
     */
    public function allowedFilters(array $allowed_filters)
    {
        $this->allowed_filters = array_merge($this->allowed_filters, $allowed_filters);

        return $this;
    }

    /**
     * @param array $filters
     *
     * @return Elastic
     */
    public function filter(array $filters)
    {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if (str_starts_with($name, 'apply') && str_ends_with($name, 'Filter')) {
            $name = str_replace('apply', '', $name);
            $name = str_replace('Filter', '', $name);
            $name = Str::snake($name);

            $this->applyBasicFilter($name, $arguments[0]);
        }
    }

    /**
     * @param Request $request
     */
    protected function setConfigFromRequest(Request $request)
    {
        // Pagination / infinite scrolling
        if ($request->filled('page')) {
            $this->offset = (($request->get('page', 1) - 1) * $this->per_page);
        } else {
            $this->offset = $request->get('start', 0);
        }

        if ($request->filled('sort')) {
            $this->sort = $request->get('sort');
        }
        if ($request->filled('order')) {
            $this->order = $request->get('order');
        }
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function aggregateBy(string $column)
    {
        $this->aggregate_by = $column;

        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        /** @var IndexableInterface $model */
        $model = new $this->class_name;

        $this->queries = [
            'filter' => [],
        ];

        foreach ($this->filters as $key => $value) {
            if (! in_array($key, $this->allowed_filters)) {
                continue;
            }
            if ($value === null || $value === '') {
                continue;
            }

            $method = Str::camel("apply_{$key}_filter");

            $this->$method($value);
        }

        // TODO: date filters should be limited to 1
        // TODO: rating, created_at should default order to descending
        // TODO: if KeywordFilter overrides all other filters, should also force BestMatch sorting

        $sorts = [
            'best_match' => new BestMatch(),
            'random'     => new RandomSort(),
            'distance'   => new DistanceSort(new Coords($this->latitude, $this->longitude)),
            //'past_and_ongoing' => new PastAndOngoingSort(),
            //'past' => new PastSort(),
        ];

        $final = [];
        if (! isset($sorts[$sort])) {
            $final[] = new BasicSort($sort, $order);
        }
        if ($sort !== $this->default_sort) {
            $final[] = new BasicSort($this->default_sort);
        }
        if ($sort !== $this->secondary_sort) {
            $final[] = new BasicSort($this->secondary_sort);
        }

        $sorts = [];
        foreach ($final as $sort) {
            $sorts = array_merge($sorts, $sort->toArray());
        }

        $params = [
            'index' => $model->getIndexName(),
            'type'  => 'entity',
            'body'  => [
                'query'     => [
                    'bool' => $this->queries,
                ],
                'sort'      => $sorts,
                'from'      => $this->offset,
                'size'      => $this->per_page,
                'min_score' => $this->min_score,
            ],
        ];

        if ($this->aggregate_by) {
            $params['body']['collapse'] = [
                'field' => $this->aggregate_by,
            ];
        }

        if ($this->highlight) {
            $params['body']['highlight'] = $this->highlight;
        }

        return $params;
    }
    
    /**
     * @return ElasticquentResultCollection
     */
    public function query(&$params = null)
    {
        $params = $this->build();

        //dd($params);

        return $this->class_name::complexSearch($params);
    }
}
