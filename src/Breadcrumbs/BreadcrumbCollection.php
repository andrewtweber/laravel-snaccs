<?php

namespace Snaccs\Breadcrumbs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Snaccs\Support\JsonString;

/**
 * Class BreadcrumbCollection
 *
 * @package Snaccs\Breadcrumbs
 *
 * @method iterable<Breadcrumb> getIterator()
 * @property Breadcrumb[] $items
 */
class BreadcrumbCollection extends Collection implements Arrayable
{
    /**
     * @return HtmlString
     */
    public function toListHtml(): HtmlString
    {
        $html = view('snaccs::breadcrumbs.list')
            ->with('breadcrumbs', $this)
            ->render();

        return new HtmlString($html);
    }

    /**
     * @return HtmlString
     */
    public function toHtml(): HtmlString
    {
        return (new JsonString($this->toArray()))->toScriptTag();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            '@context'        => 'http://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        foreach ($this->items as $position => $crumb) {
            $data['itemListElement'][] = $crumb->setPosition($position + 1)->toArray();
        }

        return $data;
    }
}
