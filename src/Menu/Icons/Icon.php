<?php

namespace Snaccs\Menu\Icons;

use Illuminate\Support\HtmlString;

/**
 * Class Icon
 *
 * @package Snaccs\Menu\Icons
 */
abstract class Icon
{
    /**
     * @param string|null $classes
     *
     * @return string
     */
    abstract public function render(?string $classes = null): string;

    /**
     * @return array<int, string>
     */
    public function classes(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function styles(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function styleString(): string
    {
        $styles = [];

        foreach ($this->styles() as $property => $value) {
            $styles[] = "{$property}:{$value}";
        }

        return implode(';', $styles);
    }

    /**
     * @param string|null $classes
     *
     * @return HtmlString
     */
    final public function html(?string $classes = null): HtmlString
    {
        return new HtmlString(trim($this->render($classes)));
    }
}
