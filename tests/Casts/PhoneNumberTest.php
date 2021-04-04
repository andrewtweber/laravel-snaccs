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
     */
    public function get_value()
    {
        $number = new PhoneNumber();

        $this->assertSame("(555) 111-2222", $number->get(null, null, " 15551112222 ", null));
        $this->assertSame("(555) 111-2222", $number->get(null, null, " 5551112222 ", null));
        $this->assertSame("(555) 111-2222", $number->get(null, null, "555.111.2222", null));
    }

    /**
     * @test
     */
    public function set_value()
    {
        $number = new PhoneNumber();

        $this->assertSame("5551112222", $number->set(null, null, " 1-555-111-2222 ", null));
        $this->assertSame("5551112222", $number->set(null, null, "(555) 111-2222", null));
        $this->assertSame("5551112222", $number->set(null, null, "555.111.2222", null));
    }
}
