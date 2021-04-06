<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Twitter
 *
 * @package Snaccs\Validation\Rules
 */
class Twitter implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
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

        $value = parse_handle($value);

        return (preg_match('/^@?([a-zA-Z0-9_]){1,15}$/', $value) === 1);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field is not a valid Twitter handle.';
    }
}
