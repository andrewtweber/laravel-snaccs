<?php

namespace Snaccs\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;

/**
 * Class JsonString
 *
 * @package Snaccs\Support
 */
class JsonString
{
    /**
     * JsonString constructor.
     *
     * @param array $data
     */
    public function __construct(
        public array $data
    ) {
    }

    /**
     * @return HtmlString
     */
    public function toScriptTag(): HtmlString
    {
        $html = <<<EOF
            <script type="application/ld+json">
            {$this}
            </script>
        EOF;

        return new HtmlString($html);
    }

    /**
     * @return HtmlString
     */
    public function toHtml(): HtmlString
    {
        return new HtmlString((string)$this);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->data, App::environment('production') ? null : JSON_PRETTY_PRINT);
    }
}
