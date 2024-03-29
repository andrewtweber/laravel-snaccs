<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\PhoneNumber;

/**
 * Class PhoneNumberTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class PhoneNumberTest extends LaravelTestCase
{
    /**
     * @test
     *
     * @param string|null $number
     *
     * @testWith [null]
     *           [""]
     *           ["1234567890"]
     *           ["12345678901"]
     *           ["5551112222"]
     *           ["555-111-2222"]
     *           ["1-555-111-2222"]
     *           ["(555) 111-2222"]
     *           [" 5551112222 "]
     *           ["555STANLEY"]
     *           ["555-STANLEY"]
     *           ["1-555-STANLEY"]
     *           ["(555) STANLEY"]
     *           [" 555STANLEY "]
     *           ["555-111-2222 ext. 123"]
     *           ["(555) 111-2222 EXT 123"]
     */
    public function passes(?string $number)
    {
        $rule = new PhoneNumber();

        $this->assertTrue($rule->passes('phone', $number));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string      $country_code
     *
     * @testWith ["1234567", "DE"]
     *           ["5551112222", "US"]
     *           ["555-111-2222", "US"]
     *           ["15551112222", "US"]
     *           ["493456789", "DE"]
     *           ["04934567890", "DE"]
     *           ["493456789012345", "DE"]
     *           ["555-111-2222 ext. 123", "US"]
     *           ["(555) 111-2222 EXT 123", "US"]
     */
    public function passes_with_country(?string $number, string $country_code)
    {
        $rule = new PhoneNumber($country_code);

        $this->assertTrue($rule->passes('phone', $number));
    }

    /**
     * @test Must be exactly 10 digits if US/CA
     *
     * @param string|null $number
     *
     * @testWith ["asdf"]
     *           ["123456789"]
     *           ["01234567890"]
     */
    public function fails(?string $number)
    {
        $rule = new PhoneNumber();

        $this->assertFalse($rule->passes('phone', $number));
        $this->assertSame("The :attribute field is not a valid phone number.", $rule->message());
    }

    /**
     * @test Must be between 7-15 digits if not US/CA
     *
     * @param string|null $number
     * @param string      $country_code
     *
     * @testWith ["asdf", "DE"]
     *           ["4934", "DE"]
     *           ["45551112222", "US"]
     *           ["4934567890123456", "DE"]
     */
    public function fails_with_country(?string $number, string $country_code)
    {
        $rule = new PhoneNumber($country_code);

        $this->assertFalse($rule->passes('phone', $number));
        $this->assertSame("The :attribute field is not a valid {$country_code} phone number.", $rule->message());
    }
}
