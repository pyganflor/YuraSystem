<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\ContenedorPropag;
use yura\Modelos\Submenu;
use Validator;

class propagConfiguracionesController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.propagacion.configuraciones.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function listar_contenedores(Request $request)
    {
        $contenedores = ContenedorPropag::All()->sortBy('nombre');
        return view('adminlte.gestion.propagacion.configuraciones.partials.listado_contenedores', [
            'contenedores' => $contenedores
        ]);
    }

    public function add_contenedor(Request $request)
    {
        return view('adminlte.gestion.propagacion.configuraciones.forms.add_contenedor', [
        ]);
    }

    public function edit_contenedor(Request $request)
    {
        $contenedor = ContenedorPropag::find($request->id);
        return view('adminlte.gestion.propagacion.configuraciones.forms.add_contenedor', [
            'contenedor' => $contenedor
        ]);
    }

    public function store_contenedor(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25|unique:contenedor_propag',
            'cantidad' => 'required',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'cantidad.required' => 'La cantidad es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = new ContenedorPropag();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
            $model->cantidad = $request->cantidad;
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = ContenedorPropag::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo contenedor satisfactoriamente</p>'
                    . '</div>';
                bitacora('contenedor', $model->id_contenedor_propag, 'I', 'Inserción satisfactoria de un nuevo contenedor');
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

    public function update_contenedor(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'cantidad' => 'required',
            'id_contenedor' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_contenedor.required' => 'El contenedor es obligatorio',
            'cantidad.required' => 'La cantidad es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(ContenedorPropag::All()
                    ->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('id_contenedor_propag', '!=', $request->id_contenedor)) != 0) { // se repite el nombre
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p>El contenedor "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
                    . '</div>';
            } else if (count(ContenedorPropag::All()
                    ->where('cantidad', '=', $request->cantidad)
                    ->where('id_contenedor_propag', '!=', $request->id_contenedor)) != 0) { // se repite la cantidad
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p>El contenedor con "' . $request->cantidad . '" de cantidad ya se encuentra en el sistema</p>'
                    . '</div>';
            } else {    // es un nuevo contenedor
                $model = ContenedorPropag::find($request->id_contenedor);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
                $model->cantidad = $request->cantidad;

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el contenedor satisfactoriamente</p>'
                        . '</div>';
                    bitacora('contenedor_propag', $model->id_contenedor_propag, 'U', 'Actualización satisfactoria de un contenedor');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
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

    public function eliminar_contenedor(Request $request)
    {
        $contenedor = ContenedorPropag::find($request->id_contenedor);
        if ($contenedor != '') {
            $contenedor->estado = $contenedor->estado == 1 ? 0 : 1;
            if ($contenedor->save()) {
                $accion = $contenedor->estado == 1 ? 'activado' : 'desactivado';
                return [
                    'success' => true,
                    'mensaje' => '<div class="alert alert-success text-center">Se ha ' . $accion . ' el contenedor satisfactoriamente</div>',
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => '<div class="alert alert-danger text-center">ha ocurrido un problema al guardar la información</div>',
                ];
            }
        } else {
            return [
                'success' => false,
                'mensaje' => '<div class="alert alert-danger text-center">No se ha encontrado el contenedor en el sistema</div>',
            ];
        }
    }
}
