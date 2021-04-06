<?php

namespace Snaccs\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers as BaseTrait;
use Illuminate\Http\Request;

/**
 * Trait AuthenticatesUsers
 *
 * @package Snaccs\Auth
 */
trait AuthenticatesUsers
{
    use BaseTrait {
        login as baseLogin;
    }

    /**
     * @var string The name of the actual input field in the form.
     */
    protected string $fieldName = 'email';

    /**
     * @var string The username column on the `users` table.
     */
    protected string $usernameColumn = 'username';

    /**
     * @var string The email column on the `users` table.
     */
    protected string $emailColumn = 'email';

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
        // If the input field's value is an email address, use the email column
        // otherwise use the username column.
        $this->username = filter_var($request->get($this->fieldName), FILTER_VALIDATE_EMAIL)
            ? $this->emailColumn
            : $this->usernameColumn;

        $request->merge([
            $this->username => $request->get($this->fieldName),
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
