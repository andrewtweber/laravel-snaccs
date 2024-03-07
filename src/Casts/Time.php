<?php

namespace Snaccs\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Time
 *
 * @package Snaccs\Casts
 */
class Time implements CastsAttributes
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
        if ($value === null) {
            return null;
        }

        return $value;
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
        if ($value === null) {
            return null;
        }

        return Carbon::createFromTimeString($value)->format('H:i:s');
    }
}
