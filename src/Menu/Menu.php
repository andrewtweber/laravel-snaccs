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

        // If any dropdown has only 1 item, and that item is the same link as the parent,
        //  replace the dropdown with the single link.
        foreach ($this->items as $index => $item) {
            if (count($item->children) === 1 && in_array($item->url, [null, $item->children[0]->url])) {
                $this->items[$index] = $item->children[0];
                $this->items[$index]->condensed = true;
            }
        }
    }
}
