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
        $value = strtoupper($value);
        $value = preg_replace('/[^0-9A-Z]/', '', $value);

        if (strlen($value) == 11 && substr($value, 0, 1) == 1) {
            $value = substr($value, 1);
        }

        return $value;
    }
}
