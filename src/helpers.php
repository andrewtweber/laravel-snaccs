<?php

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Snaccs\Models\Job;
use Snaccs\Services\System;

if (! function_exists('class_uses_deep')) {
    /**
     * @param mixed $class
     * @param bool  $autoload
     *
     * @return array
     */
    #[Pure] function class_uses_deep($class, bool $autoload = true): array
    {
        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (! empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}

if (! function_exists('dispatch_with_delay')) {
    /**
     * Dispatch a job with a delay
     *
     * @param ShouldQueue|Queueable $job
     * @param int                   $delay
     * @param int|null              $initial_delay
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    function dispatch_with_delay(ShouldQueue $job, int $delay = 15, int $initial_delay = null)
    {
        if (! in_array(Queueable::class, class_uses_deep($job))) {
            throw new InvalidArgumentException(get_class($job) . " does not use Queueable trait");
        }

        /** @var Job $last_job */
        $last_job = Job::where('queue', $job->queue ?? 'default')
            ->orderBy('available_at', 'desc')
            ->first();

        // The soonest this job should be available.
        $earliest = Carbon::now()->addSeconds($initial_delay ?? $delay);

        if ($last_job && $last_job->available_at instanceof Carbon) {
            // If other queued jobs are pending, it should be at least `$delay` seconds
            // after the last one.
            $next_available = $last_job->available_at->addSeconds($delay);

            $job->delay($earliest->gt($next_available) ? $earliest : $next_available);
        } else {
            // Nothing in the queue - set the initial delay.
            $job->delay($earliest);
        }

        return dispatch($job);
    }
}

if (! function_exists('format_bytes')) {
    /**
     * Convert bytes to kb, MB, etc.
     *
     * @param int|null $bytes
     * @param int      $precision
     *
     * @return string|null
     */
    #[Pure] function format_bytes(?int $bytes, int $precision = 2): ?string
    {
        assert($bytes >= 0, new \InvalidArgumentException("Bytes must be an integer >= 0"));
        assert($precision >= 0, new \InvalidArgumentException("Precision must be an integer >= 0"));

        $suffixes = config('formatting.bytes');

        if ($bytes === null) {
            return null;
        }
        if ($bytes === 0) {
            return '0' . $suffixes[0];
        }

        $base = log($bytes, 1024);

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
}

if (! function_exists('format_money')) {
    /**
     * @param int|null $price_in_cents
     * @param bool     $show_currency
     *
     * @return string|null
     */
    #[Pure] function format_money(?int $price_in_cents, bool $show_currency = true): ?string
    {
        if ($price_in_cents === null) {
            return null;
        }

        // Check that the cents_per_dollar is a power of 10
        $cents_per_dollar = config('formatting.money.cents_per_dollar');
        $places = log($cents_per_dollar, 10);
        assert(
            fmod($places, 1) === 0.0 && $cents_per_dollar > 0,
            new \RuntimeException("cents_per_dollar must be a power of 10")
        );

        // How many decimal places to show
        $places = (config('formatting.money.show_zero_cents') || $price_in_cents % $cents_per_dollar !== 0)
            ? (int)$places : 0;

        // String replacements
        $replacements = [
            ($price_in_cents < 0 ? config('formatting.money.negative_prefix') : config('formatting.money.positive_prefix')),
            $show_currency ? config('formatting.money.currency_prefix') : '',
            abs($price_in_cents / $cents_per_dollar),
            $show_currency ? config('formatting.money.currency_suffix') : '',
            ($price_in_cents < 0 ? config('formatting.money.negative_suffix') : config('formatting.money.positive_suffix')),
        ];

        return sprintf("%s%s%01.{$places}f%s%s", ...$replacements);
    }
}

if (! function_exists('format_phone')) {
    /**
     * Display a phone number nicely
     *
     * @param string|null $number
     * @param string|null $country
     *
     * @return string|null
     * @todo if it's an alphabetical phone number, don't break it into pieces
     *
     */
    #[Pure] function format_phone(?string $number, string $country = null): ?string
    {
        if ($number === null) {
            return null;
        }

        $number = preg_replace('/[^0-9A-Z]/', '', strtoupper($number));

        if (! $number) {
            return '';
        }

        // If country is not provided, try to determine by the length of the number
        if ($country === null) {
            if (strlen($number) == 10) {
                $country = 'US';
            } elseif (strlen($number) == 11 && substr($number, 0, 1) == 1) {
                $country = 'US';
            }
        }

        $extension = null;
        $format = false;

        switch ($country) {
            case 'US':
            case 'CA':
                if (substr($number, 0, 1) == '1') {
                    $number = substr($number, 1);
                }

                if (Str::contains($number, 'EXT')) {
                    $parts = explode('EXT', $number);
                    $extension = array_pop($parts);
                    $number = implode('', $parts);
                }

                $format = config('formatting.phone.locales.US');
                break;

            case 'DE':
                $format = config('formatting.phone.locales.DE');
                break;

            case 'PL':
                $format = config('formatting.phone.locales.PL');
                break;
        }

        if ($format) {
            $start = 0;
            $formatted = '';

            for ($i = 0; $i < strlen($format); ++$i) {
                if ($format[$i] == 'Y') {
                    $formatted .= substr($number, $start);
                    break;
                } elseif ($format[$i] == 'X') {
                    $formatted .= substr($number, $start, 1);
                    ++$start;
                } else {
                    $formatted .= $format[$i];
                }
            }

            if ($extension) {
                $formatted .= config('formatting.phone.extension_separator') . $extension;
            }

            return $formatted;
        }

        return $number;
    }
}

if (! function_exists('is_mobile')) {
    /**
     * @return bool
     */
    #[Pure] function is_mobile(): bool
    {
        return app(System::class)->is_mobile;
    }
}

if (! function_exists('ordinal')) {
    /**
     * @param int|null $number
     *
     * @return string|null
     */
    #[Pure] function ordinal(?int $number): ?string
    {
        if ($number === null) {
            return null;
        }

        $digit = $number % 10;

        $suffix = match ($digit) {
            -1, 1 => 'st',
            -2, 2 => 'nd',
            -3, 3 => 'rd',
            default => 'th'
        };

        // 11-19 all end in 'th'
        $tens = ($number % 100) - $digit;
        if (abs($tens) === 10) {
            $suffix = 'th';
        }

        return $number . $suffix;
    }
}

if (! function_exists('parse_domain')) {
    /**
     * @param string|null $url
     *
     * @return string|null
     */
    #[Pure] function parse_domain(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $domain = parse_url($url, PHP_URL_HOST);

        return (Str::startsWith($domain, 'www.'))
            ? substr($domain, 4)
            : $domain;
    }
}

if (! function_exists('parse_handle')) {
    /**
     * Note: white space is trimmed, but internal white space is untouched.
     * The `/` character is trimmed only if the provided value is a URL.
     * The `@` character is always stripped out, all other special characters are untouched.
     * This should be paired with the Instagram/Twitter validation rules.
     *
     * @param string|null $url
     * @param array       $allowed_domains
     *
     * @return string|null
     */
    #[Pure] function parse_handle(?string $url, array $allowed_domains = []): ?string
    {
        if ($url === null) {
            return null;
        }

        if (empty($allowed_domains) || ! Str::contains($url, $allowed_domains)) {
            return trim(str_replace('@', '', $url));
        }

        $url = parse_website($url);
        $url = str_replace('#!', '', $url);
        $handle = parse_url($url, PHP_URL_PATH);

        return trim(str_replace('@', '', $handle), '/');
    }
}

if (! function_exists('parse_phone')) {
    /**
     * @param string|null $value
     *
     * @return string|null
     */
    #[Pure] function parse_phone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/[^0-9A-Z]/', '', strtoupper($value));

        if (strlen($value) == 11 && substr($value, 0, 1) == 1) {
            $value = substr($value, 1);
        }

        return $value;
    }
}

if (! function_exists('parse_website')) {
    /**
     * @param string|null $value
     *
     * @return string|null
     */
    #[Pure] function parse_website(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if (strlen($value) > 0 && ! Str::contains($value, '://')) {
            $value = 'http://' . $value;
        }
        if ($value == 'http://') {
            $value = '';
        }

        return $value;
    }
}
