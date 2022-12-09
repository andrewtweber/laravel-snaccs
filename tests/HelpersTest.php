<?php

namespace Snaccs\Tests;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use libphonenumber\NumberParseException;
use Snaccs\Models\Job;
use Snaccs\Models\SerializedJob;

/**
 * Class HelpersTest
 *
 * @package Snaccs\Tests
 */
class HelpersTest extends TestCase
{
    /**
     * @test
     */
    public function class_uses_deep()
    {
        $job = new Job();

        // Regular `class_uses` misses parent class traits
        $this->assertTrue(in_array(SerializedJob::class, class_uses($job)));
        $this->assertFalse(in_array(HasAttributes::class, class_uses($job)));

        // `class_uses_deep` captures them
        $this->assertTrue(in_array(SerializedJob::class, class_uses_deep($job)));
        $this->assertTrue(in_array(HasAttributes::class, class_uses_deep($job)));

        // Same tests on class name string vs. object
        $this->assertTrue(in_array(SerializedJob::class, class_uses(Job::class)));
        $this->assertFalse(in_array(HasAttributes::class, class_uses(Job::class)));
        $this->assertTrue(in_array(SerializedJob::class, class_uses_deep(Job::class)));
        $this->assertTrue(in_array(HasAttributes::class, class_uses_deep(Job::class)));
    }

    /**
     * @test
     *
     * @param array       $items
     * @param string|null $expected
     *
     * @testWith [[], null]
     *           [["lions"], "lions"]
     *           [["lions", "tigers"], "lions and tigers"]
     *           [["lions", "tigers", "bears"], "lions, tigers, and bears"]
     *           [["lions", "tigers", "bears", "oh my!"], "lions, tigers, bears, and oh my!"]
     */
    public function comma_separated(array $items, ?string $expected)
    {
        $this->assertSame($expected, comma_separated($items));
    }

    /**
     * @test
     *
     * @param int|null    $input
     * @param string|null $expected
     *
     * @testWith [null, null]
     *           [-311, "-311th"]
     *           [-204, "-204th"]
     *           [-103, "-103rd"]
     *           [-22, "-22nd"]
     *           [-11, "-11th"]
     *           [-4, "-4th"]
     *           [-3, "-3rd"]
     *           [-2, "-2nd"]
     *           [-1, "-1st"]
     *           [0, "0th"]
     *           [1, "1st"]
     *           [2, "2nd"]
     *           [3, "3rd"]
     *           [4, "4th"]
     *           [5, "5th"]
     *           [6, "6th"]
     *           [7, "7th"]
     *           [8, "8th"]
     *           [9, "9th"]
     *           [10, "10th"]
     *           [11, "11th"]
     *           [12, "12th"]
     *           [13, "13th"]
     *           [14, "14th"]
     *           [21, "21st"]
     *           [22, "22nd"]
     *           [23, "23rd"]
     *           [24, "24th"]
     *           [100, "100th"]
     *           [101, "101st"]
     *           [202, "202nd"]
     *           [303, "303rd"]
     *           [404, "404th"]
     *           [511, "511th"]
     *           [612, "612th"]
     *           [1013, "1013th"]
     *           [2014, "2014th"]
     *           [3121, "3121st"]
     */
    public function ordinal(?int $input, ?string $expected)
    {
        $this->assertSame($expected, ordinal($input));
    }

    /**
     * @test
     *
     * @param string|null $url
     * @param string|null $expected
     *
     * @testWith [null,                      null]
     *           ["google.com",              null]
     *           ["http://google.com",       "google.com"]
     *           ["http://www.google.com",   "google.com"]
     *           ["https://maps.google.com", "maps.google.com"]
     *           ["https://google.com/maps", "google.com"]
     */
    public function parse_domain(?string $url, ?string $expected)
    {
        $this->assertSame($expected, parse_domain($url));
    }

