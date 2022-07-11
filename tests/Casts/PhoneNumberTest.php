<?php

namespace Snaccs\Tests\Casts;

use Snaccs\Casts\PhoneNumber;
use Snaccs\Tests\LaravelTestCase;
use Snaccs\Tests\TestModel;

/**
 * Class PhoneNumberTest
 *
 * @package Snaccs\Tests\Casts
 */
class PhoneNumberTest extends LaravelTestCase
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
    public function get_value(?string $number, ?string $expected)
    {
        $cast = new PhoneNumber();
        $model = new TestModel();

        $this->assertSame($expected, $cast->get($model, "", $number, []));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $country_code
     * @param string|null $expected
     *
     * @testWith ["5551112222",    "US", "(555) 111-2222"]
     *           ["15551112222",   "US", "(555) 111-2222"]
     *           ["5551112222",    "CA", "(555) 111-2222"]
     *           ["15551112222",   "CA", "(555) 111-2222"]
     *           ["5551112222",    null, "(555) 111-2222"]
     *           ["15551112222",   null, "(555) 111-2222"]
     *           ["4930901820",    "DE", "+49 30 901820"]
     *           ["4915223433333", "DE", "+49 1522 3433333"]
     */
    public function get_value_with_country(?string $number, ?string $country_code, ?string $expected)
    {
        $cast = new PhoneNumber();
        $model = new TestModel();
        $model->setCountryCode($country_code);

        $this->assertSame($expected, $cast->get($model, "", $number, []));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $expected
     *
     * @testWith [null,               null]
     *           ["",                 ""]
     *           ["   ",              ""]
     *           ["---",              ""]
     *           ["1-555-111-2222",   "5551112222"]
     *           ["555.111.2222",     "5551112222"]
     *           ["555-111-2222",     "5551112222"]
     *           ["555-stanley",      "5557826539"]
     *           ["555-STANLEY",      "5557826539"]
     *           ["555 111 2222",     "5551112222"]
     *           ["(555) 111-2222",   "5551112222"]
     *           [" 1-555-111-2222 ", "5551112222"]
     *           [" 555-111-2222 ",   "5551112222"]
     */
    public function set_value(?string $number, ?string $expected)
    {
        $cast = new PhoneNumber();
        $model = new TestModel();

        $this->assertSame($expected, $cast->set($model, "", $number, []));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $country
     * @param string|null $expected
     *
     * @testWith [null,               "US", null]
     *           ["",                 "US", ""]
     *           ["1-555-111-2222",   "US", "5551112222"]
     *           ["555.111.2222",     "US", "5551112222"]
     *           ["555-111-2222",     "US", "5551112222"]
     *           ["555-stanley",      "US", "5557826539"]
     *           ["555-STANLEY",      "US", "5557826539"]
     *           ["555 111 2222",     "US", "5551112222"]
     *           ["(555) 111-2222",   "US", "5551112222"]
     *           [" 1-555-111-2222 ", "US", "5551112222"]
     *           ["1-555-111-2222",   null, "5551112222"]
     *           ["555-111-2222",     null, "5551112222"]
     *           [" 555-111-2222 ",   "US", "5551112222"]
     *           ["+49 30 901820",    "DE", "4930901820"]
     *           ["+49 1522 3433333", "DE", "4915223433333"]
     */
    public function set_value_with_country(?string $number, ?string $country_code, ?string $expected)
    {
        $cast = new PhoneNumber();
        $model = new TestModel();
        $model->setCountryCode($country_code);

        $this->assertSame($expected, $cast->set($model, "", $number, []));
    }
}
