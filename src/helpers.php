<?php

use Illuminate\Support\Str;

if (! function_exists('format_phone')) {
    /**
     * @param string|null $number
     * @param string|null $country
     *
     * @return string|null
     */
    function format_phone(?string $number, string $country = null)
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
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th'
        };

        // 11-19 all end in 'th'
        $tens = ($number % 100) - $digit;
        if ($tens === 10) {
            $suffix = 'th';
        }

        return $number . $suffix;
    }
}
