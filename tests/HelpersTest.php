<?php

namespace Tests;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Snaccs\Models\Job;
use Snaccs\Models\SerializedJob;

/**
 * Class HelpersTest
 *
 * @package Tests
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
    }

    /**
     * @test
     *
     * @param int|null    $bytes
     * @param int         $precision
     * @param string|null $expected
     *
     * @testWith [null,          2, null]
     *           [0,             2, "0"]
     *           [1,             2, "1"]
     *           [1023,          2, "1023"]
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
     */
    public function format_bytes_bytes_failure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Bytes must be an integer >= 0");

        format_bytes(-1, 0);
    }

    /**
     * @test
     */
    public function format_bytes_precision_failure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Precision must be an integer >= 0");

        format_bytes(0, -1);
    }

    /**
     * @test
     *
     * @param string|null $number
     * @param string|null $expected
     *
     * @testWith [null,            null]
     *           ["15551112222",   "(555) 111-2222"]
     *           ["5551112222",    "(555) 111-2222"]
     *           ["555STANLEY",    "(555) STA-NLEY"]
     *           ["555.111.2222",  "(555) 111-2222"]
     *           ["555-111-2222",  "(555) 111-2222"]
     *           ["555 111 2222",  "(555) 111-2222"]
     *           ["(555)1112222",  "(555) 111-2222"]
     *           [" 15551112222 ", "(555) 111-2222"]
     *           [" 5551112222 ",  "(555) 111-2222"]
     */
    public function format_phone(?string $number, ?string $expected)
    {
        $this->assertSame($expected, format_phone($number));
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
     * @testWith [null, null]
     *           ["google.com", null]
     *           ["http://google.com", "google.com"]
     *           ["http://www.google.com", "google.com"]
     *           ["https://maps.google.com", "maps.google.com"]
     *           ["https://google.com/maps", "google.com"]
     */
    public function parse_domain(?string $url, ?string $expected)
    {
        $this->assertSame($expected, parse_domain($url));
    }
}
