<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DetectMobile
{
    public function handle(Request $request, Closure $next)
    {
        $userAgent = $request->userAgent() ?? '';

        $isMobile = (bool) preg_match(
            '/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile/i',
            $userAgent
        );

        View::share('isMobile', $isMobile);
        $request->attributes->set('isMobile', $isMobile);

        return $next($request);
    }
}
