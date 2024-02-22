<?php

namespace Snaccs\Breadcrumbs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\HtmlString;

/**
 * Class Breadcrumb
 *
 * @package Snaccs\Breadcrumbs
 */
class Breadcrumb implements Arrayable
{
    protected int $position;

    /**
     * Breadcrumb constructor.
     *
     * @param string $url
     * @param string $label
     * @param bool   $active
     */
    public function __construct(
        public string $url,
        public string $label,
        public bool $active = false
    ) {
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return HtmlString
     */
    public function toHtml(): HtmlString
    {
        $html = view('snaccs::breadcrumbs.item')
            ->with('breadcrumb', $this)
            ->toHtml();

        return new HtmlString($html);
    }

    /**
     * This should generally not be called directly, instead a `BreadcrumbCollection` of `Breadcrumb`
     * should be cast to array because that will set the correct position.
     *
     * @deprecated
     * @return array
     */
    public function toArray()
    {
        return [
            '@type'    => 'ListItem',
            'position' => $this->position ?? 1,
            'item'     => [
                '@id'  => url($this->url),
                'name' => $this->label,
            ],
        ];
    }
}
