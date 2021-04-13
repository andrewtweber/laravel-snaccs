<?php

namespace Snaccs\Services;

use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;

/**
 * Class System
 *
 * @package Snaccs\Services
 */
class System
{
    public bool $is_mobile;

    /**
     * System constructor.
     */
    public function __construct()
    {
        if (Cookie::get('force_mobile')) {
            $this->is_mobile = true;
        } elseif (Cookie::get('force_desktop')) {
            $this->is_mobile = false;
        } else {
            $this->is_mobile = (new Agent())->isMobile();
        }
    }
}
