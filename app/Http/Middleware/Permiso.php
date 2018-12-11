<?php

namespace yura\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use yura\Modelos\Rol;
use yura\Modelos\Rol_Submenu;
use yura\Modelos\Usuario;

class Permiso
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
        $rol = Rol::find(getUsuario(Session::get('id_usuario'))->id_rol);
        if ($rol->estado == 'A') {
            foreach ($rol->submenus as $item) {
                $url = explode('/', substr($request->getRequestUri(), 1))[0];
                if ($item->submenu->url == $url && Rol_Submenu::All()->where('id_rol', '=', $rol->id_rol)
                        ->where('id_submenu', '=', $item->submenu->id_submenu)->first()->estado == 'A')
                    return $next($request);
            }
        }
        return new Response(view('errores.acceso_denegado'));
    }
}