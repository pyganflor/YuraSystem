<?php

namespace yura\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class ControlSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        return $next($request);
    }
}