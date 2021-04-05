<?php

namespace Tests\Casts;

use Snaccs\Casts\Website;
use Tests\TestCase;

/**
 * Class WebsiteTest
 *
 * @package Tests\Casts
 */
class WebsiteTest extends TestCase
{
    /**
     * @test
     *
     * @param string|null $number
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
    public function set_value(?string $number, ?string $expected)
    {
        $cast = new Website();

        $this->assertSame($expected, $cast->set(null, "", $number, []));
    }
}
