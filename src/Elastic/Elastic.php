<?php

namespace App\Services;

use App\Support\ElasticFilter;
use App\Support\Indexable;
use Carbon\Carbon;
use Elasticquent\ElasticquentResultCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * @var bool
     */
    protected $has_any_date_filter = false;

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
     * @param string $default_sort
     * @param string $secondary_sort
     *
     * @return $this
     */
    public function defaultSort($default_sort, $secondary_sort = null)
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
        $filter = (new ElasticFilter($key, $value));

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
     * The value will be the Monday at the start of that week
     * We want the range from that Monday to that Sunday
     *
     * @param string[] $dates
     */
    protected function applyDateRangeFilter($dates)
    {
        $this->has_any_date_filter = true;

        $start = Carbon::parse($dates['start']);
        $end = Carbon::parse($dates['end']);
    }

    /**
     * @param string|null $keyword
     *
     * @return $this
     */
    public function keyword($keyword)
    {
        if ($keyword) {
            $this->filters['keyword'] = $keyword;
            $this->allowed_filters[] = 'keyword';
        }

        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        /** @var Indexable $model */
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

        $sorts = [];
        if (isset($this->filters['keyword'])) {
            $sorts[] = '_score';
        } elseif ($this->sort !== 'best_match') {
            $sorts[$this->sort] = [
                'order' => $this->order,
            ];
        } else {
            // Ugh, why
            $sorts[$this->default_sort] = [
                'order' => Str::endsWith($this->default_sort, '_at') ? 'desc' : 'asc',
            ];
        }

        if ($this->secondary_sort) {
            $sorts[$this->secondary_sort] = [
                'order' => 'desc',
            ];
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

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * @var float
     */
    protected $radius;

    /**
     * Elastic constructor
     *
     * @param string  $class_name
     * @param Request $request
     * @param int     $per_page
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
     * @param int $value
     */
    protected function applyBasicFilter($key, $value)
    {
        $this->queries['filter'][] = [
            'match' => [
                $key => $value,
            ],
        ];
    }

    /**
     * The value will be the Monday at the start of that week
     * We want the range from that Monday to that Sunday
     *
     * @param string $dates
     */
    protected function applyDatesFilter($dates)
    {
        if ($this->class_name !== Tournament::class) {
            throw new InvalidArgumentException();
        }

        $this->has_any_date_filter = true;

        $dates = Carbon::parse($dates);

        $date_key = $this->class_name === Ice::class ? 'date' : 'start_date';

        $this->queries['filter'][] = [
            'range' => [
                $date_key => [
                    'gte' => $dates->toDateString(),
                    'lte' => $dates->copy()->endOfWeek()->toDateString(),
                ],
            ],
        ];
    }

    /**
     * The value will be the Monday at the start of that week
     * We want the range from that Monday to that Sunday
     *
     * @param string[] $dates
     */
    protected function applyDateTimeRangeFilter($dates)
    {
        $this->has_any_date_filter = true;

        $start = Carbon::parse($dates['start']);
        $end = Carbon::parse($dates['end']);
    }

    /**
     * @param string $keyword
     */
    protected function applyKeywordFilter($keyword)
    {
        if (! $keyword) {
            return;
        }

        /**
         * Filters are always required (AND logic)
         * If there is a query string, I'm also adding a "should" query (OR logic)
         *
         * filter(s) AND (name OR city match)
         */
        $this->queries['should'] = [];

        /**
         * ALL keywords must match
         * The best match gets added to the score
         *
         * Example:
         * Centennial Ice Arena, Wilmette IL
         *
         * "Cent" will match name ngram
         * "Arena" will match name ngram
         * "Cent Ice" will match name ngram (both keywords match)
         * "Cent Rink" will not match (only 1 keyword matches)
         * "Wilmette" will match city ngram
         */
        $keyword_query = [
            'multi_match' => [
                'query'     => $keyword,
                'fields'    => [
                    'searchable^3',
                ],
                'operator'  => 'and',
                'fuzziness' => 1,
            ],
        ];

        if (in_array($this->class_name, [Clinic::class, Tournament::class])) {
            //$keyword_query['multi_match']['fields'][] = 'rink_name';

            $this->highlight = [
                'pre_tags'  => [
                    '<em>',
                ],
                'post_tags' => [
                    '</em>',
                ],
                'fields'    => [
                    'rink_name' => [
                        'require_field_match' => false,
                    ],
                ],
            ];
        }

        $this->queries['should'][] = $keyword_query;

        $this->min_score = 1;
    }

    /**
     * @param Carbon $start_date
     * @param string $key
     */
    protected function applyStartDateFilter(Carbon $start_date, $key = 'from')
    {
        $this->has_any_date_filter = true;

        $date_key = $this->class_name === Ice::class ? 'date' : 'start_date';

        $this->queries['filter'][] = [
            'range' => [
                $date_key => [
                    $key => $start_date->toDateString(),
                ],
            ],
        ];
    }

    /**
     * @param Carbon $end_date
     */
    protected function applyEndDateFilter(Carbon $end_date, $key = 'from')
    {
        $this->has_any_date_filter = true;

        $date_key = $this->class_name === Ice::class ? 'date' : 'end_date';

        $this->queries['filter'][] = [
            'range' => [
                $date_key => [
                    $key => $end_date->toDateString(),
                ],
            ],
        ];
    }

    /**
     * Any result that starts on or before today
     *
     * @param $discard
     */
    protected function applyPastFilter($discard)
    {
        if ($this->has_any_date_filter) {
            return;
        }

        $this->applyEndDateFilter(Carbon::now()->startOfDay(), 'to');
        $this->sort = 'past';
    }

    /**
     * Any result that starts after today
     * e.g. upcoming tournaments that start tomorrow
     *
     * @param $discard
     */
    protected function applyUpcomingFilter($discard)
    {
        if ($this->has_any_date_filter) {
            return;
        }

        $this->applyStartDateFilter(Carbon::now()->startOfDay()->addDay());
    }

    /**
     * Any result that ends on today or after
     * e.g. clinics that are currently active and ending today or later
     *
     * @param $discard
     */
    protected function applyActiveFilter($discard)
    {
        if ($this->has_any_date_filter) {
            return;
        }

        $this->applyEndDateFilter(Carbon::now()->startOfDay());
    }

    /**
     * @param bool $include_past
     *
     * @return $this
     */
    public function pastOnly()
    {
        $this->filters['past'] = true;
        $this->allowed_filters[] = 'past';

        return $this;
    }

    /**
     * @param bool $include_past
     *
     * @return $this
     */
    public function withPast($include_past)
    {
        if ($include_past) {
            $this->sort = 'past';
        }

        return $this;
    }

    /**
     * @param bool $include_past
     *
     * @return $this
     */
    public function withPastAndOngoing($include_past)
    {
        if ($include_past) {
            $this->sort = 'past_and_ongoing';
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function defaultToUpcoming()
    {
        if ($this->sort !== 'past_and_ongoing') {
            $this->filters['upcoming'] = true;
            $this->allowed_filters[] = 'upcoming';
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function defaultToUpcomingIncludingToday()
    {
        if ($this->sort !== 'past') {
            $this->filters['active'] = true;
            $this->allowed_filters[] = 'active';
        }

        return $this;
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

        $this->latitude = $request->get('latitude');
        $this->longitude = $request->get('longitude');

        if ($request->filled('radius')) {
            $this->radius = $request->get('radius');

            $this->filters['radius'] = $this->radius;
            $this->allowed_filters[] = 'radius';
        }
    }

    /**
     * @param int $offset
     * @param int $per_page
     *
     * @return $this
     */
    public function overrideConfig($offset, $per_page)
    {
        $this->offset = $offset;
        $this->per_page = $per_page;

        return $this;
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

        if ($model instanceof SingleSportInterface || $model instanceof MultipleSportsInterface) {
            // For now, rink & shop cover every sport
            if (! $model instanceof Rink && ! $model instanceof Shop) {
                $sport = sport();

                if ($sport) {
                    $this->filters['sports'] = $sport->id;
                    $this->allowed_filters[] = 'sports';
                }
            }
        }

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

        // Default: best match, then name ascending
        $sorts = [
            $this->default_sort,
        ];
        if ($this->secondary_sort) {
            $sorts[] = $this->secondary_sort;
        }

        $name_key = $this->default_sort === 'last_name' ? 'last_name' : 'name';

        $sort = $this->sort;
        if ($sort === 'name') {
            $sorts = [
                $name_key,
            ];
            if ($this->secondary_sort) {
                $sorts[] = $this->secondary_sort;
            }
        } elseif ($sort === 'name.desc') {
            $sorts = [
                $name_key => [
                    'order' => 'desc',
                ],
            ];
            if ($this->secondary_sort) {
                $sorts[$this->secondary_sort] = [
                    'order' => 'desc',
                ];
            }
        } elseif ($sort === 'best_match') {
            $sorts = [
                '_score',
                $this->default_sort,
            ];
            if ($this->secondary_sort) {
                $sorts[] = $this->secondary_sort;
            }
        } elseif ($sort === 'rating') {
            $sorts = [
                'rating'            => [
                    'order' => 'desc',
                ],
                $this->default_sort => [
                    'order' => 'asc',
                ],
            ];
            if ($this->secondary_sort) {
                $sorts[$this->secondary_sort] = [
                    'order' => 'asc',
                ];
            }
        } elseif ($sort === 'rating.asc') {
            $sorts = [
                'rating',
                $this->default_sort,
            ];
            if ($this->secondary_sort) {
                $sorts[] = $this->secondary_sort;
            }
        } elseif ($sort === 'start_date') {
            $date_key = $this->class_name === Ice::class ? 'date' : 'end_date';

            $sorts = [
                $date_key,
            ];
            if ($this->secondary_sort) {
                $sorts[] = $this->secondary_sort;
            }
        } elseif ($sort === 'past_and_ongoing') {
            // Upcoming tournaments in ascending order,
            // Then ongoing and past tournaments in descending order
            // Or just change it to ? 1 : 0 and add 'start_date' => 'asc' if you want all in ascending order
            $sorts = [
                '_script' => [
                    'type'   => 'number',
                    'script' => [
                        'lang'   => 'painless',
                        'source' => "return doc['start_date'].value.millis > params.now ? Long.MAX_VALUE - doc['start_date'].value.millis : -1 * (params.now - doc['start_date'].value.millis)",
                        'params' => [
                            'now' => Carbon::now('UTC')->endOfDay()->valueOf(),
                        ],
                    ],
                    'order'  => 'desc',
                ],
            ];
        } elseif ($sort === 'past') {
            // Ongoing and upcoming clinics in ascending order,
            // Then past tournaments in descending order
            // Or just change it to ? 1 : 0 and add 'start_date' => 'asc' if you want all in ascending order
            $sorts = [
                '_script' => [
                    'type'   => 'number',
                    'script' => [
                        'lang'   => 'painless',
                        'source' => "return doc['end_date'].value.millis >= params.now ? Long.MAX_VALUE - doc['start_date'].value.millis : -1 * (params.now - doc['start_date'].value.millis)",
                        'params' => [
                            'now' => Carbon::now('UTC')->startOfDay()->valueOf(),
                        ],
                    ],
                    'order'  => 'desc',
                ],
            ];
        } elseif ($sort === 'random') {
            $sorts = [
                '_script' => [
                    'type'   => 'number',
                    'script' => [
                        'lang'   => 'painless',
                        'source' => "return (doc['_id'].value + params.salt).hashCode()",
                        'params' => [
                            'salt' => Str::random(16), // todo allow passing in salt
                        ],
                    ],
                    'order'  => 'asc',
                ],
            ];
        }

        // If searching by "near me", we want the closest first
        if ($this->latitude && $this->longitude) {
            $coords = rtrim(number_format($this->latitude, 6), '0') . ',' .
                rtrim(number_format($this->longitude, 6), '0');

            $sorts = [
                '_geo_distance' => [
                    'coords'          => $coords,
                    'order'           => 'asc',
                    'unit'            => 'mi',
                    'mode'            => 'min',
                    'distance_type'   => 'arc', // 'plane' is less accurate but faster
                    'ignore_unmapped' => true,
                ],
            ];
        }

        if ($this->default_sort === 'created_at') {
            if ($sort === 'best_match') {
                $sorts = [
                    '_score'     => [
                        'order' => 'desc',
                    ],
                    'created_at' => [
                        'order' => 'desc',
                    ],
                ];
            } else {
                $sorts = [
                    'created_at' => [
                        'order' => 'desc',
                    ],
                ];
            }
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
    public function query()
    {
        $params = $this->build();

        return $this->class_name::complexSearch($params);
    }
}
