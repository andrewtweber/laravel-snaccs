<?php

namespace Snaccs\Menu;

use Illuminate\Support\Collection;

/**
 * Class Menu
 *
 * @package Snaccs\Menu
 */
class Menu
{
    /**
     * @var Collection|MenuItem[]
     */
    public Collection $items;

    public bool $condensed = false;

    /**
     * Menu constructor.
     *
     * @param string $name
     */
    public function __construct(
        public string $name
    ) {
        $this->items = collect();
    }

    /**
     * @param MenuItem      $item
     * @param callable|null $check
     *
     * @return self
     */
    public function add(MenuItem $item, ?callable $check = null): self
    {
        if (isset($check) && ! $check($item)) {
            return $this;
        }

        $this->items->push($item);

        return $this;
    }

    /**
     * If you only have permission to view one dropdown, make that dropdown the main menu.
     */
    public function condense()
    {
        if (count($this->items) === 1) {
            $this->items = $this->items[0]->children;

            foreach ($this->items as $item) {
                $item->condensed = true;
            }

            $this->condensed = true;
        }
    }
}
