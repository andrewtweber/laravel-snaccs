<?php

namespace Snaccs\Menu;

use Illuminate\Support\HtmlString;

/**
 * Class MenuBadge
 *
 * @package Snaccs\Menu
 */
class MenuBadge
{
    /**
     * MenuBadge constructor.
     *
     * @param int    $number
     * @param string $color
     */
    public function __construct(
        public int $number,
        public string $color
    ) {
    }

    /**
     * @return HtmlString|null
     */
    public function html(): ?HtmlString
    {
        if ($this->number <= 0) {
            return null;
        }

        $html = view('snaccs::menu.badge')
            ->with('badge', $this)
            ->render();

        return new HtmlString(trim($html));
    }
}
