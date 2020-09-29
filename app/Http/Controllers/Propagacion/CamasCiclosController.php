<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Cama;
use yura\Modelos\CicloCama;
use yura\Modelos\Submenu;
use Validator;
use yura\Modelos\Variedad;

class CamasCiclosController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.propagacion.camas_ciclos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'variedades' => Variedad::All()
        ]);
    }

    /* ======================= CAMAS ========================= */
    public function listar_camas(Request $request)
    {
        $camas = Cama::All()->sortBy('area_trabajo')->sortBy('nombre');
        return view('adminlte.gestion.propagacion.camas_ciclos.partials.listado_camas', [
            'camas' => $camas
        ]);
    }

    public function add_cama(Request $request)
    {
        return view('adminlte.gestion.propagacion.camas_ciclos.forms.add_cama', [
        ]);
    }

    public function edit_cama(Request $request)
    {
        $cama = Cama::find($request->id);
        return view('adminlte.gestion.propagacion.camas_ciclos.forms.add_cama', [
            'cama' => $cama
        ]);
    }

    public function store_cama(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25|unique:cama',
            'area_trabajo' => 'required|max:250',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'area_trabajo.required' => 'El área de trabajo es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'area_trabajo.max' => 'El área de trabajo es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = new Cama();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
            $model->area_trabajo = str_limit(mb_strtoupper(espacios($request->area_trabajo)), 250);
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Cama::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado una nueva cama satisfactoriamente</p>'
                    . '</div>';
                bitacora('cama', $model->id_cama, 'I', 'Inserción satisfactoria de una nueva cama');
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

    public function update_cama(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'area_trabajo' => 'required|max:250',
            'id_cama' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_cama.required' => 'La cama es obligatoria',
            'area_trabajo.required' => 'El área de trabajo es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'area_trabajo.max' => 'El área de trabajo es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Cama::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 25))
                    ->where('id_cama', '!=', $request->id_cama)) == 0) {
                $model = Cama::find($request->id_cama);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
                $model->area_trabajo = str_limit(mb_strtoupper(espacios($request->area_trabajo)), 250);

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado la cama satisfactoriamente</p>'
                        . '</div>';
                    bitacora('cama', $model->id_cama, 'U', 'Actualización satisfactoria de una cama');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p>La cama "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
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

    public function eliminar_cama(Request $request)
    {
        $cama = Cama::find($request->id_cama);
        if ($cama != '') {
            $cama->estado = $cama->estado == 1 ? 0 : 1;
            if ($cama->save()) {
                $accion = $cama->estado == 1 ? 'activado' : 'desactivado';
                return [
                    'success' => true,
                    'mensaje' => '<div class="alert alert-success text-center">Se ha ' . $accion . ' la cama satisfactoriamente</div>',
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
                'mensaje' => '<div class="alert alert-danger text-center">No se ha encontrado la cama en el sistema</div>',
            ];
        }
    }

    /* ======================= CICLOS ========================= */
    public function listar_ciclos(Request $request)
    {
        if ($request->activo == 0) {
            $view = 'listado_ciclos_inactivos';
            $all_camas = Cama::All()->where('area_trabajo', 'PLANTAS MADRES')->sortBy('area_trabajo')->sortBy('nombre');
            $camas = [];
            foreach ($all_camas as $c)
                if ($c->ciclo_actual() == '')
                    array_push($camas, $c);
            $json = [
                'camas' => $camas,
                'variedades' => getVariedades()
            ];
        } else {
            $view = 'listado_ciclos_activos';
            $ciclos = CicloCama::where('id_variedad', $request->variedad)
                ->where('activo', 1)
                ->get();
            $json = [
                'ciclos' => $ciclos,
                'variedades' => getVariedades()
            ];
        }
        return view('adminlte.gestion.propagacion.camas_ciclos.partials.' . $view, $json);
    }
}
