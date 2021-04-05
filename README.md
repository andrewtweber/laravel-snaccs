# Laravel Snaccs

## About

Some Laravel stuff that I use in pretty much every project

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require andrewtweber/laravel-snaccs
```

## Helpers

Some helper methods:

```php
// Format bytes (precision defaults to 2)
format_bytes(-100); // RuntimeException
format_bytes(1); // "1"
format_bytes(1024); // "1 kb"
format_bytes(1793); // "1.75 kb"
format_bytes(1793, 3); // "1.751 kb"
format_bytes(1024*1024*1024); // "1 GB"

// Ordinal
ordinal(1); // "1st"
ordinal(2); // "2nd"
ordinal(11); // "11th"

// Phone numbers
format_phone("5551112222"); // "(555) 111-2222"
format_phone("4930901820", "DE"); // "+49 3090 1820"
parse_phone("1.555.111.2222"); // "5551112222"

// Parse domain (URL must be valid)
// This should be paired with the website validation rule & cast
parse_domain("http://google.com"); // "google.com"
parse_domain("http://www.google.com"); // "google.com"
parse_domain("http://maps.google.com"); // "maps.google.com"
```

## Casts

Phone numbers will be stripped down when storing in the database and formatted nicely when
displaying them. Websites will be prefixed with `http://` if the URL scheme is missing.

Usage:

```php
use Illuminate\Database\Eloquent\Model;
use Snaccs\Casts\PhoneNumber;
use Snaccs\Casts\Website;

class Account extends Model
{
    protected $casts = [
        'phone' => PhoneNumber::class,
        'phone_de' => PhoneNumber::class . ':DE',
        'website' => Website::class,
    ];
}

// Examples:
$account = new Account();
$account->phone = "1.555.111.2222"; // Stored as '5551112222'
echo $account->phone; // Displayed as "(555) 111-2222"

$account->website = "google.com"; // Stored as 'http://google.com'
```

## Models

If you use the database to track jobs and failed jobs, you can use the
`Job` and `FailedJob` models to easily handle them. For example, in a controller
you could simply fetch `Job::count()` to determine if any jobs are currently
queued or `FailedJob::count()` to see if any have failed. 

The implementation is up to you, but these models help simplify some of the 
serialization, date casting, etc.

## Persistent Session

The regular Laravel session guard logs the user out of ALL sessions on every device
(by cycling the `remember_token`) when they logout. This solves that annoyance.

Add this to your `AuthServiceProvider::boot` method:

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

Then update `config/auth.php` and set the web driver to `persistent_session`.
Warning: all existing users will be required to log back in.

## Todo

All on GCFA:

- app/Support/Helpers class
- Slugged model
- isAddress trait
- abstract transformer (nullitem, nullcollection)
- require password change middleware
- mail attachments / calendar invites
- mail service maybe
- photo processing
- Elastic search service, elasticquententity helper, command to reindex
- log keeper, slack webhook url

TS:

- app/Helpers class
- abstract builder (DB transaction)
- mobile/desktop switching
- Google structured data
- shareable trait
- social media validation
- Website validation rule
- date range trait
- Linode SDK
- meta tag stuff
  
Parangi:

- app/Helpers class
- cache exif, dimensions, file sizes, etc. scripts
- validation rules
- exif service
- schedulable interface (copied from gcfa)
- hasDimensions trait

Beehive

Probably should go in separate packages:

- WordPress helpers (TS)
- MediaWiki helpers (Parangi)

Later (they don't even work in the current apps):

- store in session if unauthenticated (TS)
- timezone basemodel, helper class (Parangi)
