<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use PHPUnit\Util\RegularExpressionTest;
use yura\Modelos\Pais;
use yura\Modelos\Rol;
use yura\Modelos\Submenu;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\ClasificacionUnitaria;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Empaque;
use yura\Modelos\Icon;
use yura\Modelos\Variedad;
use yura\Modelos\DetalleEmpaque;
use Validator;
use DB;

class ConfiguracionEmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config_empresa = ConfiguracionEmpresa::all();
        $moneda = Icon::where('nombre', 'usd')
            ->orWhere('nombre', 'jpy')
            ->orWhere('nombre', 'eur')
            ->orWhere('nombre', 'krw')
            ->orWhere('nombre', 'try')
            ->orWhere('nombre', 'inr')
            ->orWhere('nombre', 'gbp')
            ->orWhere('nombre', 'rub')
            ->orderBy('id_icono', 'desc')
            ->get();

        !empty($config_empresa[0]->propagacion) ? $arrPropagacion = explode("|", $config_empresa[0]->propagacion) : $arrPropagacion = false;
        !empty($config_empresa[0]->campo) ? $arrCampo = explode("|", $config_empresa[0]->campo) : $arrCampo = false;
        !empty($config_empresa[0]->postcocecha) ? $arrPostcocecha = explode("|", $config_empresa[0]->postcocecha) : $arrPostcocecha = false;

        return view('adminlte.gestion.configuracion_empresa.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'roles' => Rol::All(),
            'config_empresa' => $config_empresa,
            'arr_propagacion' => $arrPropagacion,
            'arr_campo' => $arrCampo,
            'arr_postcocecha' => $arrPostcocecha,
            'clasifiUnit' => ClasificacionUnitaria::all(),
            'clasifiXRamo' => ClasificacionRamo::all(),
            'empaques' => Empaque::all(),
            'iconoMoneda' => $moneda,
            'text' => ['titulo' => 'Configuración de la empresa', 'subtitulo' => 'módulo de administración'],
            'paises' => Pais::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $existDetalleEmpaque = DetalleEmpaque::where('id_empaque', $request->id_empaque)->get();

        $variedades = Variedad::all();
        $clasificacionesRamos = ClasificacionRamo::all();

        return view('adminlte.gestion.configuracion_empresa.forms.partials.add_detalle_empaque')
            ->with([
                'variedades' => $variedades,
                'clasificacionesRamos' => $clasificacionesRamos,
                'id_empaque' => $request->id_empaque,
                'nombre' => $request->nombre,
                'dataDetalleEmpaque' => $existDetalleEmpaque
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|',
            'cant_hectarea' => 'required|',
            'cantidad_usuarios' => 'required|',
            'clasifi_unit_tipos' => 'required|Array',
            'clasifi_x_ramos_tipos' => 'required|Array',
            'empaque_nombres' => 'required|Array',
            'moneda' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
            'codigo_pais' => 'required'
        ]);

        if (!$valida->fails()) {
            empty($request->id_config) ? $objConfigEmpresa = new ConfiguracionEmpresa : $objConfigEmpresa = ConfiguracionEmpresa::find($request->id_config);
            $objConfigEmpresa->nombre = $request->nombre;
            $objConfigEmpresa->cantidad_usuarios = $request->cantidad_usuarios;
            $objConfigEmpresa->cantidad_hectareas = $request->cant_hectarea;
            $objConfigEmpresa->propagacion = $request->propagacion;
            $objConfigEmpresa->campo = $request->campo;
            $objConfigEmpresa->postcocecha = $request->postcocecha;
            $objConfigEmpresa->moneda = $request->moneda;
            $objConfigEmpresa->razon_social = $request->razon_social;
            $objConfigEmpresa->direccion_matriz = $request->matriz;
            $objConfigEmpresa->direccion_establecimiento = $request->establecimiento;
            $objConfigEmpresa->codigo_pais = $request->codigo_pais;
            $objConfigEmpresa->correo = $request->correo;
            $objConfigEmpresa->telefono = $request->telefono;
            $objConfigEmpresa->fax =$request->fax;
            $objConfigEmpresa->save();

            $idConfiEmpresa = ConfiguracionEmpresa::all()->last();
            $cntClasifiUnit = count($request->clasifi_unit_tipos);
            $cntClasifiXRamos = count($request->clasifi_x_ramos_tipos);
            $cntempaques = count($request->empaque_nombres);

            $msg = '';

            for ($i = 0; $i < $cntClasifiUnit; $i++) {

                $existIdClasificacionUnitaria = ClasificacionUnitaria::find($request->arrIdClasifiUnit[$i]);
                $objClasifiUnit = '';
                empty($existIdClasificacionUnitaria->id_clasificacion_unitaria) ? $objClasifiUnit = new ClasificacionUnitaria : $objClasifiUnit = ClasificacionUnitaria::find($request->arrIdClasifiUnit[$i]);
                $objClasifiUnit->nombre = $request->clasifi_unit_tipos[$i];
                $objClasifiUnit->id_configuracion_empresa = $idConfiEmpresa->id_configuracion_empresa;

                if ($objClasifiUnit->save()) {
                    $model = ClasificacionUnitaria::all()->last();
                    $success = true;
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado la clasificación unitaria ' . $objClasifiUnit->nombre . '  exitosamente</p>'
                        . '</div>';
                    bitacora('configuracion_empresa', $model->id_clasificacion_unitaria, 'I', 'Inserción satisfactoria de una nueva clasificación unitaria');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            }

            for ($j = 0; $j < $cntClasifiXRamos; $j++) {

                $existIdClasifiXRamos = ClasificacionRamo::find($request->arrIdClasifiXRamos[$j]);
                $objClasifiXRamos = '';
                empty($existIdClasifiXRamos->id_clasificacion_ramo) ? $objClasifiXRamos = new ClasificacionRamo : $objClasifiXRamos = ClasificacionRamo::find($request->arrIdClasifiXRamos[$j]);
                $objClasifiXRamos->nombre = $request->clasifi_x_ramos_tipos[$j];
                $objClasifiXRamos->id_configuracion_empresa = $idConfiEmpresa->id_configuracion_empresa;

                if ($objClasifiXRamos->save()) {
                    $model = ClasificacionRamo::all()->last();
                    $success = true;
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado la clasificación por rampos ' . $objClasifiXRamos->nombre . ' exitosamente</p>'
                        . '</div>';
                    bitacora('clasificacion_ramo', $model->id_clasificacion_ramo, 'I', 'Inserción satisfactoria de una nueva clasificación por ramos');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>';
                }
            }

            for ($x = 0; $x < $cntempaques; $x++) {

                $existIdClasifiEmpaques = Empaque::find($request->arrIdClasifiEmpaque[$x]);
                $objEmpaques = '';
                empty($existIdClasifiEmpaques->id_empaque) ? $objEmpaques = new Empaque : $objEmpaques = Empaque::find($request->arrIdClasifiEmpaque[$x]);
                $objEmpaques->nombre = $request->empaque_nombres[$x][0];
                $objEmpaques->tipo = $request->empaque_nombres[$x][1];
                $objEmpaques->id_configuracion_empresa = $idConfiEmpresa->id_configuracion_empresa;

                if ($objEmpaques->save()) {
                    $model = Empaque::all()->last();
                    $success = true;
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado el empaque ' . $objEmpaques->nombre . '  exitosamente</p>'
                        . '</div>';
                    bitacora('clasificacion_ramo', $model->id_empaque, 'I', 'Inserción satisfactoria de un nuevo empaque');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>';
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

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function actualizarEstado(Request $request)
    {

        if (!empty($request->id_clasificacion)) {

            $model = '';
            switch ($request->clase) {
                case 'clasificacion_x_ramo':
                    $model = ClasificacionRamo::find($request->id_clasificacion);
                    $tabla = 'clasificacion_ramo';
                    break;

                case 'clasificacion_unitaria':
                    $model = ClasificacionUnitaria::find($request->id_clasificacion);
                    $tabla = 'clasificacion_unitaria';
                    break;

                case 'empaque':
                    $model = Empaque::find($request->id_clasificacion);
                    $tabla = 'empaque';
                    break;
            }

            $request->estado == 1 ? $model->estado = 0 : $model->estado = 1;
            $msg = '';
            if ($model->save()) {
                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> La clasificación ha sido modificada exitosamente</p>'
                    . '</div>';
                bitacora($tabla, $request->id_clasificacion, 'U', 'Actualización satisfactoria de una calsificación');
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

    public function guardarDetalleEmpaque(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'data_detalles_empaque' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            foreach ($request->data_detalles_empaque as $key => $detalles_empaques) {

                isset($detalles_empaques[4]) ? $existDetalleEmpaque = DetalleEmpaque::find($detalles_empaques[4]) : $existDetalleEmpaque = '';

                if ($existDetalleEmpaque != '') {
                    $objDetalleEmpaque = DetalleEmpaque::find($existDetalleEmpaque->id_detalle_empaque);
                    $accion = "U";
                    $palabra = "Actualización";
                } else {
                    $objDetalleEmpaque = new DetalleEmpaque;
                    $accion = "I";
                    $palabra = "Inserción";
                }

                $msg = '';
                $objDetalleEmpaque->id_empaque = $detalles_empaques[3];
                $objDetalleEmpaque->id_clasificacion_ramo = $detalles_empaques[1];
                $objDetalleEmpaque->id_variedad = $detalles_empaques[0];
                $objDetalleEmpaque->cantidad = $detalles_empaques[2];

                if ($objDetalleEmpaque->save()) {
                    $model = DetalleEmpaque::all()->last();
                    $success = true;
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado el detalle del empaque ' . $request->nombre . '  exitosamente</p>'
                        . '</div>';
                    bitacora('detalle_empaque', $model->id_detalle_empaque, $accion, $palabra . ' satisfactoria de un nuevo detalle de empaque');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>';
                }

            }
            return [
                'mensaje' => $msg,
                'success' => $success
            ];

        }
    }

    public function actualizarEstadoDetalleEmpaque(Request $request)
    {

        if (!empty($request->id_detalle_empaque)) {

            $model = DetalleEmpaque::find($request->id_detalle_empaque);

            $request->estado == 1 ? $model->estado = 0 : $model->estado = 1;
            $msg = '';
            if ($model->save()) {
                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> El detalle de empaque ha sido modificado exitosamente</p>'
                    . '</div>';
                bitacora('detalle_empaque', $request->id_detalle_empaque, 'U', 'Actualización satisfactoria de un detalle de empaque');
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

    public function vistaInputsDetallesEmpaque(Request $request)
    {

        $dataClasificacionRamo = ClasificacionRamo::all();
        $dataVariedad = Variedad::all();
        return view('adminlte.gestion.configuracion_empresa.forms.partials.inputs_dinamicos_detalle_empaque', [
            'dataClasificacionRamo' => $dataClasificacionRamo,
            'dataVariedad' => $dataVariedad,
            'id' => $request->cant_tr
        ]);
    }

    public function campos_empaque(Request $request)
    {

        return view('adminlte.gestion.configuracion_empresa.forms.partials.add_empaque',
            ['cnatInptus' => $request->cant_tr]);
    }
}
