# Laravel Snaccs

## About

Some Laravel stuff that I use in pretty much every project

- [Installation](#installation)
- [Formatting](#formatting)
- [Helpers](#helpers)
- [Casts](#casts)
- [Validation](#validation)
- [Models](#models)
- [Persistent Session](#persistent-session)
- [Fractal](#fractal)
- [Todo](#todo)

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require andrewtweber/laravel-snaccs
```

## Formatting

The formatting helpers use a config file. If you want to change the config, run:

```
php artisan vendor:publish --provider="Snaccs\Providers\SnaccsServiceProvider"
```

This will publish the file `config/formatting.php`.

```php
// Format money with defaults
format_money(0); // "$0.00"
format_money(1); // "$0.01"
format_money(100); // "$1.00"
format_money(-200); // "-$2.00"

// Quick option not to show currency
format_money(1, false); // "0.01"
format_money(-200, false); // "-2.00"

// With config strings "€" currency prefix, "(" negative prefix, and ")" negative suffix
format_money(100); // "€1.00"
format_money(-200); // "(€2.00)"

// If show_zero_cents is set to false
format_money(100); // "$1"
format_money(101); // "$1.01"

// Format phone with defaults
format_phone("5551112222"); // "(555) 111-2222"
format_phone("4930901820", "DE"); // "+49 3090 1820"

// With config override
format_phone("5551112222"); // "555.111.2222"
```

## Helpers

Some helper methods:

```php
// If you dispatch too many jobs at once, e.g. emails, you can easily hit 
// third-party API rate limits, etc. This is a quick and easy way of ensuring
// that jobs are spaced out with a minimum delay. Each queue will have its
// delay tracked separately.
// Note that the job class must implement the ShouldQueue interface and use
// the Queueable trait.
dispatch_with_delay($job); // Defaults to 15 seconds
dispatch_with_delay($job, 60); // 1 minute

// Ordinal
ordinal(1); // "1st"
ordinal(2); // "2nd"
ordinal(11); // "11th"

// Format bytes (precision defaults to 2)
format_bytes(-100); // RuntimeException
format_bytes(1); // "1"
format_bytes(1024); // "1 kb"
format_bytes(1793); // "1.75 kb"
format_bytes(1793, 3); // "1.751 kb"
format_bytes(1024*1024*1024); // "1 GB"

// Phone numbers
parse_phone("1.555.111.2222"); // "5551112222"

// Parse domain (URL must be valid)
// This should be paired with the website validation rule & cast
parse_domain("http://google.com"); // "google.com"
parse_domain("http://www.google.com"); // "google.com"
parse_domain("http://maps.google.com"); // "maps.google.com"
parse_domain("http://www.google.com/example"); // "google.com"
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

## Validation

Phone Number validation ignores extra characters and just checks that 7-15 digits
are supplied. If the country is CA/US/unspecified, it also verifies that there are
exactly ten digits (or a `1` followed by ten digits).

```php
use Snaccs\Validation\Rules\PhoneNumber;

// Must be ten digits, or a `1` followed by ten digits.
// Extra characters (dot, dash, parentheses) are ignored.
// Blank strings and null values also pass
$rules = [
    'phone' => [new PhoneNumber()],
];

// Same as above except blank strings and null values will fail
$rules = [
    'phone' => ['required', new PhoneNumber()],
];

// Must be between 7-15 digits.
$rules = [
    'phone' => [new PhoneNumber('DE')],
];
```

The Website casting should be paired with the Website validation rule.
This validates the URL but allows them to omit the scheme (defaults to http).
It also allows you to restrict to specific domains.

```php
use Snaccs\Validation\Rules\Website;

// Any URL is allowed, doesn't need `http://` at the beginning
// Blank strings and null values also pass
$rules = [
    'website' => [new Website()],
];

// Same as above except blank strings and null values will fail
$rules = [
    'website' => ['required', new Website()],
];

// Any URL on yelp.com including subdomains is allowed
$rules = [
    'yelp_url' => [new Website(['yelp.com'])],
];

// Any URL on any of these domains and subdomains is allowed
$rules = [
    'facebook_url' => [new Website(['facebook.com', 'fb.com', 'fb.me'])],
];
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

Add this trait to your `AuthServiceProvider` and register inside the `boot` method.
If necessary you can override the guard name and class.

```php
use Snaccs\Auth\PersistentSession;

class AuthServiceProvider extends ServiceProvider
{
    use PersistentSession;
    
    public function boot()
    {
        $this->registerPersistentSessionGuard();
    }
}
```

Then update `config/auth.php` and set the web driver to `persistent_session`.
Warning: all existing users will be required to log back in.

## Fractal

A base transformer is available which distinguishes between null items (`null`)
and null collections (empty array). It also simplifies relationships by handling
null values automatically and defaulting to a `toArray` transformer if one isn't specified.

```php
use App\User;
use Snaccs\Fractal\EloquentTransformer;

class UserTransformer extends EloquentTransformer
{
    protected $availableIncludes = ['avatar', 'posts'];

    /**
     * You can easily include any type of Eloquent relationship.
     * If the related object/collection is null it will handle that for you.
     * If no transformer is passed in, it will call `toArray` on the object(s).  
     */ 
    public function includeAvatar(User $user)
    {
        return $this->hasOne($user->avatar);        
    }
    
    /**
     * If you have a transformer defined for the related model you can pass that in.
     */
    public function includePosts(User $user)
    {
        return $this->hasMany($user->posts, new PostTransformer);
    }
}
```

## Todo

Validation

- check hashed password (parangi) 
- Instagram/Twitter handles (TS)
- slug (TS)

Helpers

- format phone (Beehive - has glue pieces)

GCFA:

- app/Support/Helpers class
- Slugged model
- isAddress trait
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
- date range trait
- Linode SDK
- meta tag stuff
  
Parangi:

- app/Helpers class
- cache exif, dimensions, file sizes, etc. scripts
- exif service
- schedulable interface (copied from gcfa)
- hasDimensions trait

Beehive: nothing I think?

Probably should go in separate packages:

- WordPress helpers (TS)
- MediaWiki helpers (Parangi)

Later (they don't even work in the current apps):

- store in session if unauthenticated (TS)
- timezone basemodel, helper class (Parangi)

MONEY CAST
