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
     * PhoneNumber constructor.
     *
     * @param string|null $country
     */
    public function __construct(
        public ?string $country = null
    ) {
    }

    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param string                              $value
     * @param array                               $attributes
     *
     * @return string
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return format_phone($value, $this->country);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param string                              $value
     * @param array                               $attributes
     *
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return parse_phone($value);
    }
}
