<?php

namespace Snaccs\Validation\Rules;

/**
 * Class Instagram
 *
 * @package Snaccs\Validation\Rules
 */
class Instagram extends Handle
{
    protected string $label = 'Instagram';
    protected string $allowed_special_chars = '_.';
    protected int $min = 1;
    protected int $max = 30;
    protected array $allowed_domains = ['instagram.com'];
}
