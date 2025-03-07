<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class EnableDebug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->getClientIp(), ['31.211.68.135']) or env('APP_DEBUG') === true) {
            \Debugbar::enable();
            Config::set('app.debug', true);
        } else {
            Config::set('app.debug', false);
        }
//        return abort(404);
        return $next($request);
    }
}
