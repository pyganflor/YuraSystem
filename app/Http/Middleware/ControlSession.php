<?php

namespace yura\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use phpseclib\Crypt\RSA;

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
        if (strtotime(date('Y-m-d H:i:s')) - strtotime(Session::get('last_quest')) < 7200)
            Session::put('last_quest', date('Y-m-d H:i:s'));
        else {
            return response(redirect('logout'));
            //return Redirect::to('logout')->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        return $next($request);
    }
}