<?php

namespace Snaccs\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Snaccs\Exceptions\RegistrationDisabledException;

/**
 * Class RegistrationEnabled
 *
 * @package Snaccs\Http\Middleware
 */
class RegistrationEnabled
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws RegistrationDisabledException
     */
    public function handle($request, Closure $next)
    {
        if (! config('system.registration_enabled', true)) {
            throw new RegistrationDisabledException();
        }

        return $next($request);
    }
}
