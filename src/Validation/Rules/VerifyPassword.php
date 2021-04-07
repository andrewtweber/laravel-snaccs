<?php

namespace Snaccs\Validation\Rules;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Hash;

/**
 * Class VerifyPassword
 *
 * @package Snaccs\Validation\Rules
 */
class VerifyPassword implements ImplicitRule
{
    /**
     * VerifyPassword constructor.
     *
     * @param Authenticatable $user
     */
    public function __construct(
        public Authenticatable $user
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
        return Hash::check($value, $this->user->getAuthPassword());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field is incorrect.';
    }
}
