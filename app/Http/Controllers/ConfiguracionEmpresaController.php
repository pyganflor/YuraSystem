<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
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


class ConfiguracionEmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config_empresa = ConfiguracionEmpresa::where('estado',1)->first();
        $moneda = Icon::whereIn('nombre',['usd','jpy','eur','krw','try','inr','gbp','rub'])
            ->orderBy('id_icono', 'desc')->get();

        !empty($config_empresa->propagacion) ? $arrPropagacion = explode("|", $config_empresa->propagacion) : $arrPropagacion = false;
        !empty($config_empresa->campo) ? $arrCampo = explode("|", $config_empresa->campo) : $arrCampo = false;
        !empty($config_empresa->postcocecha) ? $arrPostcocecha = explode("|", $config_empresa->postcocecha) : $arrPostcocecha = false;

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
            'nombre' => 'required',
            'cant_hectarea' => 'required',
            'cantidad_usuarios' => 'required',
            'clasifi_unit_tipos' => 'required',
            'clasifi_x_ramos_tipos' => 'required',
            'empaque_nombres' => 'required',
            'moneda' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
            'codigo_pais' => 'required',
            'permiso_agrocalidad' => 'required',
            'obligado_contabilidad'=>'required',
            'img_empresa' => 'mimes:jpg,JPG,JPEG,jpeg,PNG,png',
            'contrasena_firma_digital'=>'required',
            'inicial_factura'=>'required',
            'inicial_guia'=>'required',
            'incial_despacho'=>'required',
            'inicial_lote'=>'required'
        ],[
            'firma_electronica.required' => 'El archivo de la firma electrónica de la empresa es obligatorio',
            'clasifi_unit_tipos.required' => 'No se obtuvieron las clasificaciones unitarias',
            'clasifi_x_ramos_tipos.required' => 'No se obtuvieron las clasificaciones por ramo',
            'empaque_nombres.required' => 'No se obtuvieron los nombres de los empaques',
            'codigo_pais.required' => 'Debe seleccionar una opción  en el campo país',
            'cant_hectarea.required' => 'Debe colocar la cantidad de hectareas',
            'cantidad_usuarios.required' => 'Debe colocar la cantidad de usuarios que facturaran',
            'obligado_contabilidad.required' => 'Debe seleccionar si la empresa es obligada a llevar contabilidad o no',
            'img_empresa.mimes' => 'La imagen de la empresa debe ser en formato jpg o png',
            'contrasena_firma_digital.required'=> 'Debe colocar la contraseña del archivo de la firma digital de la empresa',
            'inicial_factura.required'=> 'Debe colocar el último numero de factura electronica con que la empresa termino antes de migrar a este sistema',
            'inicial_guia.required'=> 'Debe colocar el último número de guía de remisión electronica con que la empresa termino antes de migrar a este sistema',
            'inicial_lote.required'=> 'Debe colocar el último número de documento en lote enviado de forma electrónica al SRI, si su empresa no usa el envío en lote deje este valor en 0',
            'incial_despacho.required'=> 'Debe colocar el último número de despacho con el que terminó en la anterior empresa antes de migrar a este sistema',
        ]);

        $success = false;
        $msg = '<div class="alert alert-warning text-center">' .
            '<p> Ha ocurrido un problema al guardar la información al sistema</p>';

        if (!$valida->fails()) {
            empty($request['id_config']) ? $objConfigEmpresa = new ConfiguracionEmpresa : $objConfigEmpresa = ConfiguracionEmpresa::find($request['id_config']);
            $continuar = true;
            if(!empty($request['id_config']) && empty($objConfigEmpresa->firma_electronica)){
                $valida = Validator::make($request->all(), [
                    'firma_electronica' => 'required',
                ],['firma_electronica.required' => 'El archivo de la firma electrónica de la empresa es obligatorio']);
                if ($valida->fails()) {
                    $continuar = false;
                    $success = false;
                    $errores = '';
                    foreach ($valida->errors()->all() as $mi_error) {
                        if ($errores == '') {
                            $errores = '<li>' . $mi_error . '</li>';
                        } else {    }
                    }
                    $errores .= '<li>' . $mi_error . '</li>';

                    $msg = '<div class="alert alert-danger">' .
                        '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                        '<ul>' .
                        $errores .
                        '</ul>' .
                        '</div>';
                }
            }
            if($continuar){
                $objConfigEmpresa->nombre = $request['nombre'];
                $objConfigEmpresa->cantidad_usuarios = $request['cantidad_usuarios'];
                $objConfigEmpresa->cantidad_hectareas = $request['cant_hectarea'];
                $objConfigEmpresa->propagacion = $request['propagacion'];
                $objConfigEmpresa->campo = $request['campo'];
                $objConfigEmpresa->postcocecha = $request['postcocecha'];
                $objConfigEmpresa->moneda = $request['moneda'];
                $objConfigEmpresa->razon_social = $request['razon_social'];
                $objConfigEmpresa->direccion_matriz = $request['matriz'];
                $objConfigEmpresa->direccion_establecimiento = $request['establecimiento'];
                $objConfigEmpresa->codigo_pais = $request['codigo_pais'];
                $objConfigEmpresa->correo = $request['correo'];
                $objConfigEmpresa->telefono = $request['telefono'];
                $objConfigEmpresa->fax = $request['fax'];
                $objConfigEmpresa->permiso_agrocalidad = $request['permiso_agrocalidad'];
                $objConfigEmpresa->ruc = $request['ruc'];
                $objConfigEmpresa->obligado_contabilidad = $request['obligado_contabilidad'];
                $objConfigEmpresa->contrasena_firma_digital = $request['contrasena_firma_digital'];
                $objConfigEmpresa->inicial_factura = $request['inicial_factura'];
                $objConfigEmpresa->inicial_lote = $request['inicial_lote'];
                $objConfigEmpresa->inicial_guia_remision = $request['inicial_guia'];
                $objConfigEmpresa->inicial_despacho = $request['incial_despacho'];
                if($request->has('firma_electronica')){
                    $firma = $request->file('firma_electronica');
                    $nombre_archivo = $request['razon_social']."_".$firma->getClientOriginalName();
                    $extension = $firma->getClientOriginalExtension();
                    if($extension === "P12" || $extension === "p12"){
                        $firma->move(env('PATH_FIRMA_DIGITAL'),$nombre_archivo);
                        $objConfigEmpresa->firma_electronica = $nombre_archivo;
                    }
                }
                if($request->has('img_empresa')){
                    $imagen = $request->file('img_empresa');
                    $nombre_archivo = $request['razon_social']."_".$imagen->getClientOriginalName();
                    $imagen->move(public_path('images'),$nombre_archivo);
                    $objConfigEmpresa->imagen = $nombre_archivo;
                }

                if($objConfigEmpresa->save()){
                    $msg = "";

                    $idConfiEmpresa = ConfiguracionEmpresa::all()->last();

                    $clasifiUnitId = explode(",",$request['arrIdClasifiUnit']);
                    $clasifiUnitTipos = explode(",",$request['clasifi_unit_tipos']);
                    $cntClasifiUnit = count($clasifiUnitTipos);

                    $clasifiXRamosId = explode(",",$request['arrIdClasifiXRamos']);
                    $clasifiXRamosTipos = explode(",",$request['clasifi_x_ramos_tipos']);
                    $cntClasifiXRamos = count($clasifiXRamosTipos);

                    $clasifiEmpaqueId = explode(",",$request['arrIdClasifiEmpaque']);
                    $empaquesTipos = explode(",",$request['empaque_nombres']);
                    $cntEmpaques = count($empaquesTipos);

                    $msg = '';
                    for ($i = 0; $i < $cntClasifiUnit; $i++) {

                        $existIdClasificacionUnitaria = ClasificacionUnitaria::find($clasifiUnitId[$i]);
                        empty($existIdClasificacionUnitaria->id_clasificacion_unitaria) ? $objClasifiUnit = new ClasificacionUnitaria : $objClasifiUnit = ClasificacionUnitaria::find($clasifiUnitId[$i]);
                        $objClasifiUnit->nombre = $clasifiUnitTipos[$i];
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

                        $existIdClasifiXRamos = ClasificacionRamo::find($clasifiXRamosId[$j]);
                        empty($existIdClasifiXRamos->id_clasificacion_ramo) ? $objClasifiXRamos = new ClasificacionRamo : $objClasifiXRamos = ClasificacionRamo::find($clasifiXRamosId[$j]);
                        $objClasifiXRamos->nombre = $clasifiXRamosTipos[$j];
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
                   // dd($empaquesTipos);
                    for ($x = 0; $x < $cntEmpaques; $x++) {
                        $existIdClasifiEmpaques = Empaque::find($clasifiEmpaqueId[$x]);
                        empty($existIdClasifiEmpaques->id_empaque) ? $objEmpaques = new Empaque : $objEmpaques = Empaque::find($clasifiEmpaqueId[$x]);
                        $objEmpaques->nombre =  substr($empaquesTipos[$x],0,-2);
                        $objEmpaques->tipo = isset(explode("|",$empaquesTipos[$x])[3]) ? explode("|",$empaquesTipos[$x])[3] : explode("|",$empaquesTipos[$x])[1];
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
                }
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {    }
            }
            $errores .= '<li>' . $mi_error . '</li>';

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

    public function empresa_facturacion(Request $request){
        return view('adminlte.gestion.configuracion_empresa.partials.admin_empresa_facturacion_inicio',[
            'config_empresa' => ConfiguracionEmpresa::where('estado',false)->get()
        ]);
    }
}


