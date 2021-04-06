<?php

namespace Snaccs\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers as BaseTrait;
use Illuminate\Http\Request;

/**
 * Trait AuthenticatesUsers
 *
 * @package Snaccs\Auth
 *
 * Available properties
 *   $fieldName - The name of the actual input field in the form. Defaults to "email"
 *   $usernameColumn - The username column on the `users` table. Defaults to "username"
 *   $emailColumn - The email column on the `users` table. Defaults to "email"
 */
trait AuthenticatesUsers
{
    use BaseTrait {
        login as baseLogin;
    }

    /**
     * @var string The selected column (set automatically) based on the input value.
     */
    protected string $username = 'email';

    /**
     * Handle a login request to the application.
     *
     * @inheritDoc
     */
    public function login(Request $request)
    {
        $fieldName = property_exists($this, 'fieldName') ? $this->fieldName : 'email';
        $emailColumn = property_exists($this, 'emailColumn') ? $this->emailColumn : 'email';
        $usernameColumn = property_exists($this, 'usernameColumn') ? $this->usernameColumn : 'username';

        // If the input field's value is an email address, use the email column
        // otherwise use the username column.
        $this->username = filter_var($request->get($fieldName), FILTER_VALIDATE_EMAIL)
            ? $emailColumn
            : $usernameColumn;

        $request->merge([
            $this->username => $request->get($fieldName),
        ]);

        return $this->baseLogin($request);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }
}
