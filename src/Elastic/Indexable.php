<?php

namespace Snaccs\Elastic;

use Elasticquent\ElasticquentInterface;
use Elasticquent\ElasticquentResultCollection;
use Illuminate\Http\Request;

/**
 * Interface Indexable
 *
 * @package Snaccs\Elastic
 */
interface Indexable extends ElasticquentInterface
{
    /**
     * @return array
     */
    public function allowedFilters(): array;

    /**
     * @return array
     */
    public function allowedSorts(): array;

    /**
     * @return array
     */
    public function defaultSorts(): array;

    /**
     * @param Request $request
     *
     * @return Config
     */
    public function getConfig(Request $request): Config;

    /**
     * @return string|null
     */
    public function searchableText(): ?string;

    /**
     * @return array
     */
    public function relationsToIndex(): array;

    /**
     * @return array
     */
    public function additionalFieldsToIndex(): array;

    /**
     * @param Request|null $request
     * @param array        $filters
     * @param int          $per_page
     *
     * @return ElasticquentResultCollection
     */
    public static function elasticSearch(
        ?Request $request = null,
        array $filters = [],
        int $per_page = 10
    );

    /**
     * @return array
     */
    public function transform(): array;
}
