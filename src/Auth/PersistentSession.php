<?php

namespace Snaccs\Auth;

use Illuminate\Support\Facades\Auth;

/**
 * Trait PersistentSession
 *
 * @package Snaccs\Auth
 */
trait PersistentSession
{
    /**
     * Register PersistentSessionGuard.
     *
     * @param string $guardName
     * @param string $guardClass
     */
    protected function registerPersistentSessionGuard(
        string $guardName = 'persistent_session',
        string $guardClass = PersistentSessionGuard::class
    ) {
        Auth::extend($guardName, function ($app, $name, array $config) use ($guardClass) {
            $provider = Auth::createUserProvider($config['provider'] ?? null);

            $guard = new $guardClass($name, $provider, $this->app['session.store']);

            // When using the remember me functionality of the authentication services we
            // will need to be set the encryption instance of the guard, which allows
            // secure, encrypted cookie values to get generated for those cookies.
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($this->app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($this->app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });
    }
}
