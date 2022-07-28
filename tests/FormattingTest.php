<?php

namespace Snaccs\Tests;

use Illuminate\Support\Facades\Config;
use libphonenumber\PhoneNumberFormat;

/**
 * Class FormattingTest
 *
 * @package Snaccs\Tests
 */
class FormattingTest extends LaravelTestCase
{
    /**
     * @test
     */
    public function format_money_failure_not_divisible_by_10()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("cents_per_dollar must be a power of 10");

        Config::set('formatting.money.cents_per_dollar', 8);

        format_money(100);
    }

    /**
     * @test
     */
    public function format_money_failure_not_power_of_10()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("cents_per_dollar must be a power of 10");

        Config::set('formatting.money.cents_per_dollar', 20);

        format_money(100);
    }

    /**
     * @test
     *
     * @param int|null    $price_in_cents
     * @param string|null $expected
     *
     * @testWith [null,   null]
     *           [0,      "$0.00"]
     *           [1,      "$0.01"]
     *           [99,     "$0.99"]
     *           [100,    "$1.00"]
     *           [2000,   "$20.00"]
     *           [400000, "$4000.00"]
     *           [-1,     "-$0.01"]
     *           [-99,    "-$0.99"]
     *           [-100,   "-$1.00"]
     */
    public function format_money_with_defaults(?int $price_in_cents, ?string $expected)
    {
        $this->assertSame($expected, format_money($price_in_cents));
    }

    /**
     * @test
     *
     * @param int|null    $price_in_cents
     * @param string|null $expected
     *
     * @testWith [null, null]
     *           [0,    "0.00"]
     *           [1,    "0.01"]
     *           [2000, "20.00"]
     *           [-100, "-1.00"]
     */
    public function format_money_without_currency(?int $price_in_cents, ?string $expected)
    {
        Config::set('formatting.money.currency_prefix', '$');
        Config::set('formatting.money.currency_suffix', '!');

        $this->assertSame($expected, format_money($price_in_cents, false));
    }

    /**
     * @test
     *
     * @param int|null    $price_in_cents
     * @param string|null $expected
     *
     * @testWith [null,   null]
     *           [0,      "$0"]
     *           [1,      "$0.01"]
     *           [99,     "$0.99"]
     *           [100,    "$1"]
     *           [2000,   "$20"]
     *           [400000, "$4000"]
     *           [-1,     "-$0.01"]
     *           [-99,    "-$0.99"]
     *           [-100,   "-$1"]
     */
    public function format_money_with_options(?int $price_in_cents, ?string $expected)
    {
        Config::set('formatting.money.show_zero_cents', false);

        $this->assertSame($expected, format_money($price_in_cents));
    }

    /**
     * @test
     *
     * @param int|null    $price_in_cents
     * @param string|null $expected
     *
     * @testWith [null,   null]
     *           [0,      "¥0.000"]
     *           [1,      "¥0.001"]
     *           [99,     "¥0.099"]
     *           [100,    "¥0.100"]
     *           [2000,   "¥2.000"]
     *           [400000, "¥400.000"]
     *           [-1,     "-¥0.001"]
     *           [-99,    "-¥0.099"]
     *           [-1000,  "-¥1.000"]
     */
    public function format_money_as_yen(?int $price_in_cents, ?string $expected)
    {
        Config::set('formatting.money.currency_prefix', '¥');
        Config::set('formatting.money.cents_per_dollar', 1000);

        $this->assertSame($expected, format_money($price_in_cents));
    }

    /**
     * @test
     *
     * @param int|null    $price_in_cents
     * @param string|null $expected
     *
     * @testWith [null,   null]
     *           [0,      "¥0"]
     *           [1,      "¥0.001"]
     *           [99,     "¥0.099"]
     *           [100,    "¥0.100"]
     *           [2000,   "¥2"]
     *           [400000, "¥400"]
     *           [-1,     "-¥0.001"]
     *           [-99,    "-¥0.099"]
     *           [-1000,  "-¥1"]
     */
    public function format_money_as_yen_with_options(?int $price_in_cents, ?string $expected)
    {
        Config::set('formatting.money.currency_prefix', '¥');
        Config::set('formatting.money.cents_per_dollar', 1000);
        Config::set('formatting.money.show_zero_cents', false);

        $this->assertSame($expected, format_money($price_in_cents));
    }

    /**
     * @test
     *
     * @param int|null    $price_in_cents
     * @param string|null $expected
     *
     * @testWith [null,   null]
     *           [0,      "+€0.00_!"]
     *           [1,      "+€0.01_!"]
     *           [99,     "+€0.99_!"]
     *           [100,    "+€1.00_!"]
     *           [2000,   "+€20.00_!"]
     *           [400000, "+€4000.00_!"]
     *           [-1,     "(€0.01_)"]
     *           [-99,    "(€0.99_)"]
     *           [-100,   "(€1.00_)"]
     */
    public function format_money_with_config_strings(?int $price_in_cents, ?string $expected)
    {
        Config::set('formatting.money.currency_prefix', '€');
        Config::set('formatting.money.currency_suffix', '_');
        Config::set('formatting.money.positive_prefix', '+');
        Config::set('formatting.money.positive_suffix', '!');
        Config::set('formatting.money.negative_prefix', '(');
        Config::set('formatting.money.negative_suffix', ')');

        $this->assertSame($expected, format_money($price_in_cents));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $expected
     *
     * @testWith [null,            null]
     *           ["",              ""]
     *           ["   ",           ""]
     *           ["---",           ""]
     *           ["15551112222",   "(555) 111-2222"]
     *           ["5551112222",    "(555) 111-2222"]
     *           ["555stanley",    "(555) 782-6539"]
     *           ["555STANLEY",    "(555) 782-6539"]
     *           ["555.111.2222",  "(555) 111-2222"]
     *           ["555-111-2222",  "(555) 111-2222"]
     *           ["555 111 2222",  "(555) 111-2222"]
     *           ["(555)1112222",  "(555) 111-2222"]
     *           [" 15551112222 ", "(555) 111-2222"]
     *           [" 5551112222 ",  "(555) 111-2222"]
     *           ["4930901820",    "(493) 090-1820"]
     *           ["4915223433333", "4915223433333"]
     */
    public function format_phone(?string $number, ?string $expected)
    {
        $this->assertSame($expected, format_phone($number));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $country
     * @param string|null $expected
     *
     * @testWith ["5551112222",    "US", "(555) 111-2222"]
     *           ["15551112222",   "US", "(555) 111-2222"]
     *           ["5551112222",    "CA", "(555) 111-2222"]
     *           ["15551112222",   "CA", "(555) 111-2222"]
     *           ["5551112222",    null, "(555) 111-2222"]
     *           ["15551112222",   null, "(555) 111-2222"]
     *           ["4930901820",    "DE", "+49 30 901820"]
     *           ["4915223433333", "DE", "+49 491 5223433333"]
     */
    public function format_phone_with_country(?string $number, ?string $country, ?string $expected)
    {
        $this->assertSame($expected, format_phone($number, $country));
    }

    /**
     * @test
     *
     * 0 = E164, 1 = International, 2 = National, 3 = RFC3966
     *
     * @param string|null $number
     * @param string      $country
     * @param int         $format
     * @param string|null $expected
     *
     * @see PhoneNumberFormat
     *
     * @testWith ["5551112222",       "US", 0, "+15551112222"]
     *           ["5551112222",       "US", 1, "+1 555-111-2222"]
     *           ["5551112222",       "US", 2, "(555) 111-2222"]
     *           ["5551112222",       "US", 3, "tel:+1-555-111-2222"]
     *           ["5551112222EXT123", "US", 3, "tel:+1-555-111-2222;ext=123"]
     *           ["4930901820",       "DE", 0, "+4930901820"]
     *           ["4930901820",       "DE", 1, "+49 30 901820"]
     *           ["4930901820",       "DE", 2, "030 901820"]
     *           ["4930901820",       "DE", 3, "tel:+49-30-901820"]
     */
    public function format_phone_with_format(?string $number, string $country, int $format, ?string $expected)
    {
        $this->assertSame($expected, format_phone($number, $country, $format));
    }

    /**
     * @test
     *
     * @param int|null    $bytes
     * @param int         $precision
     * @param string|null $expected
     *
     * @testWith [null,          2, null]
     *           [0,             2, "0 bytes"]
     *           [1,             2, "1 bytes"]
     *           [1023,          2, "1023 bytes"]
     *           [1024,          0, "1 kb"]
     *           [1024,          1, "1 kb"]
     *           [1024,          2, "1 kb"]
     *           [1536,          2, "1.5 kb"]
     *           [1792,          2, "1.75 kb"]
     *           [1792,          3, "1.75 kb"]
     *           [1793,          2, "1.75 kb"]
     *           [1793,          3, "1.751 kb"]
     *           [2000,          1, "2 kb"]
     *           [2000,          2, "1.95 kb"]
     *           [2048,          2, "2 kb"]
     *           [2150,          2, "2.1 kb"]
     *           [2253,          2, "2.2 kb"]
     *           [1048576,       2, "1 MB"]
     *           [1073741824,    2, "1 GB"]
     *           [1099511627776, 2, "1 TB"]
     */
    public function format_bytes(?int $bytes, int $precision, ?string $expected)
    {
        $this->assertSame($expected, format_bytes($bytes, $precision));
    }

    /**
     * @test
     *
     * @param int|null    $bytes
     * @param int         $precision
     * @param string|null $expected
     *
     * @testWith [null,          2, null]
     *           [0,             2, "0 b"]
     *           [1,             2, "1 b"]
     *           [1023,          2, "1023 b"]
     *           [1024,          2, "1k"]
     *           [1048576,       2, "1M"]
     *           [1073741824,    2, "1G"]
     *           [1099511627776, 2, "1T"]
     */
    public function format_bytes_with_config(?int $bytes, int $precision, ?string $expected)
    {
        Config::set('formatting.bytes', [' b', 'k', 'M', 'G', 'T']);

        $this->assertSame($expected, format_bytes($bytes, $precision));
    }

    /**
     * @test
     */
    public function format_bytes_bytes_failure()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Bytes must be an integer >= 0");

        format_bytes(-1, 0);
    }

    /**
     * @test
     */
    public function format_bytes_precision_failure()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Precision must be an integer >= 0");

        format_bytes(0, -1);
    }
}
