<?php

namespace Snaccs\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Snaccs\Models\Interfaces\PhoneNumberable;

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
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return string|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (! $model instanceof PhoneNumberable) {
            throw new InvalidArgumentException($model::class . ' must implement PhoneNumberable');
        }

        return format_phone($value, $model->getCountryCode());
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return string|null
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (! $model instanceof PhoneNumberable) {
            throw new InvalidArgumentException($model::class . ' must implement PhoneNumberable');
        }

        return parse_phone($value, $model->getCountryCode());
    }
}
