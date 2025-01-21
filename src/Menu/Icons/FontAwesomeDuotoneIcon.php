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
     * @param string $primary_color
     * @param string $secondary_color
     * @param float $primary_opacity
     * @param float $secondary_opacity
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
     * @return array<int, string>
     */
    public function classes(): array
    {
        return [
            'fad',
            $this->icon,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function styles(): array
    {
        return [
            '--fa-primary-opacity' => (string)$this->primary_opacity,
            '--fa-secondary-opacity' => (string)$this->secondary_opacity,
            '--fa-primary-color' => $this->primary_color,
            '--fa-secondary-color' => $this->secondary_color,
        ];
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
            ->with('classes', array_merge($this->classes(), [$classes ?? 'me-md-2']))
            ->render();
    }
}
