<?php

namespace Snaccs\Validation\Rules;

/**
 * Class TikTok
 *
 * @package Snaccs\Validation\Rules
 */
class TikTok extends Handle
{
    protected string $label = 'TikTok';
    protected string $allowed_special_chars = '_.';
    protected int $min = 2;
    protected int $max = 24;
}
