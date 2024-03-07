<?php

namespace Snaccs\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IpAddress
 *
 * @package Snaccs\Casts
 */
class IpAddress implements CastsAttributes
{
    /**
     * @param Model       $model
     * @param string      $key
     * @param string|null $value
     * @param array       $attributes
     *
     * @return string|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value === null ? null : inet_ntop($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model       $model
     * @param string      $key
     * @param string|null $value
     * @param array       $attributes
     *
     * @return string|null
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value === null ? null : inet_pton($value);
    }
}
