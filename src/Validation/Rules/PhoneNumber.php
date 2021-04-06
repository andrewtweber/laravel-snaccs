<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class PhoneNumber
 *
 * @package Snaccs\Validation\Rules
 */
class PhoneNumber implements Rule
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

        // Strips extra characters out
        // Also removes the `1` country code if US/CA
        $value = parse_phone($value);

        if (in_array($this->country, [null, 'CA', 'US'])) {
            return strlen($value) === 10;
        }

        return strlen($value) >= 7 && strlen($value) <= 15;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->country) {
            return 'The :attribute field is not a valid ' . $this->country . ' phone number.';
        }

        return 'The :attribute field is not a valid phone number.';
    }
}
