<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

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
            // TODO: this is similar to `parse_phone`
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phoneUtil->parse($value, $this->country_code ?? 'US');

            $value = parse_phone($value, $this->country_code);

            if (in_array($this->country_code, [null, 'CA', 'US'])) {
                return strlen($value) === 10;
            }

            return strlen($value) >= 7 && strlen($value) <= 15;
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
