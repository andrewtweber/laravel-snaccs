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
     * @param string $format - the format when accessing (the format when mutating is always H:i:s)
     */
    public function __construct(
        public string $format = 'H:i:s'
    ) {
    }

    /**
     * @param Model       $model
     * @param string      $key
     * @param string|null $value
     * @param array       $attributes
     *
     * @return string|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return Carbon::createFromTimeString($value)->format($this->format);
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
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return Carbon::createFromTimeString($value)->format('H:i:s');
    }
}
