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
     * @param string|null $classes
     *
     * @return HtmlString
     */
    public function html(?string $classes = null): HtmlString
    {
        return new HtmlString($this->render($classes));
    }
}
