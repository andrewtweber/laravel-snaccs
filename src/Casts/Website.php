<?php

namespace Snaccs\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Snaccs\Support\Url;

/**
 * Class Website
 *
 * @package Snaccs\Casts
 */
class Website implements CastsAttributes
{
    /**
     * @param Model       $model
     * @param string      $key
     * @param string|null $value
     * @param array       $attributes
     *
     * @return Url|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return new Url($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model       $model
     * @param string      $key
     * @param string|null $value
     * @param array       $attributes
     *
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return parse_website($value);
    }
}
