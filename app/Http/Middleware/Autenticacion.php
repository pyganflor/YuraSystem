<?php

namespace yura\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class Autenticacion
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
        if ($request->session()->has('logeado')) {
            if ($request->session()->get('logeado')) {
                if (getUsuario($request->session()->get('id_usuario'))->estado == 'A')
                    return $next($request);
                else
                    return new Response(view('errores.usuario_inactivo'));
            }
        }
        return response(redirect('login'));
        //return Redirect::to('login')->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}