<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Username
 *
 * @package Snaccs\Validation\Rules
 */
class Username implements Rule
{
    private string $message = 'The :attribute field is not a valid username.';

    /**
     * Username constructor.
     *
     * @param Authenticatable|null $user
     */
    public function __construct(
        public ?Authenticatable $user = null
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

        $config = config('system.usernames');

        assert($config['min'] > 0, new \RuntimeException("Invalid username min length {$config['min']}"));
        assert(
            $config['max'] >= $config['min'],
            new \RuntimeException("Invalid username length bounds {$config['min']}-{$config['max']}")
        );

        // Check for uniqueness
        if ($config['table']) {
            $exists = DB::table($config['table']);

            // Ignore the given user, if applicable
            if ($this->user && $this->user instanceof Model) {
                $key = $this->user->getKeyName();

                $exists->where($key, '!=', $this->user->$key);
            }

            $exists = $exists->where($attribute, '=', $value)->first();

            if ($exists) {
                $this->message = "That username is already in use.";

                return false;
            }
        }

        // Length requirements.
        if (strlen($value) < $config['min']) {
            $this->message = "The username must be {$config['min']} or more characters.";

            return false;
        } else if (strlen($value) > $config['max']) {
            $this->message = "The username must be {$config['max']} or fewer characters.";

            return false;
        }

        // Reserved usernames.
        if (in_array($value, $config['reserved'])) {
            $this->message = "That username is not allowed.";

            return false;
        }

        // Escape special characters for regex.
        $chars = preg_quote($config['allowed_special_chars']);

        // Forward slash is not a special regular expression character,
        // but we're using it as the delimiter so we need to escape it too.
        $escaped_chars = str_replace("/", "\/", $chars);

        // Check that it's alphanumeric + allowed special characters.
        if (preg_replace("/[^a-zA-Z0-9{$escaped_chars}]/", '', $value) !== $value) {
            $this->message = "The username must consist of letters, numbers, and {$chars} only.";

            return false;
        }

        // Check if it's numbers only.
        if (! $config['allow_numbers_only'] && preg_match('/^([0-9])+$/', $value) === 1) {
            $this->message = "The username cannot be numbers only.";

            return false;
        }

        // Check if it's special characters only.
        if (! $config['allow_special_chars_only'] && preg_match("/^([{$escaped_chars}])+$/", $value) === 1) {
            $this->message = "The username cannot be special characters only.";

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
        return $this->message;
    }
}
