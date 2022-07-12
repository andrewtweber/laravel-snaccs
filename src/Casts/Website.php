<?php

namespace Snaccs\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;

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
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return parse_website($value);
    }
}
