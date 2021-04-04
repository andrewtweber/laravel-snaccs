<?php

use Illuminate\Support\Str;

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

        $number = trim(preg_replace('/[^0-9A-Za-z]/', '', $number));

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
