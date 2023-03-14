<?php

namespace Snaccs\Menu\Icons;

/**
 * Class FontAwesomeDuotoneIcon
 *
 * @package Snaccs\Menu\Icons
 */
class FontAwesomeDuotoneIcon extends Icon
{
    /**
     * @param string $icon
     */
    public function __construct(
        public string $icon,
        public string $primary_color,
        public string $secondary_color,
        public float $primary_opacity = 1,
        public float $secondary_opacity = 1
    ) {
    }

    /**
     * @param string|null $classes
     *
     * @return string
     */
    public function render(?string $classes = null): string
    {
        return view('snaccs::menu.icons.fontawesome_duotone')
            ->with('icon', $this)
            ->with('classes', $classes)
            ->render();
    }
}
