# Laravel Snaccs

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/andrewtweber/laravel-snaccs/tree/master.svg?style=shield&circle-token=c18ef225f399c42f4f082abfe30379846900b1ec)](https://dl.circleci.com/status-badge/redirect/gh/andrewtweber/laravel-snaccs/tree/master)

## About

Some Laravel stuff that I use in pretty much every project

- [Installation](#installation)
- [Auth](#auth)
- [Formatting](#formatting)
- [Helpers](#helpers)
- [Casts](#casts)
- [Validation](#validation)
- [Models](#models)
- [Hashids](#hashids)
- [Fractal](#fractal)
- [Mail](#mail)
- [Misc](#misc)
- [Todo](#todo)

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require andrewtweber/laravel-snaccs
```

The formatting helpers and username validation rule use config files.
If you want to change the config, run:

```
php artisan vendor:publish --provider="Snaccs\Providers\SnaccsServiceProvider"
```

This will publish the files `config/formatting.php` and `config/system.php`.

## Auth

### Login Credentials

If you'd like your user to be able to login with either their email address or username,
use the `Snaccs\Auth\AuthenticatesUsers` trait on your `LoginController` instead of the
Laravel trait.

### Persistent Session

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

### Disabling Registration

You can easily disable registration by setting `system.registration_enabled` to false.

Then enable the middleware on your `RegisterController`:

```php
class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        $this->middleware([
            \Snaccs\Http\Middleware\RegistrationEnabled::class,
            'guest',
        ]);
    }
}
```

You will probably want to add some manual `@if` statements to your blade files
to hide the registration links when it's disabled.

If the registration page is accessed while it is disabled, an exception will be
thrown which will show the 403 error page. This exception is not logged by the `ErrorHandler`.

## Formatting

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

// Format phone
format_phone("5551112222"); // "(555) 111-2222"
format_phone("4930901820", "DE"); // "+49 30 901820"

// Format phone as clickable URL
format_phone("5551112222EXT123", "US", PhoneNumberFormat::RFC3966); // "tel:+1-555-111-2222;ext=123"

// Format bytes (precision defaults to 2)
format_bytes(-100); // RuntimeException
format_bytes(1); // "1 b"
format_bytes(1024); // "1 kb"
format_bytes(1793); // "1.75 kb"
format_bytes(1793, 3); // "1.751 kb"
format_bytes(1024*1024*1024); // "1 GB"

// With config override set to [" bytes", "k", ...]
format_bytes(1); // "1 bytes"
format_bytes(1024); // "1k"
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
dispatch_with_delay($job); // Defaults to 15 seconds between jobs
dispatch_with_delay($job, 60); // Wait 1 minute for first job, and 1 minute between jobs
dispatch_with_delay($job, 60, 600); // Wait 10 minutes for first job, then 1 minute between jobs

// Comma-separated
comma_separated(["lions"]); // "lions"
comma_separated(["lions", "tigers"]); // "lions and tigers"
comma_separated(["lions", "tigers", "bears"]); // "lions, tigers, and bears"

// Ordinal
ordinal(1); // "1st"
ordinal(2); // "2nd"
ordinal(11); // "11th"

// Phone numbers
parse_phone("1.555.111.2222"); // "5551112222"

// Parse domain (URL must be valid)
// This should be paired with the website validation rule & cast
parse_domain("http://google.com"); // "google.com"
parse_domain("http://www.google.com"); // "google.com"
parse_domain("http://maps.google.com"); // "maps.google.com"
parse_domain("http://www.google.com/example"); // "google.com"

// Parse social media handle
parse_handle("ferretpapa"); // "ferretpapa"
parse_handle("@ferretpapa"); // "ferretpapa"
parse_handle("instagram.com/ferretpapa/"); // "ferretpapa"
```

## Casts

Phone numbers will be stripped down when storing in the database and formatted nicely when
displaying them. Websites will be prefixed with `http://` if the URL scheme is missing.

Usage:

```php
use Illuminate\Database\Eloquent\Model;
use Snaccs\Casts\IpAddress;
use Snaccs\Casts\PhoneNumber;
use Snaccs\Casts\Website;
use Snaccs\Models\Interfaces\PhoneNumberable;

class Account extends Model implements PhoneNumberable
{
    protected $casts = [
        'phone' => PhoneNumber::class,
        'website' => Website::class,
        'ip_address' => IpAddress::class,
    ];

    /**
     * The phone number cast requires implementing the PhoneNumberable
     * interface which has this sole method. It should return the 2-letter
     * (ISO 3166-2) country code. You could hard code it to 'US', return
     * a database column, etc. Null value defaults to 'US'.
     */
    public function getCountryCode(): string
    {
        return $this->country;
    }
}

// Examples:
$account = new Account();
$account->phone = "1.555.111.2222"; // Stored as '5551112222'
echo $account->phone; // Displayed as "(555) 111-2222"

$account->website = "google.com"; // Stored as 'http://google.com'

// IP addresses should be stored as a binary(16) or varbinary(16) column
// Both IPv4 and IPv6 addresses are supported
$account->ip_address = "127.0.0.1";
$account->ip_address = "200d:31c4:1905:9eb2:3c7f:c45c:de78:42cd";
```

## Validation

Phone Number validation ignores extra characters and just checks that 7-15 digits
are supplied. If the country is CA/US/unspecified, it also verifies that there are
exactly ten digits (or a `1` followed by ten digits).

You can override the translations `phone_with_country` and `phone` in your validation
language file if you like.

```php
use Snaccs\Validation\Rules\PhoneNumber;

// Must be ten digits, or a `1` followed by ten digits.
// Extra characters (dot, dash, parentheses) are ignored.
// Blank strings and null values also pass
$rules = [
    'phone' => [new PhoneNumber()],
];
// "1-555-111-2222" passes
// "(800) 444-1111" passes
// "5551112222"     passes
// "555111222"      fails

// Same as above except blank strings and null values will fail
$rules = [
    'phone' => ['required', new PhoneNumber()],
];

// Must be a valid German phone number.
$rules = [
    'phone' => [new PhoneNumber('DE')],
];

// If the country is also set in the request, you can do something like
$rules = [
    'phone' => [new PhoneNumber($this->get('country'))],
];
```

The Website casting should be paired with the Website validation rule.
This validates the URL but allows them to omit the scheme (defaults to http).
It also allows you to restrict to specific domains.

You can override the translations `website_with_domain` and `website` in your validation
language file if you like.

```php
use Snaccs\Validation\Rules\Website;

// Any URL is allowed, doesn't need `http://` at the beginning
// Blank strings and null values also pass
$rules = [
    'website' => [new Website()],
];
// "google.com" passes
// "http://google.com" passes

// Same as above except blank strings and null values will fail
$rules = [
    'website' => ['required', new Website()],
];

// Any URL on yelp.com including subdomains is allowed
$rules = [
    'yelp_url' => [new Website(['yelp.com'])],
];
// "yelp.com/test"     passes
// "http://yelp.com"   passes
// "www.yelp.com/test" passes
// "biz.yelp.com/test" passes
// "fakeyelp.com"      fails

// Any URL on any of these domains and subdomains is allowed
$rules = [
    'facebook_url' => [new Website(['facebook.com', 'fb.com', 'fb.me'])],
];
// "facebook.com/test"  passes
// "m.fb.com/test"      passes
// "http://fb.me/test"  passes
// "instagram.com/test" fails
```

Username validation allows you to easily control username min/max lengths,
reserved words (e.g. "admin"), special characters, uniqueness, and more. 
See `config/system.php` for all of your options.

```php
use Snaccs\Validation\Rules\Username;

$rules = [
    'username' => ['required', new Username()],
];
// "test"   passes
// "_test_" passes
// "test,"  fails because commas are not allowed by default
// "admin"  fails because it is reserved
// "inuse"  fails if a user already has that username

// You can also pass a user object to the constructor. This is the equivalent of
// Rule::unique('users')->ignore($user->id)
$rules = [
    'username' => ['required', new Username(Auth::user())],
];
// "inuse" will pass if it's the Auth user's username
```

The password verification rule simply checks if the input password matches
the given user's current password.

```php
use Snaccs\Validation\Rules\VerifyPassword;

$rules = [
    'old_password' => ['required', new VerifyPassword(Auth::user())],
    'password' => ['required|confirmed|string|min:10'],
];
```

Social media validation rules accept valid handles (with the appropriate length
and special character checks) with or without the `@` prefix, and also accept URLs to
profiles. They should be used with the `parse_handle` method.

Some rules are pre-defined; you can also extend the `Handle` validation rule to add custom
ones. You can also override the `handle` translation message if you like.

```php
use Snaccs\Validation\Rules\Instagram;

$rules = [
    'instagram' => ['nullable', new Instagram()],
];
// "ferretpapa" passes
// "@ferretpapa" passes
// "instagram.com/ferretpapa" passes
// "illegal+chars" fails
// "string_that_exceeds_instagram_30_char_limit" fails
```

The latitude and longitude rules validate that the value is numeric and falls within the
allowed range (-90 to 90 for lat, -180 to 180 for lng).

```php
use Snaccs\Validation\Rules\Latitude;
use Snaccs\Validation\Rules\Longitude;

$rules = [
    'latitude' => ['required', new Latitude()],
    'longitude' => ['required', new Longitude()],
];
```

## Models

If you use the database to track jobs and failed jobs, you can use the
`Job` and `FailedJob` models to easily handle them. For example, in a controller
you could simply fetch `Job::count()` to determine if any jobs are currently
queued or `FailedJob::count()` to see if any have failed. 

The implementation is up to you, but these models help simplify some of the 
serialization, date casting, etc.

### Builders

The `AbstractBuilder` class is helpful for moving model creating/updating
logic outside of the controller and into a testable class. It also wraps the
functionality inside of a database transaction, which is especially useful if
you create related models and need to revert all of them upon failure.

```php
class AccountController extends Controller
{
    public function create(AccountRequest $request)
    {
        $account = (new AccountBuilder($request->all()))
            ->setUser($request->user())
            ->save();
            
        return response()->json($account);
    }
}
```

## Hashids

If your model has a hashed ID (to make URL guessing more difficult, etc.)
you can simply use the `HashedID` trait. Make sure to also publish the 
hashid config file (`php artisan vendor:publish`)

```php
use Illuminate\Database\Eloquent\Model;
use Snaccs\Hashids\HashedID;

class Account extends Model
{
    use HashedID;
    
    // Optionally specify the connection if different from default
    public static $hashids_connection = 'custom';
}
```

This automatically resolves routes for you. It also adds an accessor for a
`display_id` attribute. And it adds some helper methods: `findByDisplayId`
and `findByDisplayIdOrFail`. 

So you can define your routes and controllers like this, and the models
will automatically be resolved using the hashed ID instead of the integer ID.

```php
Route::get('accounts/{account}', [AccountController::class, 'show']);

class AccountController extends Controller {
    public function show(Account $account) {
        return view('accounts.show')->with('account', $account);
    }
}
```

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

## Mail

The `Attachment` and `Invite` classes make it easier to send calendar invites through email.
All you have to do is implement the `Schedulable` interface on your event class(es), and
then send an Invite attachment.

```php
use Illuminate\Database\Eloquent\Model;
use Snaccs\Mail\Invite;
use Snaccs\Mail\Schedulable;

class Event extends Model implements Schedulable {
    // ...truncated...
}

$event = Event::first();
$invite = new Invite($event, "recipient@example.com");
Mail::send(Mailable::class)->attach($invite);
```

## Misc


### Mobile / Desktop switching

Of course, responsive design is ideal, but in some cases your mobile site really does
need to look or behave differently. This package makes it easy to switch between mobile
and desktop for debugging purposes.

First add the middleware to your http Kernel. It needs to go after the AddQueuedCookiesToResponse
middleware, but otherwise the order doesn't really matter.

```php
class Kernel extends HttpKernel {
    protected $middlewareGroups = [
        'web' => [
            //...
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Snaccs\Http\Middleware\ForceMobileOrDesktop::class,
            Middleware\VerifyCsrfToken::class,
            //...
        ],
    ];
}
```

Next register the System singleton. In your `AppServiceProvider` `boot` method:

```php
use Snaccs\Services\System;

class AppServiceProvider extends ServiceProvider {
    public function boot() {
        $this->app->instance(System::class, new System());
    }
}
```

Now you can easily check if you're on mobile or desktop using the `is_mobile()` helper.
This value is cached on the singleton so that you're not running the same logic over and over.
Finally, you might want to add some quick links to toggle. For example in a blade file:

```blade
<footer>
    @if (is_mobile())
        <a href="?desktop">Desktop</a>
    @else
        <a href="?mobile">Mobile</a>
    @endif
</footer>
```

### Breadcrumbs

Use the `BreadcrumbCollection` and `Breadcrumb` classes to easily generate both HTML and LD+Json
breadcrumbs (for SEO).

```php
use Snaccs\Breadcrumbs\Breadcrumb;
use Snaccs\Breadcrumbs\BreadcrumbCollection;

class Company extends Model {
    public function breadcrumbs(): BreadcrumbCollection {
        $crumbs = [];
        $crumbs[] = new Breadcrumb('/companies', 'All Companies');
        $crumbs[] = new Breadcrumb($this->url, $this->name);
        return new BreadcrumbCollection($crumbs);
    }
}
```

Render it as HTML or as a script tag

```blade
{{ $company->breadcrumbs()->toListHtml() }}
{{ $company->breadcrumbs()->toHtml() }}
```

### Coordinates

Just a small helper class to deal with latitude and longitude pairs.

```php
use Snaccs\Support\Coordinates;

$coords = new Coordinates(10, -20);
(string)$coords; // "10,-20"

$coords->distanceFrom(new Coordinates(12.3455, -19.223));
```

## Todo

- assets config and views
- instead of relying on a manual script (gitcheck/laravel), have a console command
  that runs regularly and pushes to Slack if debug mode is enabled, etc.

GCFA:

- app/Support/Helpers class
- isAddress trait - address normalization and helper accessors
- require password change middleware
- gmail service, MailMessage
- photo processing
- Elastic search service, elasticquententity helper, command to reindex
  - make it more flexible to add new filters, sorts, etc.
  - merge with TS
- debug warning view & Appserviceprovider

TS:

- app/Helpers class - get remote image size esp.
- date range trait
- Linode SDK
  
Parangi:

- app/Helpers class
- cache exif, dimensions, file sizes, etc. scripts
- exif service
- hasDimensions trait

Beehive: nothing I think?
GuessTheCar / ATW / FerretLove

### Separate Packages

- Slugged model (slimak - but make it more reusable, add slug validation rule from TS)
- WordPress helpers (TS)
- MediaWiki helpers + wiki config (Parangi)
- General meta/analytics stuff:
  - shareable URLs (utm_* - TS shareable trait)
  - Google structured data interfaces/helpers (TS)
  - meta tag/FB og tag stuff (TS)

Later (they don't even work in the current apps):

- store in session if unauthenticated (TS)
- timezone basemodel, helper class (Parangi)

MONEY CAST
custom thousands/decimal separators for money
zero value options for money/bytes
