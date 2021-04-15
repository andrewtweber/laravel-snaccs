<?php

namespace Snaccs\Elastic;

use Elasticquent\ElasticquentTrait;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Trait ElasticEntity
 *
 * @package Snaccs\Elastic
 */
trait ElasticEntity
{
    use ElasticquentTrait {
        newFromHitBuilder as elasticquentNewFromHitBuilder;
    }

    /**
     * @param Request $request
     *
     * @return Config
     */
    public function getConfig(Request $request): Config
    {
        return (new Config())
            ->defaultSorts($this->defaultSorts())
            ->setFromRequest($request);
    }

    /**
     * Elasticsearch index settings
     *
     * Tokens:
     * - full email
     * - partial emails (split by '@' character)
     * - full name ngrams (split by whitespace). 1-16 characters from the beginning of each word
     *
     * @var array
     */
    protected $indexSettings = [
        'analysis' => [
            'filter'   => [
                'ngram_filter' => [
                    'type'     => 'edge_ngram',
                    'min_gram' => 1,
                    'max_gram' => 16,
                    'side'     => 'front',
                ],
            ],
            'analyzer' => [
                'default_analyzer' => [
                    'tokenizer' => 'whitespace',
                    'filter'    => [
                        'lowercase',
                        'asciifolding', // Convert a-umlaut into 'a', etc.
                    ],
                ],
                'sortable'         => [
                    'tokenizer' => 'keyword',
                    'filter'    => [
                        'lowercase',
                        'asciifolding', // Convert a-umlaut into 'a', etc.
                    ],
                ],
                'name_ngram'       => [
                    'tokenizer' => 'whitespace',
                    'filter'    => [
                        'lowercase',
                        'asciifolding',
                        'ngram_filter',
                    ],
                ],
            ],
        ],
    ];

    /**
     * Elasticsearch mappings
     *
     * Note: most columns will be automatically mapped
     *
     * @var array
     */
    protected $mappingProperties = [
        'searchable'      => [
            'type'            => 'text',
            //'fielddata'       => true,
            'analyzer'        => 'name_ngram',
            'search_analyzer' => 'default_analyzer',
        ],
        'name'            => [
            'type'      => 'text',
            'fielddata' => true,
            'analyzer'  => 'sortable',
        ],
        'first_name'      => [
            'type' => 'keyword',
        ],
        'last_name'       => [
            'type' => 'keyword',
        ],
        'type'            => [
            'type' => 'keyword',
        ],
        'city'            => [
            'type' => 'keyword',
        ],
        'region'          => [
            'type' => 'keyword',
        ],
        'coords'          => [
            'type' => 'geo_point',
        ],
        'date'            => [
            'type'   => 'date',
            'format' => 'yyyy-MM-dd',
        ],
        'start_date'      => [
            'type'   => 'date',
            'format' => "yyyy-MM-dd",
        ],
        'end_date'        => [
            'type'   => 'date',
            'format' => "yyyy-MM-dd",
        ],
        'start_time'      => [
            'type'   => 'date',
            'format' => 'HH:mm:ss',
        ],
        'end_time'        => [
            'type'   => 'date',
            'format' => 'HH:mm:ss',
        ],
        'date_range'      => [
            'type'   => 'date_range',
            'format' => 'yyyy-MM-dd',
        ],
        'date_time_range' => [
            'type'   => 'date_range',
            'format' => "yyyy-MM-dd' 'HH:mm:ss",
        ],
        'created_at'      => [
            'type'   => 'date',
            'format' => "yyyy-MM-dd' 'HH:mm:ss",
        ],
        'applied_at'      => [
            'type'   => 'date',
            'format' => "yyyy-MM-dd' 'HH:mm:ss",
        ],
    ];

    /**
     * Unfortunately we can't search across everything, have to split into multiple indexes...
     *
     * @return string
     */
    public function getIndexName()
    {
        return config('elasticquent.default_index') . '.' . $this->getTable();
    }

    /**
     * Type must be the same now
     * https://www.elastic.co/blog/index-type-parent-child-join-now-future-in-elasticsearch
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'entity';
    }

    /**
     * Add sort "scores" to object when loading from Elasticquent
     * For example, so that we can display the actual distance from the user
     *
     * @param array $hit
     *
     * @return static
     */
    public function newFromHitBuilder($hit = [])
    {
        if (! empty($hit['sort'])) {
            $hit['fields']['sort'] = $hit['sort'];
        }

        if (! empty($hit['highlight'])) {
            $hit['fields']['highlight'] = $hit['highlight'];
        }

        return $this->elasticquentNewFromHitBuilder($hit);
    }

    /**
     * Remove from index when deleting
     *
     * @return bool|null
     */
    public function delete()
    {
        try {
            $this->removeFromIndex();
        } catch (Missing404Exception $e) {
            // Ignore
        }

        return parent::delete();
    }

    /**
     * Simplified array for Elasticache
     * This is not *just* fields to be indexed, but anything we want it to return
     *
     * Note: id is automatically included
     */
    public function getIndexDocumentData()
    {
        // Every indexable entity should have searchable text
        $data = [
            'searchable' => $this->searchableText(),
        ];

        $data = array_merge($data, $this->additionalFieldsToIndex());

        // Relations
        foreach ($this->relationsToIndex() as $key => $value) {
            if (! is_numeric($key)) {
                $index_key = $relation = $key;
                $column = $value;
            } else {
                $index_key = $relation = $value;
                $column = 'id';
            }
            $relation = Str::camel($relation);

            // Note: the array indexes must be incrementing
            // That's why we have to do values() and then all()
            if ($this->$relation) {
                $values = $this->$relation->pluck($column)->unique()->values()->all();
            } else {
                $values = [];
            }

            if (count($values) > 0) {
                $data[$index_key] = $values;
            }
        }

        return $data;
    }
}
