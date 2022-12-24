<?php

namespace Snaccs\Support;

use Illuminate\Support\HtmlString;

/**
 * Class Url
 *
 * @package Snaccs\Support
 *
 * @property string $base_domain
 * @property string $domain
 * @property string $subdomain
 */
class Url
{
    /**
     * TODO: should this URL be validated when it is set?
     *
     * @param string $url
     */
    public function __construct(
        public string $url,
    ) {
    }

    /**
     * @param string $name
     *
     * @return null
     */
    public function __get(string $name)
    {
        $attribute = UrlAttribute::from($name);

        $domain = parse_domain($this->url);

        switch ($attribute) {
            case UrlAttribute::BaseDomain:
                $parts = explode('.', $domain);
                $base = [];
                $base[] = array_pop($parts);
                $base[] = array_pop($parts);

                return implode('.', array_reverse($base));

            case UrlAttribute::Domain:
                return $domain;

            case UrlAttribute::Subdomain:
                $parts = explode('.', $domain);
                array_pop($parts);
                array_pop($parts);

                return empty($parts) ? null : implode('.', $parts);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->url;
    }

    /**
     * @param UrlAttribute|string|null $text
     * @param string|null              $class
     * @param string|null              $rel
     * @param string|null              $target
     *
     * @return HtmlString
     */
    public function toHtml(
        UrlAttribute|string|null $text = null,
        ?string $class = null,
        ?string $rel = null,
        ?string $target = null,
    ): HtmlString {
        if ($text === UrlAttribute::Domain) {
            $text = $this->domain;
        }
        if ($text === null) {
            $text = $this->url;
        }

        $html = '<a href="' . $this->url . '"'
            . ($class ? ' class="' . $class . '"' : '')
            . ($rel ? ' rel="' . $rel . '"' : '')
            . ($target ? ' target="' . $target . '"' : '')
            . '>' . $text . '</a>';

        return new HtmlString($html);
    }
}
