<?php

namespace Snaccs\Auth;

use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\SessionGuard as Guard;

/**
 * Class PersistentSessionGuard
 *
 * @package Snaccs\Auth
 */
class PersistentSessionGuard extends Guard
{
    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();

        $this->clearUserDataFromStorage();

        // DON'T REGENERATE THEIR REMEMBER TOKEN
        //if (! is_null($this->user) && ! empty($user->getRememberToken())) {
        //    $this->cycleRememberToken($user);
        //}

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        if (isset($this->events)) {
            $this->events->dispatch(new Logout($this->name, $user));
        }

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;
    }
}
