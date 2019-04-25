<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Camion;
use yura\Modelos\Conductor;
use yura\Modelos\Despacho;
use yura\Modelos\DetalleDespacho;
use yura\Modelos\Pedido;
use yura\Modelos\Submenu;
use yura\Modelos\Transportista;
use yura\Modelos\Variedad;
use Validator;
use Barryvdh\DomPDF\Facade as PDF;


class DespachosController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.despachos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get(),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'unitarias' => getUnitarias(),
        ]);
    }

    public function listar_resumen_pedidos(Request $request)
    {
        $listado = [];

        if ($request->fecha != '') {
            $listado = DB::table('pedido as p')
                ->join('cliente as c', 'c.id_cliente', '=', 'p.id_cliente')
                ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'p.id_cliente')
                ->select('p.*')->distinct()
                ->where('dc.estado', '=', 1)
                ->where('c.estado', '=', 1)
                ->where('p.estado', '=', 1)
                //->where('p.empaquetado', '=', 0)
                ->where('p.fecha_pedido', '=', $request->fecha)
                ->orderBy('dc.nombre', 'asc')
                ->get();

            $ids_pedidos = [];
            foreach ($listado as $item) {
                array_push($ids_pedidos, $item->id_pedido);
            }

            $ramos_x_variedad = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida',
                    DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))
                ->whereIn('dp.id_pedido', $ids_pedidos)
                ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->orderBy('dp.id_pedido', 'desc')
                ->get();

            $variedades = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad')->distinct()
                ->whereIn('dp.id_pedido', $ids_pedidos)
                ->get();
        }

        $datos = [
            'listado' => $listado,
            'fecha' => $request->fecha,
            'ramos_x_variedad' => $ramos_x_variedad,
            'variedades' => $variedades,
            'opciones' => $request->opciones,
        ];

        return view('adminlte.gestion.postcocecha.despachos.partials.listado', $datos);
    }

    public function crear_despacho(Request $request){
        $arr_data_pedido = [];
        foreach ($request->pedidos as $id_pedido) {
            $arr_data_pedido[] = Pedido::where('id_pedido',$id_pedido)->first();
        }
        if(!empty($request->pedidos)){
            return view('adminlte.gestion.postcocecha.despachos.form.despacho_listado',[
                'pedidos' => $arr_data_pedido,
                'empresa' => getConfiguracionEmpresa(),
                'transportistas' => Transportista::where('estado',1)->get(),
                'datos_responsables' => Despacho::all()->last()
            ]);
        }else{

        }
    }

    public function list_camiones_conductores(Request $request){
        return response()->json([
            'camiones' => Camion::where([
                ['id_transportista',$request->id_transportista],
                ['estado',1]
            ])->get(),
            'conductores' => Conductor::where([
                ['id_transportista',$request->id_transportista],
                ['estado',1]
            ])->get()
        ]);
    }

    public function list_placa_camion(Request $request){
       return Camion::where([
                ['id_camion',$request->id_camion],
                ['estado',1]
            ])->select('placa')->first();
    }

    public function store_despacho(Request $request){

        $valida = Validator::make($request->all(), [
            'fecha_despacho' => 'required',
            'firma_id_transportista' => 'required',
            'id_asist_comercial' => 'required',
            'id_camion' => 'required',
            'id_conductor' => 'required',
            'id_cuarto_frio' => 'required',
            'id_guardia_turno' => 'required',
            'id_oficina_despacho' => 'required',
            'id_transportista' => 'required',
            'n_placa' => 'required',
            'n_viaje' => 'required',
            'nombre_asist_comercial' => 'required',
            'nombre_cuarto_frio' => 'required',
            'nombre_guardia_turno' => 'required',
            'nombre_oficina_despacho' => 'required',
            'nombre_transportista' => 'required',
            'sello_salida' => 'required',
            'sellos' => 'required',
            'semana' => 'required',
            'pedidos'  => 'required',
            'correo_oficina_despacho'  => 'required'
        ]);

        if (!$valida->fails()) {
            $msg = '';
            $objDespacho = new Despacho;
            $objDespacho->id_transportista = $request->id_transportista;
            $objDespacho->id_camion = $request->id_camion;
            $objDespacho->id_conductor = $request->id_conductor;
            $objDespacho->fecha_despacho = $request->fecha_despacho;
            $objDespacho->sello_salida = $request->sello_salida;
            $objDespacho->semana = $request->semana;
            $objDespacho->rango_temp = $request->rango_temp;
            $objDespacho->n_viaje = $request->n_viaje;
            $objDespacho->hora_salida = $request->horas_salida;
            $objDespacho->temp = $request->temperatura;
            $objDespacho->kilometraje = $request->kilometraje;
            $objDespacho->sellos = substr(implode("|",$request->sellos), 0, -1);
            $objDespacho->sello_adicional =$request->sello_adicional;
            $objDespacho->horario = $request->horario;
            $objDespacho->resp_ofi_despacho = $request->nombre_oficina_despacho;
            $objDespacho->id_resp_ofi_despacho = $request->id_oficina_despacho;
            $objDespacho->aux_cuarto_fri = $request->nombre_cuarto_frio;
            $objDespacho->id_aux_cuarto_fri = $request->id_cuarto_frio;
            $objDespacho->guardia_turno = $request->nombre_guardia_turno;
            $objDespacho->id_guardia_turno = $request->id_guardia_turno;
            $objDespacho->asist_comercial_ext = $request->nombre_asist_comercial;
            $objDespacho->id_asist_comrecial_ext = $request->id_asist_comercial;
            $objDespacho->resp_transporte = $request->nombre_transportista;
            $objDespacho->id_resp_transporte = $request->firma_id_transportista;
            $objDespacho->correo_oficina_despacho = $request->correo_oficina_despacho;
            $objDespacho->n_despacho = getSecuenciaDespacho();

            if ($objDespacho->save()) {
                $modelDespacho = Despacho::all()->last();
                bitacora('despacho', $modelDespacho->id_despacho, 'I', 'Inserción satisfactoria de un nuevo despacho');
                foreach ($request->pedidos as $p){
                    $objDetalleDespacho = new DetalleDespacho;
                    $objDetalleDespacho->id_despacho = $modelDespacho->id_despacho;
                    $objDetalleDespacho->id_pedido = $p;
                    if($objDetalleDespacho->save()){
                        $modelDetalleDespacho = DetalleDespacho::all()->last();
                        bitacora('detalle_despacho', $modelDetalleDespacho->id_detalle_despacho, 'I', 'Inserción satisfactoria de un nuevo detalle de despacho');
                        $success = true;
                        $msg = '<div class="alert alert-success text-center">' .
                            '<p> Se ha guardado el despacho ' . str_pad($objDespacho->n_despacho, 9, "0", STR_PAD_LEFT) . '  exitosamente, 
                            <a target="_blank" href="'.url('despachos/descargar_despacho/'.$objDespacho->n_despacho.'').'">  clic aquí para ver y descargar</a></p>'
                            . '</div>';
                    }else{
                        DetalleDespacho::where('id_despacho',$modelDespacho->id_despacho)->delete();
                        Despacho::destroy($modelDespacho->id_despacho);
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                            . '</div>';
                        $success = false;
                        return [
                            'mensaje' => $msg,
                            'success' => $success
                        ];
                    }
                }
                $data = [
                    'empresa' => getConfiguracionEmpresa(),
                    'despacho' => Despacho::where('n_despacho',(getSecuenciaDespacho()-1))
                        ->join('detalle_despacho as dd','despacho.id_despacho','dd.id_despacho')->get()
                ];
                PDF::loadView('adminlte.gestion.postcocecha.despachos.partials.pdf_despacho', compact('data'))->setPaper('a4', 'landscape')
                    ->save(env('PATH_PDF_DESPACHOS') . str_pad((getSecuenciaDespacho()-1), 9, "0", STR_PAD_LEFT) . ".pdf");
            }else{
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
            'success' => $success,
        ];
    }

    public function descargar_despacho($n_despacho){
        $data = [
            'empresa' => getConfiguracionEmpresa(),
            'despacho' => Despacho::where('n_despacho',$n_despacho)
                ->join('detalle_despacho as dd','despacho.id_despacho','dd.id_despacho')->get()
        ];
        return PDF::loadView('adminlte.gestion.postcocecha.despachos.partials.pdf_despacho', compact('data'))
            ->setPaper('a4', 'landscape')->stream();
    }

    public function ver_despachos(Request $request){
        return view('adminlte.gestion.postcocecha.despachos.partials.despachos',[
            'listado' => Despacho::where('estado',1)->paginate(20)
        ]);
    }

    public function update_estado_despachos(Request $request){
        $despacho = Despacho::find($request->id_despacho);
        if($despacho->update(['estado'=>$request->estado == 1 ? 0 :1])){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha actualizado el estado del despacho exitosamente</p>'
                . '</div>';
        }else{
            $success = false;
            $msg = '<div class="alert danger text-center">' .
                '<p> Hubo un error al intentar actualizar el estado del despacho exitosamente</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
        ];
    }
}
