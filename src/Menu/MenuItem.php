<?php

namespace Snaccs\Menu;

use Illuminate\Support\Collection;

/**
 * Class MenuItem
 *
 * @package Snaccs\Menu
 */
class MenuItem
{
    /**
     * @var Collection|MenuItem[]
     */
    public Collection $children;

    public bool $condensed = false;

    public ?MenuBadge $badge = null;

    /**
     * MenuItem constructor.
     *
     * @param string      $label
     * @param string|null $url
     * @param string|null $section
     * @param string|null $icon
     * @param mixed       $permission
     */
    public function __construct(
        public string $label,
        public ?string $url,
        public ?string $section = null,
        public ?string $icon = null,
        public mixed $permission = null
    ) {
        $this->children = collect();
    }

    /**
     * @return self
     */
    public function divider(): self
    {
        $this->children->push(new MenuDivider());

        return $this;
    }

    /**
     * @param MenuItem      $item
     * @param callable|null $check
     *
     * @return self
     */
    public function withChild(MenuItem $item, ?callable $check = null): self
    {
        if (isset($check) && ! $check($item)) {
            return $this;
        }

        $this->children->push($item);

        return $this;
    }

    /**
     * @param MenuBadge $badge
     *
     * @return self
     */
    public function withBadge(MenuBadge $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @param string $color
     *
     * @return self
     */
    public function withTotaledBadge(string $color): self
    {
        $this->badge = new MenuBadge(0, $color);

        foreach ($this->children as $item) {
            if ($item->badge) {
                $this->badge->number += $item->badge->number;
            }
        }

        return $this;
    }

    /**
     * @param string|null $active
     * @param string|null $sub_active
     *
     * @return bool
     */
    public function isActive(?string $active, ?string $sub_active): bool
    {
        if (! $this->section) {
            return false;
        }

        if (! $this->condensed && isset($active) && $active === $this->section) {
            return true;
        }

        if ($this->condensed && isset($sub_active) && $sub_active === $this->section) {
            return true;
        }

        return false;
    }
}
