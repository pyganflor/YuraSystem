<?php

namespace yura\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

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
        return redirect('login');
    }
}