<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class Website
 *
 * @package Snaccs\Validation\Rules
 */
class Website implements Rule
{
    /**
     * Website constructor
     *
     * @param array $allowedDomains
     */
    public function __construct(
        public array $allowedDomains = []
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
        $value = parse_website($value);

        // If you want an empty string or null to fail, you must also make it `required`
        // @link https://laravel.com/docs/8.x/validation#implicit-rules
        if ($value === null || $value === '') {
            return true;
        }

        // URL invalid, immediately quit
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        // Any domain is allowed
        if (count($this->allowedDomains) === 0) {
            return true;
        }

        // Check if any domain matches, including subdomains
        foreach ($this->allowedDomains as $domain) {
            $domain = strtolower($domain);
            $host = strtolower(parse_url($value, PHP_URL_HOST));

            if ($host === $domain || Str::endsWith(strtolower($host), '.' . strtolower($domain))) {
                return true;
            }
        }

        // No domains valid
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (count($this->allowedDomains) > 0) {
            return 'The :attribute field is not a valid ' . $this->allowedDomains[0] . ' URL.';
        }

        return 'The :attribute field is not a valid URL.';
    }
}
