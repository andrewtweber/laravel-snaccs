<?php

namespace Snaccs\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Class PhoneNumber
 *
 * @package Snaccs\Casts
 */
class PhoneNumber implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        return format_phone($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return parse_phone($value);
    }
}
