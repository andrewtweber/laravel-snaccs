<?php

namespace Snaccs\Tests\Casts;

use Snaccs\Casts\Website;
use Snaccs\Support\Url;
use Snaccs\Tests\TestCase;

/**
 * Class WebsiteTest
 *
 * @package Snaccs\Tests\Casts
 */
class WebsiteTest extends TestCase
{
    /**
     * @test
     *
     * @param string|null $value
     * @param string|null $expected
     *
     * @testWith [null,             null]
     *           ["",               ""]
     *           ["http://",        "http://"]
     *           ["www.google.com", "www.google.com"]
     */
    public function get_value(?string $value, ?string $expected)
    {
        $cast = new Website();

        $result = $cast->get(null, "", $value, []);

        if ($value === null) {
            $this->assertNull($result);
        } else {
            $this->assertTrue($result instanceof Url);
            $this->assertSame($expected, (string)$result);
        }
    }

    /**
     * @test
     *
     * @param string|null $url
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
    public function set_value(?string $url, ?string $expected)
    {
        $cast = new Website();

        $this->assertSame($expected, $cast->set(null, "", $url, []));
    }
}
