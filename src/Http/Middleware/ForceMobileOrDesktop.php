<?php

namespace Snaccs\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;

/**
 * Class ForceMobileOrDesktop
 *
 * @package Snaccs\Http\Middleware
 */
class ForceMobileOrDesktop
{
    /**
     * Handle an incoming request.
     *
     * @param Request     $request
     * @param Closure     $next
     * @param string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->has('mobile')) {
            Cookie::queue(Cookie::forget('force_desktop'));

            if ((new Agent())->isMobile()) {
                return redirect()->back();
            }

            return redirect()->back()->withCookie('force_mobile', 1);
        } elseif ($request->has('desktop')) {
            Cookie::queue(Cookie::forget('force_mobile'));

            if (! (new Agent())->isMobile()) {
                return redirect()->back();
            }

            return redirect()->back()->withCookie('force_desktop', 1);
        }

        return $next($request);
    }
}
