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
     * @return string
     */
    abstract public function render(): string;

    /**
     * @return HtmlString
     */
    public function html(): HtmlString
    {
        return new HtmlString($this->render());
    }
}
