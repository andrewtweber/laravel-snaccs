<?php

namespace Snaccs\Tests;

use Illuminate\Support\Facades\Config;

/**
 * Class PhoneTest
 *
 * @package Snaccs\Tests
 */
class PhoneTest extends LaravelTestCase
{
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
     *           ["555stanley",    "(555) STA-NLEY"]
     *           ["555STANLEY",    "(555) STA-NLEY"]
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
     * @param string|null $expected
     *
     * @testWith [null,            null]
     *           ["",              ""]
     *           ["   ",           ""]
     *           ["---",           ""]
     *           ["15551112222",   "555.111.2222"]
     *           ["5551112222",    "555.111.2222"]
     *           ["555stanley",    "555.STA.NLEY"]
     *           ["555STANLEY",    "555.STA.NLEY"]
     *           ["555.111.2222",  "555.111.2222"]
     *           ["555-111-2222",  "555.111.2222"]
     *           ["555 111 2222",  "555.111.2222"]
     *           ["(555)1112222",  "555.111.2222"]
     *           [" 15551112222 ", "555.111.2222"]
     *           [" 5551112222 ",  "555.111.2222"]
     *           ["4930901820",    "493.090.1820"]
     *           ["4915223433333", "4915223433333"]
     */
    public function format_phone_with_custom_format(?string $number, ?string $expected)
    {
        Config::set('formatting.phone.locales.US', 'XXX.XXX.YYYY');

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
     *           ["4930901820",    "DE", "+49 3090 1820"]
     *           ["4915223433333", "DE", "+49 1522 3433333"]
     */
    public function format_phone_with_country(?string $number, ?string $country, ?string $expected)
    {
        $this->assertSame($expected, format_phone($number, $country));
    }
}
