<?php

namespace Snaccs\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;

/**
 * Class Website
 *
 * @package Snaccs\Casts
 */
class Website implements CastsInboundAttributes
{
    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return parse_website($value);
    }
}
