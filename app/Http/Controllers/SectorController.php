<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Lote;
use yura\Modelos\Modulo;
use yura\Modelos\Sector;
use yura\Modelos\Submenu;
use Validator;

class SectorController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.sectores_modulos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'sectores' => Sector::All()
        ]);
    }

    public function select_sector(Request $request)
    {
        $s = Sector::find($request->id_sector);
        return view('adminlte.gestion.sectores_modulos.partials.listado_modulo', [
            'modulos' => $s->modulos
        ]);
    }

    public function listar_modulos_x_sector(Request $request)
    {
        $modulos = [];
        if ($request->id_sector != '') {
            $s = Sector::find($request->id_sector);
            if ($s != '')
                $modulos = $s->modulos;
        }
        return view('adminlte.gestion.sectores_modulos.forms.partials.select_modulos', [
            'modulos' => $modulos
        ]);
    }

    public function select_modulo(Request $request)
    {
        $m = Modulo::find($request->id_modulo);
        return view('adminlte.gestion.sectores_modulos.partials.listado_lote', [
            'lotes' => $m->lotes
        ]);
    }

    /* =====================================================*/

    public function add_sector(Request $request)
    {
        return view('adminlte.gestion.sectores_modulos.forms.add_sector');
    }

    public function store_sector(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250|unique:sector',
            'interno' => 'required',
            'descripcion' => 'max:1000',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'interno.required' => 'El campo interno es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'descripcion.max' => 'La descripción es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = new Sector();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
            $model->descripcion = str_limit((espacios($request->descripcion)), 1000);
            $model->interno = $request->interno;
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Sector::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo sector satisfactoriamente</p>'
                    . '</div>';
                bitacora('sector', $model->id_sector, 'I', 'Inserción satisfactoria de un nuevo sector');
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

    public function add_modulo(Request $request)
    {
        return view('adminlte.gestion.sectores_modulos.forms.add_modulo', [
            'sectores' => Sector::All()->where('estado', '=', 1),
        ]);
    }

    public function store_modulo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_sector' => 'required|',
            'area' => 'required|',
            'descripcion' => 'max:1000',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'area.required' => 'El área es obligatorio',
            'id_sector.required' => 'El sector es obligatorio',
            'descripcion.max' => 'La descripción es muy grande',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Modulo::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 25))
                    ->where('id_sector', '=', $request->id_sector)) == 0) {
                $model = new Modulo();
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
                $model->id_sector = $request->id_sector;
                $model->area = $request->area;
                $model->descripcion = str_limit((espacios($request->descripcion)), 1000);
                $model->fecha_registro = date('Y-m-d H:i:s');

                if ($model->save()) {
                    $model = Modulo::All()->last();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado un nuevo módulo satisfactoriamente</p>'
                        . '</div>';
                    bitacora('modulo', $model->id_modulo, 'I', 'Inserción satisfactoria de un nuevo módulo');

                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El módulo "' . espacios($request->nombre) . '" ya se encuentra en este sector</p>'
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

    public function add_lote(Request $request)
    {
        return view('adminlte.gestion.sectores_modulos.forms.add_lote', [
            'sectores' => Sector::All()->where('estado', '=', 1),
        ]);
    }

    public function store_lote(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'descripcion' => 'max:1000',
            'area' => 'max:11|',
            'id_modulo' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'area.max' => 'El área es muy grande',
            'descripcion.max' => 'La descripción es muy grande',
            'id_modulo.required' => 'El módulo es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Lote::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 25))
                    ->where('id_modulo', '=', $request->id_modulo)) == 0) {
                $model = new Lote();
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
                $model->descripcion = str_limit((espacios($request->descripcion)), 1000);
                $model->area = $request->area;
                $model->id_modulo = $request->id_modulo;
                $model->fecha_registro = date('Y-m-d H:i:s');

                if ($model->save()) {
                    $model = Lote::All()->last();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado un nuevo lote satisfactoriamente</p>'
                        . '</div>';
                    bitacora('lote', $model->id_lote, 'I', 'Inserción satisfactoria de un nuevo lote');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El lote "' . espacios($request->nombre) . '" ya se encuentra en este módulo</p>'
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

    /* =====================================================*/

    public function edit_sector(Request $request)
    {
        if ($request->has('id_sector')) {
            $s = Sector::find($request->id_sector);
            if ($s != '') {
                return view('adminlte.gestion.sectores_modulos.forms.edit_sector', [
                    'sector' => $s
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el sector en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado un sector</div>';
        }
    }

    public function update_sector(Request $request)
    {
        dd($request->all());
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_sector' => 'required|',
            'interno' => 'required|',
            'descripcion' => 'max:1000|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_sector.required' => 'El sector es obligatorio',
            'interno.required' => 'El campo interno es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'descripcion.max' => 'La descripción es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Sector::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('id_sector', '!=', $request->id_sector)) == 0) {
                $model = Sector::find($request->id_sector);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
                $model->interno = $request->interno;
                $model->descripcion = str_limit((espacios($request->descripcion)), 1000);

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el sector satisfactoriamente</p>'
                        . '</div>';
                    bitacora('sector', $model->id_sector, 'U', 'Actualización satisfactoria de un sector');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El sector "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
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

    public function cambiar_estado_sector(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_sector')) {
            $s = Sector::find($request->id_sector);
            if ($s != '') {
                $s->estado = $request->estado;
                if ($s->save()) {
                    $texto = $request->estado == 1 ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('sector', $s->id_sector, 'U', 'Cambio de estado de un sector');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el sector en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un sector</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

    public function edit_modulo(Request $request)
    {
        if ($request->has('id_modulo')) {
            $m = Modulo::find($request->id_modulo);
            if ($m != '') {
                return view('adminlte.gestion.sectores_modulos.forms.edit_modulo', [
                    'modulo' => $m,
                    'sectores' => Sector::All()->where('estado', '=', 1),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el módulo en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado un módulo</div>';
        }
    }

    public function update_modulo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_modulo' => 'required|',
            'area' => 'required|',
            'id_sector' => 'required|',
            'descripcion' => 'max:1000|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'area.required' => 'El área es obligatorio',
            'descripcion.max' => 'La descripción es muy grande',
            'id_modulo.required' => 'El módulo es obligatorio',
            'id_sector.required' => 'El sector es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Modulo::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 25))
                    ->where('id_sector', '=', $request->id_sector)
                    ->where('id_modulo', '!=', $request->id_modulo)) == 0) {
                $model = Modulo::find($request->id_modulo);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
                $model->descripcion = str_limit((espacios($request->descripcion)), 1000);
                $model->area = $request->area;
                $model->id_sector = $request->id_sector;

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el módulo satisfactoriamente</p>'
                        . '</div>';
                    bitacora('modulo', $model->id_modulo, 'U', 'Actualización satisfactoria de un módulo');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El módulo "' . espacios($request->nombre) . '" ya se encuentra en este sector</p>'
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

    public function cambiar_estado_modulo(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_modulo')) {
            $m = Modulo::find($request->id_modulo);
            if ($m != '') {
                $m->estado = $request->estado;
                if ($m->save()) {
                    $texto = $request->estado == 1 ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('modulo', $m->id_modulo, 'U', 'Cambio de estado de un módulo');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el módulo en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un sector</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

    public function edit_lote(Request $request)
    {
        if ($request->has('id_lote')) {
            $l = Lote::find($request->id_lote);
            if ($l != '') {
                return view('adminlte.gestion.sectores_modulos.forms.edit_lote', [
                    'lote' => $l,
                    'sectores' => Sector::All()->where('estado', '=', 1)
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el lote en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado un lote</div>';
        }
    }

    public function update_lote(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
            'area' => '|max:11',
            'descripcion' => '|max:1000',
            'id_modulo' => 'required|',
            'id_lote' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_modulo.required' => 'El módulo es obligatorio',
            'id_lote.required' => 'El lote es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'area.max' => 'El área es muy grande',
            'descripcion.max' => 'La descripción es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Lote::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 25))
                    ->where('id_modulo', '=', $request->id_modulo)
                    ->where('id_lote', '!=', $request->id_lote)) == 0) {
                $model = Lote::find($request->id_lote);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
                $model->descripcion = str_limit((espacios($request->descripcion)), 1000);
                $model->area = $request->area;
                $model->id_modulo = $request->id_modulo;
                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el lote satisfactoriamente</p>'
                        . '</div>';
                    bitacora('lote', $model->id_lote, 'U', 'Actualización satisfactoria de un lote');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El lote "' . espacios($request->nombre) . '" ya se encuentra en este módulo</p>'
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

    public function cambiar_estado_lote(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_lote')) {
            $l = Lote::find($request->id_lote);
            if ($l != '') {
                $l->estado = $request->estado;
                if ($l->save()) {
                    $texto = $request->estado == 1 ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('lote', $l->id_lote, 'U', 'Cambio de estado de un lote');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el lote en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un lote</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }
}