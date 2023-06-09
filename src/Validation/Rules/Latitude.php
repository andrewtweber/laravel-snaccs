<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Latitude
 *
 * @package Snaccs\Validation\Rules
 */
class Latitude implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param mixed $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // If you want an empty string or null to fail, you must also make it `required`
        // @link https://laravel.com/docs/8.x/validation#implicit-rules
        if ($value === null || $value === '') {
            return true;
        }

        return is_numeric($value)
            && $value >= -90
            && $value <= 90;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute field is not a valid latitude.";
    }
}
