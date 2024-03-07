<?php

namespace Snaccs\Validation\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class Time
 *
 * @package Snaccs\Validation\Rules
 */
class Time implements Rule
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

        try {
            Carbon::createFromTimeString($value);
        } catch (Exception $e) { // DateMalformedStringException in PHP 8.3+
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute field is not a valid time.";
    }
}
