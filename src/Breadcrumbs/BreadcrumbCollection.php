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
     * @return HtmlString|null
     */
    public function toListHtml(): ?HtmlString
    {
        if ($this->isEmpty()) {
            return null;
        }

        $html = view('snaccs::breadcrumbs.list')
            ->with('breadcrumbs', $this)
            ->render();

        return new HtmlString($html);
    }

    /**
     * @return HtmlString|null
     */
    public function toHtml(): ?HtmlString
    {
        if ($this->isEmpty()) {
            return null;
        }

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
