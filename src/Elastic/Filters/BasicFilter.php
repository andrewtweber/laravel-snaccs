<?php

namespace Snaccs\Elastic\Filters;

/**
 * Class BasicFilter
 *
 * @package Snaccs\Elastic\Filters
 */
class BasicFilter extends AbstractFilter
{
    protected string $matching;

    protected bool $nullable = false;

    // Match types
    public const MATCH_ALL = 'MATCH_ALL';
    public const MATCH_ANY = 'MATCH_ANY';

    /**
     * BasicFilter constructor.
     *
     * @param string $field
     * @param mixed  $value
     */
    public function __construct(
        public string $field,
        public mixed $value
    ) {
        $this->matching = static::MATCH_ANY;

        if (is_array($value) && in_array(null, $value)) {
            $this->nullable = true;

            // Remove null values from array
            // Then use array_values so that the keys are incrementing
            $this->value = array_values(array_filter($value, function ($single) {
                return $single !== null;
            }));
        } else if ($value === null) {
            $this->nullable = true;
        }
    }

    /**
     * Match with variable.
     *
     * @param string $matching
     *
     * @return $this
     */
    public function match(string $matching): static
    {
        $this->matching = $matching;

        return $this;
    }

    /**
     * Match all.
     *
     * @return $this
     */
    public function matchAll(): static
    {
        $this->matching = static::MATCH_ALL;

        return $this;
    }

    /**
     * Match any.
     *
     * @return $this
     */
    public function matchAny(): static
    {
        $this->matching = static::MATCH_ANY;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $type = $this->matching === static::MATCH_ALL ? 'term' : 'terms';
        $nullable = $this->nullable;
        $key = $this->field;
        $value = $this->value;

        // More than 1 value
        if (is_array($value) && count($value) > 0) {
            // Match any (anything besides tags)
            if ($type === 'terms') {
                if (count($value) === 1) {
                    $type = 'match';
                    $value = array_pop($value);
                }

                if ($nullable) {
                    // Match any of multiple non-null values OR null
                    return [
                        'bool' => [
                            'should' => [
                                [
                                    'bool' => [
                                        'must_not' => [
                                            'exists' => [
                                                'field' => $key,
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'bool' => [
                                        'filter' => [
                                            $type => [
                                                $key => $value,
                                            ],
                                        ],
                                    ],
                                ],
                            ],

                            'minimum_should_match' => 1,
                        ],
                    ];
                } else {
                    // Match any of multiple non-null values
                    return [
                        $type => [
                            $key => $value,
                        ],
                    ];
                }
            }

            // Match all non-null values (tags)
            foreach ($value as $single) {
                return [
                    $type => [$key => $single],
                ];
            }
        }

        // Convert single array value to flat
        if (is_array($value)) {
            if (count($value) === 0 && ! $this->nullable) {
                return null;
            }

            $value = array_pop($value);
        }

        // Match null only
        if ($value === null || $this->nullable) {
            return [
                'bool' => [
                    'must_not' => [
                        'exists' => [
                            'field' => $key,
                        ],
                    ],
                ],
            ];
        }

        // Match non-null only
        return [
            'match' => [
                $key => $value,
            ],
        ];
    }
}
