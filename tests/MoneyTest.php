<?php

namespace Snaccs\Tests;

use Illuminate\Support\Facades\Config;

/**
 * Class MoneyTest
 *
 * @package Snaccs\Tests
 */
class MoneyTest extends LaravelTestCase
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
}
