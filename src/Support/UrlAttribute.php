<?php

namespace Snaccs\Support;

/**
 * Enum UrlAttribute
 *
 * @package Snaccs\Support
 */
enum UrlAttribute: string
{
    case BaseDomain = 'base_domain';
    case Domain = 'domain';
    case Subdomain = 'subdomain';
}
