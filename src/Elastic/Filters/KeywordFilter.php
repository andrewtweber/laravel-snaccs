<?php

namespace Snaccs\Elastic\Filters;

class KeywordFilter extends AbstractFilter
{
    public function __construct(
        public string $keyword
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        /**
         * Filters are always required (AND logic)
         * If there is a query string, I'm also adding a "should" query (OR logic)
         *
         * filter(s) AND (name OR city match)
         */
        //$this->queries['should'] = [];

        /**
         * RESET ALL OTHER FILTERS
         * If searching by a keyword, filters are ignored
         */
        $this->queries['filter'] = [];

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
        return [
            'multi_match' => [
                'query'     => $this->keyword,
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

        $this->min_score = 1;
    }
}
