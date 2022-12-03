<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use libphonenumber\NumberParseException;

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
     * @param string|null $country_code
     */
    public function __construct(
        public ?string $country_code = null
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

        try {
            $value = parse_phone($value, $this->country_code);
            $parts = explode('EXT', $value);
            $number = $parts[0];

            if (in_array($this->country_code, [null, 'CA', 'US'])) {
                return strlen($number) === 10;
            }

            return strlen($number) >= 7 && strlen($number) <= 15;
        } catch (NumberParseException $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->country_code) {
            return trans()->has('validation.phone_with_country')
                ? trans('validation.phone_with_country', ['country' => $this->country_code])
                : 'The :attribute field is not a valid ' . $this->country_code . ' phone number.';
        }

        return trans()->has('validation.phone')
            ? trans('validation.phone')
            : 'The :attribute field is not a valid phone number.';
    }
}
