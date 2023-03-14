<?php

namespace Snaccs\Menu\Icons;

/**
 * Class FontAwesomeIcon
 *
 * @package Snaccs\Menu\Icons
 */
class FontAwesomeIcon extends Icon
{
    /**
     * @param string $icon
     */
    public function __construct(
        public string $icon
    ) {
    }

    /**
     * @param string|null $classes
     *
     * @return string
     */
    public function render(?string $classes = null): string
    {
        return view('snaccs::menu.icons.fontawesome')
            ->with('icon', $this)
            ->with('classes', $classes)
            ->render();
    }
}
