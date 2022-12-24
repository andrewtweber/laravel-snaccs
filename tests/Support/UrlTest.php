<?php

namespace Snaccs\Tests\Support;

use Snaccs\Support\Url;
use Snaccs\Support\UrlAttribute;
use Snaccs\Tests\TestCase;

/**
 * Class UrlTest
 *
 * @package Snaccs\Tests\Support
 */
class UrlTest extends TestCase
{
    /**
     * @test
     */
    public function url_methods()
    {
        $url = new Url('https://google.com');
        $this->assertSame('https://google.com', (string)$url);
        $this->assertSame('google.com', $url->base_domain);
        $this->assertSame('google.com', $url->domain);
        $this->assertNull($url->subdomain);

        $url = new Url('https://www.google.com');
        $this->assertSame('https://www.google.com', (string)$url);
        $this->assertSame('google.com', $url->base_domain);
        $this->assertSame('google.com', $url->domain);
        $this->assertNull($url->subdomain);

        $url = new Url('https://adsense.google.com');
        $this->assertSame('https://adsense.google.com', (string)$url);
        $this->assertSame('google.com', $url->base_domain);
        $this->assertSame('adsense.google.com', $url->domain);
        $this->assertSame('adsense', $url->subdomain);

        $url = new Url('https://start.adsense.google.com');
        $this->assertSame('https://start.adsense.google.com', (string)$url);
        $this->assertSame('google.com', $url->base_domain);
        $this->assertSame('start.adsense.google.com', $url->domain);
        $this->assertSame('start.adsense', $url->subdomain);

        $this->expectError();
        $this->assertNull($url->nonexistent_field);
    }

    /**
     * @test
     */
    public function html_generation()
    {
        $url = new Url('https://google.com');

        $this->assertSame(
            '<a href="https://google.com">https://google.com</a>',
            (string)$url->toHtml()
        );

        $this->assertSame(
            '<a href="https://google.com">google.com</a>',
            (string)$url->toHtml(UrlAttribute::Domain)
        );

        $this->assertSame(
            '<a href="https://google.com">google.com</a>',
            (string)$url->toHtml($url->domain)
        );

        $this->assertSame(
            '<a href="https://google.com" class="btn btn-primary">website</a>',
            (string)$url->toHtml(text: 'website', class: 'btn btn-primary')
        );

        $this->assertSame(
            '<a href="https://google.com" rel="nofollow">website</a>',
            (string)$url->toHtml(text: 'website', rel: 'nofollow')
        );

        $this->assertSame(
            '<a href="https://google.com" target="_blank">website</a>',
            (string)$url->toHtml(text: 'website', target: '_blank')
        );

        $this->assertSame(
            '<a href="https://google.com" class="btn" rel="nofollow" target="_blank">website</a>',
            (string)$url->toHtml(text: 'website', class: 'btn', rel: 'nofollow', target: '_blank')
        );
    }
}
