<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Icon;
use yura\Modelos\Notificacion;
use yura\Modelos\NotificacionUsuario;
use yura\Modelos\Submenu;
use Validator;
use yura\Modelos\Usuario;

class NotificacionesController extends Controller
{
    public function inicio(Request $request)
    {
        $list = Notificacion::All();
        return view('adminlte.gestion.notificaciones.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'listado' => $list,
            'iconos' => Icon::All()->where('estado', 'A'),
        ]);
    }

    public function store_notificacion(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50|unique:notificacion',
            'tipo' => 'required|',
            'icon' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
            'icon.required' => 'El ícono es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'nombre.unique' => 'El nombre ya existe',
        ]);
        if (!$valida->fails()) {
            $model = new Notificacion();
            $model->nombre = str_limit(mb_strtolower(str_replace(' ', '_', espacios($request->nombre))), 50);
            $model->tipo = $request->tipo;
            $model->id_icono = $request->icon;

            if ($model->save()) {
                $model = Notificacion::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado una nueva notificación satisfactoriamente</p>'
                    . '</div>';
                bitacora('notificacion', $model->id_notificacion, 'I', 'Inserción satisfactoria de una nueva notificación');
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_notificacion(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50|unique:notificacion',
            'tipo' => 'required|',
            'icon' => 'required|',
            'id' => 'required|',
        ], [
            'id.required' => 'La notificación es obligatoria',
            'nombre.required' => 'El nombre es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
            'icon.required' => 'El ícono es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'nombre.unique' => 'El nombre ya existe',
        ]);
        if (!$valida->fails()) {
            $model = Notificacion::find($request->id);
            $model->nombre = str_limit(mb_strtolower(str_replace(' ', '_', espacios($request->nombre))), 50);
            $model->tipo = $request->tipo;
            $model->id_icono = $request->icon;

            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualziado la notificación satisfactoriamente</p>'
                    . '</div>';
                bitacora('notificacion', $model->id_notificacion, 'U', 'Actualización satisfactoria de una nueva notificación');
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function cambiar_estado(Request $request)
    {
        $notificacion = Notificacion::find($request->id);
        $notificacion->estado == 1 ? $notificacion->estado = 0 : $notificacion->estado = 1;
        $notificacion->save();
        bitacora('notificacion', $notificacion->id_notificacion, 'U', 'Cambio de destado de una notificacion a ' . $notificacion->estado);
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha cambiado el estado satisfactoriamente</div>',
        ];
    }

    public function admin_usuarios(Request $request)
    {
        $notificacion = Notificacion::find($request->id);
        return view('adminlte.gestion.notificaciones.partials.usuarios', [
            'notificacion' => $notificacion,
            'usuarios' => Usuario::All()->where('estado', 'A')->sortBy('nombre_completo'),
        ]);
    }

    public function save_notificacion_usuario(Request $request)
    {
        $model = NotificacionUsuario::All()->where('id_usuario', $request->user)->where('id_notificacion', $request->not)->first();
        if ($model == '') {
            $model = new NotificacionUsuario();
            $model->id_notificacion = $request->not;
            $model->id_usuario = $request->user;
            $model->save();
            $model = NotificacionUsuario::All()->last();
            bitacora('notificacion_usuario', $model->id_notificacion_usuario, 'I', 'Inserción de una nueva notificacion-usuario');
        } else {
            $model->estado == 1 ? $model->estado = 0 : $model->estado = 1;
            $model->save();
            bitacora('notificacion_usuario', $model->id_notificacion_usuario, 'U', 'Cambio de estado de una notificacion-usuario');
        }
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha actualizado satisfactoriamente</div>',
        ];
    }
}