    /**
     * @test
     *
     * @param string|null $value
     * @param string|null $expected
     *
     * @testWith [null,                       null]
     *           ["",                         ""]
     *           ["_legal.",                  "_legal."]
     *           [".legal_",                  ".legal_"]
     *           ["_legal_",                  "_legal_"]
     *           [".legal.",                  ".legal."]
     *           [" ferretpapa ",             "ferretpapa"]
     *           ["ferret papa",              "ferret papa"]
     *           ["ferretpapa",               "ferretpapa"]
     *           ["@ferretpapa",              "ferretpapa"]
     *           [" @ ferretpapa ",           "ferretpapa"]
     *           ["/ferretpapa",              "/ferretpapa"]
     *           ["instagram.com/ferretpapa", "instagram.com/ferretpapa"]
     */
    public function parse_handle(?string $value, ?string $expected)
    {
        $this->assertSame($expected, parse_handle($value));
    }

    /**
     * @test
     *
     * @param string|null $value
     * @param array       $allowed_domains
     * @param string|null $expected
     *
     * @testWith ["instagram.com/ferretpapa",         ["instagram.com"], "ferretpapa"]
     *           ["instagram.com/ferretpapa/",        ["instagram.com"], "ferretpapa"]
     *           ["instagram.com/@ferretpapa",        ["instagram.com"], "ferretpapa"]
     *           ["https://instagram.com/ferretpapa", ["instagram.com"], "ferretpapa"]
     *           ["twitter.com/#!ferretpapa",         ["instagram.com"], "twitter.com/#!ferretpapa"]
     *           ["twitter.com/#!ferretpapa",         ["twitter.com"],   "ferretpapa"]
     *           ["instagram.com/ferretpapa",         ["twitter.com"],   "instagram.com/ferretpapa"]
     */
    public function parse_handle_with_domains(?string $value, array $allowed_domains, ?string $expected)
    {
        $this->assertSame($expected, parse_handle($value, $allowed_domains));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $expected
     *
     * @testWith [null,                     null]
     *           ["",                       null]
     *           ["1-555-111-2222",         "5551112222"]
     *           ["555.111.2222",           "5551112222"]
     *           ["555-111-2222",           "5551112222"]
     *           ["555-stanley",            "5557826539"]
     *           ["555-STANLEY",            "5557826539"]
     *           ["555 111 2222",           "5551112222"]
     *           ["(555) 111-2222",         "5551112222"]
     *           [" 1-555-111-2222 ",       "5551112222"]
     *           [" 555-111-2222 ",         "5551112222"]
     *           ["(555) 111-2222 EXT 123", "5551112222EXT123"]
     */
    public function parse_phone(?string $number, ?string $expected)
    {
        $this->assertSame($expected, parse_phone($number));
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $country
     * @param string|null $expected
     *
     * @testWith [null,               "US", null]
     *           ["",                 "US", null]
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
    public function parse_phone_with_country(?string $number, ?string $country_code, ?string $expected)
    {
        $this->assertSame($expected, parse_phone($number, $country_code));
    }

    /**
     * @test
     *
     * @param string|null $number
     *
     * @testWith ["   "]
     *           ["---"]
     *           ["-.-(-.-)-.-"]
     *           ["555-111-2222 ext 12 or 15"]
     */
    public function parse_invalid_phone(?string $number)
    {
        $this->expectException(NumberParseException::class);
        parse_phone($number);
    }

    /**
     * @test
     *
     * @param string|null $website
     * @param string|null $expected
     *
     * @testWith [null,                    null]
     *           ["",                      ""]
     *           ["   ",                   ""]
     *           ["---",                   "http://---"]
     *           ["http://",               ""]
     *           ["ftp://example.com",     "ftp://example.com"]
     *           ["http://example.com",    "http://example.com"]
     *           ["https://example.com",   "https://example.com"]
     *           ["example.com",           "http://example.com"]
     *           [" example.com ",         "http://example.com"]
     *           [" http://example.com ",  "http://example.com"]
     *           [" https://example.com ", "https://example.com"]
     */
    public function parse_website(?string $website, ?string $expected)
    {
        $this->assertSame($expected, parse_website($website));
    }

    /**
     * @test
     *
     * @param string|null $input
     * @param string|null $output
     *
     * @testWith [null,           null]
     *           ["",              ""]
     *           ["The USA",      "USA"]
     *           ["a cat",        "cat"]
     *           ["AN APPLE",     "APPLE"]
     *           ["These people", "These people"]
     *           ["apples",       "apples"]
     */
    public function strip_articles(?string $input, ?string $output)
    {
        $this->assertSame($output, strip_articles($input));
    }
}
