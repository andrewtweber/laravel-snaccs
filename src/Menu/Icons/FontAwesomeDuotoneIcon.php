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
     * @return string
     */
    public function render(): string
    {
        return view('snaccs::menu.icons.fontawesome_duotone')
            ->with('icon', $this)
            ->render();
    }
}
