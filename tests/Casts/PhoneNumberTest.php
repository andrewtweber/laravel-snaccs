<?php

namespace Tests\Casts;

use Snaccs\Casts\PhoneNumber;
use Tests\TestCase;

/**
 * Class PhoneNumberTest
 *
 * @package Tests\Casts
 */
class PhoneNumberTest extends TestCase
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
     */
    public function get_value(?string $number, ?string $expected)
    {
        $cast = new PhoneNumber();

        $this->assertSame($expected, $cast->get(null, null, $number, null));
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
     *           ["555-stanley",      "555STANLEY"]
     *           ["555-STANLEY",      "555STANLEY"]
     *           ["555 111 2222",     "5551112222"]
     *           ["(555) 111-2222",   "5551112222"]
     *           [" 1-555-111-2222 ", "5551112222"]
     *           [" 555-111-2222 ",   "5551112222"]
     */
    public function set_value(?string $number, ?string $expected)
    {
        $cast = new PhoneNumber();

        $this->assertSame($expected, $cast->set(null, null, $number, null));
    }
}
