<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\Website;

/**
 * Class WebsiteTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class WebsiteTest extends LaravelTestCase
{
    /**
     * @test
     *
     * @param string|null $url
     * @param array       $allowedDomains
     *
     * @testWith [null,                          []]
     *           ["",                            []]
     *           ["google.com",                  []]
     *           ["http://google.com",           []]
     *           ["google.com/test",             []]
     *           ["http://google.com/test",      []]
     *           ["www.google.com",              []]
     *           ["http://www.google.com",       []]
     *           ["www.google.com/test",         []]
     *           ["http://www.google.com/test",  []]
     *           ["google.com",                  ["google.com"]]
     *           ["http://google.com",           ["google.com"]]
     *           ["google.com/test",             ["google.com"]]
     *           ["http://google.com/test",      ["google.com"]]
     *           ["www.google.com",              ["google.com"]]
     *           ["http://www.google.com",       ["google.com"]]
     *           ["www.google.com/test",         ["google.com"]]
     *           ["http://www.google.com/test",  ["google.com"]]
     *           ["maps.google.com",             ["google.com"]]
     *           ["http://maps.google.com",      ["google.com"]]
     *           ["maps.google.com/test",        ["google.com"]]
     *           ["http://maps.google.com/test", ["google.com"]]
     *           ["example.com",                 ["example.com", "google.com"]]
     *           ["google.com",                  ["example.com", "google.com"]]
     *           ["www.example.com",             ["example.com", "google.com"]]
     *           ["www.google.com",              ["example.com", "google.com"]]
     *           ["maps.example.com",            ["example.com", "google.com"]]
     *           ["maps.google.com",             ["example.com", "google.com"]]
     *           ["www.google.com",              ["www.google.com", "maps.google.com"]]
     *           ["maps.google.com",             ["www.google.com", "maps.google.com"]]
     */
    public function passes(?string $url, array $allowedDomains)
    {
        $rule = new Website($allowedDomains);

        $this->assertTrue($rule->passes('website', $url));
    }

    /**
     * @test
     *
     * @param string|null $url
     * @param array       $allowedDomains
     *
     * @testWith ["1,",             []]
     *           ["google.com",     ["example.com"]]
     *           ["google.com",     ["fakegoogle.com"]]
     *           ["fakegoogle.com", ["google.com"]]
     *           ["google.com",     ["www.google.com", "maps.google.com"]]
     */
    public function fails(?string $url, array $allowedDomains)
    {
        $rule = new Website($allowedDomains);

        $this->assertFalse($rule->passes('website', $url));
    }

    /**
     * @test
     */
    public function message()
    {
        $rule = new Website();
        $this->assertSame("The :attribute field is not a valid URL.", $rule->message());

        $rule = new Website(['google.com']);
        $this->assertSame("The :attribute field is not a valid google.com URL.", $rule->message());

        $rule = new Website(['facebook.com', 'fb.com', 'fb.me']);
        $this->assertSame("The :attribute field is not a valid facebook.com URL.", $rule->message());
    }
}
