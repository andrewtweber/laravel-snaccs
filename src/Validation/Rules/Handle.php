<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Handle
 *
 * @package Snaccs\Validation\Rules
 */
class Handle implements Rule
{
    protected string $label;
    protected string $allowed_special_chars;
    protected int $min;
    protected int $max;
    protected array $allowed_domains = [];

    /**
     * @param int $min
     * @param int $max
     *
     * @return $this
     */
    public function lengthBetween(int $min, int $max): static
    {
        $this->min = $min;
        $this->max = $max;

        return $this;
    }

    /**
     * @param string $allowed_special_chars
     *
     * @return $this
     */
    public function allowedSpecialChars(string $allowed_special_chars): static
    {
        $this->allowed_special_chars = $allowed_special_chars;

        return $this;
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

        $value = parse_handle($value, $this->allowed_domains);

        $chars = $this->allowed_special_chars;

        // Escape special characters for regex.
        // Forward slash is not a special regular expression character,
        // but we're using it as the delimiter so we need to escape it too.
        $escaped_chars = str_replace("/", "\/", preg_quote($chars));

        $regex = '/^@?([a-zA-Z0-9' . $escaped_chars . ']){' . $this->min . ',' . $this->max . '}$/';

        return (preg_match($regex, $value) === 1);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans()->has('validation.handle')
            ? trans('validation.handle', ['label' => $this->label])
            : "The :attribute field is not a valid {$this->label} handle.";
    }
}
