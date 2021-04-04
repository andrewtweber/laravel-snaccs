<?php

use Illuminate\Support\Str;

if (! function_exists('class_uses_deep')) {
    /**
     * @param mixed $class
     * @param bool  $autoload
     *
     * @return array
     */
    function class_uses_deep($class, bool $autoload = true): array
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

if (! function_exists('format_bytes')) {
    /**
     * Convert bytes to kb, MB, etc.
     *
     * @param int|null $bytes
     * @param int      $precision
     *
     * @return string|null
     */
    function format_bytes(?int $bytes, int $precision = 2): ?string
    {
        assert($bytes >= 0, new \RuntimeException("Bytes must be an integer >= 0"));
        assert($precision >= 0, new \RuntimeException("Precision must be an integer >= 0"));

        if ($bytes === null) {
            return null;
        }
        if ($bytes === 0) {
            return '0';
        }

        $base = log($bytes, 1024);
        $suffixes = ['', ' kb', ' MB', ' GB', ' TB'];

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
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
     */
    function format_phone(?string $number, string $country = null): ?string
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

                $format = '(XXX) XXX-YYYY';
                break;

            case 'DE':
                $format = '+XX XXXX YYYYY';
                break;

            case 'PL':
                $format = '+XX XX XXX XX YY';
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
                $formatted .= ' ext ' . $extension;
            }

            return $formatted;
        }

        return $number;
    }
}

if (! function_exists('ordinal')) {
    /**
     * @param int|null $number
     *
     * @return string|null
     */
    function ordinal(?int $number): ?string
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
    function parse_domain(?string $url): ?string
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

if (! function_exists('parse_phone')) {
    /**
     * @param string|null $value
     *
     * @return string|null
     */
    function parse_phone(?string $value): ?string
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
