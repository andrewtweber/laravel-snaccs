<?php

namespace Snaccs\Elastic;

use Elasticquent\ElasticquentResultCollection;
use Illuminate\Contracts\Support\Arrayable;
use Snaccs\Elastic\Filters\AbstractFilter;

/**
 * Class Query
 *
 * @package Snaccs\Elastic
 */
class Query implements Arrayable
{
    protected array $queries;

    protected array $highlight = [];

    protected ?string $aggregate_by = null;

    /**
     * Query constructor.
     *
     * @param Config $config
     */
    public function __construct(
        public Indexable $model,
        public Config $config
    ) {
        $this->queries = [
            'filter' => [],
        ];
    }

    /**
     * @param AbstractFilter $filter
     *
     * @return $this
     */
    public function addFilter(AbstractFilter $filter): static
    {
        $this->queries['filter'][] = $filter->toArray();

        $this->config->min_score = max($this->config->min_score, $filter->minScore());

        return $this;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function aggregateBy(string $field): static
    {
        $this->aggregate_by = $field;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $params = [
            'index' => $model->getIndexName(),
            'type'  => 'entity',
            'body'  => [
                'query'     => [
                    'bool' => $this->queries,
                ],
                'sort'      => $sorts,
                'from'      => $this->config->offset,
                'size'      => $this->config->per_page,
                'min_score' => $this->config->min_score,
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
     * @param array|null $params
     *
     * @return ElasticquentResultCollection
     */
    public function get(?array &$params = null)
    {
        return get_class($this->model)::complexSearch($params);
    }
}
