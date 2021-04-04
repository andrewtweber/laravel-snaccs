# Laravel Snaccs

## About

Some Laravel stuff that I use in pretty much every project

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require andrewtweber/laravel-snaccs
```

## Casts

Format phone numbers. This will strip them down when storing in the database
and format them nicely when displaying them.

Usage:

```php
use Snaccs\Casts\PhoneNumber;

class Account extends Eloquent
{
    protected $casts = [
        'phone' => PhoneNumber::class,
    ];
}
```

## Persistent Session

The regular Laravel session guard logs the user out of ALL sessions on every device
(by cycling the `remember_token`) when they logout. This solves that annoyance.

Add to your `AppServiceProvider::boot` method:

```php
use Illuminate\Support\Facades\Auth;
use Snaccs\Auth\PersistentSessionGuard;

Auth::extend('persistent_session', function ($app, $name, array $config) {
    $provider = Auth::createUserProvider($config['provider'] ?? null);

    $guard = new PersistentSessionGuard($name, $provider, $this->app['session.store']);

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
```

Then update `config/auth.php` and set the web driver to `persistent_session`


## Todo

All on GCFA:

- helper functions
- Slugged model
- isAddress trait
- Serialized Job/FailedJob models
- abstract transformer (nullitem, nullcollection)
- require password change middleware
- mail attachments / calendar invites
- mail service maybe
- photo processing
- Elastic search service, elasticquententity helper, command to reindex
- log keeper, slack webhook url

TS:

- helper functions
- abstract builder (DB transaction)
- mobile/desktop switching
- store in session if unauthenticated
- Google structured data
- shareable trait
- social media validation
- Website/Domain helper
- Website validation rule
- date range trait
- Linode SDK
- meta tag stuff
- WordPress helpers
  
Parangi:

- mediawiki helpers
- timezone basemodel, helper class
- cache exif, dimensions, file sizes, etc. scripts
- validation rules
- exif service
- schedulable interface (copied from gcfa)
- hasDimensions trait

FerretLove
