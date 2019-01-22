<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Nature\Plant;
use yura\Modelos\Planta;
use yura\Modelos\Submenu;
use yura\Modelos\Precio;
use DB;
use yura\Modelos\ClasificacionRamo;
use Validator;
use yura\Modelos\Variedad;

class PlantaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.plantas_variedades.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'plantas' => Planta::All()
        ]);
    }

    public function select_planta(Request $request)
    {
        $p = Planta::find($request->id_planta);
        return view('adminlte.gestion.plantas_variedades.partials.listado_variedad', [
            'variedades' => $p->variedades
        ]);
    }

    /* =====================================================*/

    public function add_planta(Request $request)
    {
        return view('adminlte.gestion.plantas_variedades.forms.add_planta');
    }

    public function store_planta(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250|unique:planta',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = new Planta();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Planta::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado una nueva planta satisfactoriamente</p>'
                    . '</div>';
                bitacora('planta', $model->id_planta, 'I', 'Inserción satisfactoria de una nueva planta');
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

    public function add_variedad(Request $request)
    {
        return view('adminlte.gestion.plantas_variedades.forms.add_variedad', [
            'plantas' => Planta::All()->where('estado', '=', 1),
        ]);
    }

    public function store_variedad(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250',
            'siglas' => 'required|max:25',
            'id_planta' => 'required|',
            //'unidad_medida' => 'required|',
            //'tallos_por_ramo' => 'required|',
            'minimo_apertura' => 'required|',
            'maximo_apertura' => 'required|',
            'estandar' => 'required|',
            'tallos_x_malla' => 'required|'
        ], [
            'tallos_x_malla.required' => 'Los tallos por malla son obligatorios',
            'nombre.required' => 'El nombre es obligatorio',
            'siglas.required' => 'Las siglas son obligatorias',
            'id_planta.required' => 'La planta es obligatoria',
            //'unidad_medida' => 'La unidad de medida es requerida',
            //'tallos_por_ramo.required' => 'El tallo por ramo es requerido',
            'nombre.max' => 'El nombre es muy grande',
            'siglas.max' => 'Las siglas son muy grande',
            'maximo_apertura.required' => 'EL maximo de apertura es obligatorio',
            'minimo_apertura.required' => 'EL minimo de apertura es obligatorio',
            'estandar.required' => 'El campo estandar es obligatorio'
        ]);
        if (!$valida->fails()) {
            if (count(Variedad::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('id_planta', '=', $request->id_planta)) == 0) {
                $model = new Variedad();
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
                $model->siglas = str_limit(mb_strtoupper(espacios($request->siglas)), 25);
                $model->id_planta = $request->id_planta;
                //  $model->unidad_de_medida = $request->unidad_medida;
                //$model->cantidad = $request->tallos_por_ramo;
                $model->minimo_apertura = $request->minimo_apertura;
                $model->maximo_apertura = $request->maximo_apertura;
                $model->tallos_x_malla = $request->tallos_x_malla;
                $model->fecha_registro = date('Y-m-d H:i:s');
                $model->estandar_apertura = $request->estandar;

                if ($model->save()) {
                    $model = Variedad::All()->last();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado una nueva variedad satisfactoriamente</p>'
                        . '</div>';
                    bitacora('variedad', $model->id_variedad, 'I', 'Inserción satisfactoria de una nueva variedad');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> La variedad "' . espacios($request->nombre) . '" ya se encuentra en esta planta</p>'
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

    public function edit_planta(Request $request)
    {
        if ($request->has('id_planta')) {
            $p = Planta::find($request->id_planta);
            if ($p != '') {
                return view('adminlte.gestion.plantas_variedades.forms.edit_planta', [
                    'planta' => $p
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la panta en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado una planta</div>';
        }
    }

    public function update_planta(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250',
            'id_planta' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_planta.required' => 'La platna es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Planta::All()->where('nombre', '=', $request->nombre)
                    ->where('id_planta', '!=', $request->id_planta)) == 0) {
                $model = Planta::find($request->id_planta);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado la planta satisfactoriamente</p>'
                        . '</div>';
                    bitacora('planta', $model->id_planta, 'U', 'Actualización satisfactoria de una planta');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> La planta "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
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

    public function cambiar_estado_planta(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_planta')) {
            $p = Planta::find($request->id_planta);
            if ($p != '') {
                $p->estado = $request->estado;
                if ($p->save()) {
                    $texto = $request->estado == 1 ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('planta', $p->id_planta, 'U', 'Cambio de estado de una planta');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado la planta en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado una planta</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

    public function edit_variedad(Request $request)
    {
        if ($request->has('id_variedad')) {
            $v = Variedad::find($request->id_variedad);
            if ($v != '') {
                return view('adminlte.gestion.plantas_variedades.forms.edit_variedad', [
                    'variedad' => $v,
                    'plantas' => Planta::All()->where('estado', '=', 1),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la variedad en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado una variedad</div>';
        }
    }

    public function update_variedad(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250',
            'siglas' => 'required|max:25',
            'id_planta' => 'required|',
            //'unidad_medida'  => 'required|',
            //'tallos_por_ramo' => 'required|',
            'minimo_apertura' => 'required|',
            'maximo_apertura' => 'required|',
            'tallos_x_malla' => 'required|',
            'estandar' => 'required|'
        ], [
            'tallos_x_malla.required' => 'Los tallos por malla son obligatorios',
            'nombre.required' => 'El nombre es obligatorio',
            'siglas.required' => 'Las siglas son obligatorias',
            'id_planta.required' => 'La planta es obligatoria',
            //'unidad_medida' => 'La unidad de medida es requerida',
            //'tallos_por_ramo.required' => 'El tallo por ramo es requerido',
            'nombre.max' => 'El nombre es muy grande',
            'siglas.max' => 'Las siglas son muy grande',
            'maximo_apertura.required' => 'EL maximo de apertura es obligatorio',
            'minimo_apertura.required' => 'EL minimo de apertura es obligatorio',
            'estandar.required' => 'El campo estandar es obligatorio'
        ]);
        if (!$valida->fails()) {
            if (count(Variedad::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('id_planta', '=', $request->id_planta)
                    ->where('id_variedad', '!=', $request->id_variedad)) == 0) {
                $model = Variedad::find($request->id_variedad);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
                $model->siglas = str_limit(mb_strtoupper(espacios($request->siglas)), 25);
                //$model->unidad_de_medida = $request->unidad_medida;
                //$model->cantidad = $request->tallos_por_ramo;
                $model->id_planta = $request->id_planta;
                $model->minimo_apertura = $request->minimo_apertura;
                $model->maximo_apertura = $request->maximo_apertura;
                $model->estandar_apertura = $request->estandar;
                $model->tallos_x_malla = $request->tallos_x_malla;

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado la variedad satisfactoriamente</p>'
                        . '</div>';
                    bitacora('variedad', $model->id_variedad, 'U', 'Actualización satisfactoria de una variedad');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> La variedad "' . espacios($request->nombre) . '" ya se encuentra en esta planta</p>'
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

    public function cambiar_estado_variedad(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_variedad')) {
            $v = Variedad::find($request->id_variedad);
            if ($v != '') {
                $v->estado = $request->estado;
                if ($v->save()) {
                    $texto = $request->estado == 1 ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('variedad', $v->id_variedad, 'U', 'Cambio de estado de una variedad');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado la variedad en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado ninguna variedad</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }

    public function form_precio_variedad(Request $request)
    {

        $dataMondea = DB::table('configuracion_empresa')->select('moneda')->first();
        $dataPrecio = Precio::where('precio.id_variedad', $request->id_variedad)
            ->join('variedad as v', 'precio.id_variedad', '=', 'v.id_variedad')->get();
        $adataClasificacionRamos = ClasificacionRamo::where('estado', 1)->get();
        return view('adminlte.gestion.plantas_variedades.forms.add_precio',
            [
                'moneda' => $dataMondea,
                'dataPrecio' => $dataPrecio,
                'adataClasificacionRamos' => $adataClasificacionRamos
            ]);

    }

    public function store_precio(Request $request)
    {
        //dd($request->all());
        $valida = Validator::make($request->all(), [
            'arrData' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            $msg = '';
            foreach ($request->arrData as $key => $precio) {
                $verifica = false;
                $existPrecio = Precio::where([
                    ['id_variedad', $request->id_variedad],
                    ['id_clasificacion_ramo', $request->arrData[$key][1]]
                ])->get();

                if (count($existPrecio) > 0) {

                    if (!empty($request->arrData[$key][2])) {

                        $objClasificacionRamo = ClasificacionRamo::find($existPrecio[0]->id_clasificacion_ramo);
                        $msg .= '<div class="alert alert-success text-center">Ya existe un precio establecido para la clasificación por ramo de ' . $objClasificacionRamo->nombre . ' , este precio no será guardado nuevamene  </div>';
                        $success = false;

                    } else {

                        $verifica = true;
                        $objPrecio = Precio::find($existPrecio[0]->id_precio);
                        $palabra = 'Actualizado';
                        $accion = 'U';
                    }
                } elseif (count($existPrecio) < 1 && empty($request->arrData[$key][2])) {

                    $verifica = true;
                    $objPrecio = new Precio;
                    $palabra = 'Insertado';
                    $accion = 'I';
                }

                if ($verifica) {

                    $objPrecio->id_clasificacion_ramo = $request->arrData[$key][1];
                    $objPrecio->id_variedad = $request->id_variedad;
                    $objPrecio->cantidad = $request->arrData[$key][0];

                    if ($objPrecio->save()) {

                        $model = Precio::All()->last();
                        $msg .= '<div class="alert alert-success text-center">Se ha ' . $palabra . ' satisfactoriamente el precio ' . $model->cantidad . '</div>';
                        $success = true;
                        bitacora('precio', $model->id_precio, $accion, $palabra . ' de precio');

                    } else {

                        $msg .= '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                        $success = false;
                    }
                }
            }
            return [
                'success' => $success,
                'mensaje' => $msg
            ];
        }
    }

    public function add_inptus_precio_variedad(Request $request)
    {

        $adataClasificacionRamos = ClasificacionRamo::where('estado', 1)->get();
        return view('adminlte.gestion.plantas_variedades.forms.partials.add_inputs_precio',
            [
                'adataClasificacionRamos' => $adataClasificacionRamos,
                'cntTr' => $request->cant_tr

            ]);
    }

    public function update_precio(Request $request)
    {

        if (!empty($request->id_precio)) {

            $model = Precio::find($request->id_precio);

            $request->estado == 1 ? $model->estado = 0 : $model->estado = 1;
            $msg = '';
            if ($model->save()) {
                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> El precio ha sido modificada exitosamente</p>'
                    . '</div>';
                bitacora('precio', $request->id_precio, 'U', 'Actualización satisfactoria del precio');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>';
            }
            return [
                'success' => $success,
                'mensaje' => $msg
            ];

        }
    }
}
