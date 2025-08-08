<?php

namespace Snaccs\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;
use Snaccs\Support\Url;

/**
 * Class Website
 *
 * @package Snaccs\Casts
 */
class Website implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * @param Model       $model
     * @param string      $key
     * @param string|null $value
     * @param array       $attributes
     *
     * @return Url|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
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
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return parse_website($value);
    }

    public function serialize(Model $model, string $key, mixed $value, array $attributes)
    {
        $value = $this->get($model, $key, $value, $attributes);

        return is_null($value) ? null : (string)$value;
    }
}
