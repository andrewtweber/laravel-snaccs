<?php

namespace Snaccs\Validation\Rules;

/**
 * Class Twitter
 *
 * @package Snaccs\Validation\Rules
 */
class Twitter extends Handle
{
    protected string $label = 'Twitter';

    protected string $allowed_special_chars = '_';

    protected int $min = 1;

    protected int $max = 15;

    protected array $allowed_domains = ['twitter.com', 'x.com'];
}
