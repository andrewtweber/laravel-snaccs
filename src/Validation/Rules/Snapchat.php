<?php

namespace Snaccs\Validation\Rules;

/**
 * Class Snapchat
 *
 * @package Snaccs\Validation\Rules
 */
class Snapchat extends Handle
{
    protected string $label = 'Snapchat';
    protected string $allowed_special_chars = '-_.';
    protected int $min = 3;
    protected int $max = 15;
}
