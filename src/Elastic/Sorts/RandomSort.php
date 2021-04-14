<?php

namespace Snaccs\Elastic\Sorts;

use Illuminate\Support\Str;

/**
 * Class RandomSort
 *
 * @package Snaccs\Elastic\Sorts
 */
class RandomSort extends AbstractSort
{
    public string $seed;

    /**
     * RandomSort constructor.
     *
     * @param string|null $seed
     */
    public function __construct(?string $seed = null)
    {
        $this->seed = $seed ?? Str::random(16);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_script' => [
                'type'   => 'number',
                'script' => [
                    'lang'   => 'painless',
                    'source' => "return (doc['_id'].value + params.salt).hashCode()",
                    'params' => [
                        'salt' => $this->seed,
                    ],
                ],
                'order'  => $this->order, // order is kind of irrelevant in this
            ],
        ];
    }
}
