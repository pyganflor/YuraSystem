<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Cliente;
use yura\Modelos\Color;
use yura\Modelos\DetalleEnvio;
use yura\Modelos\Empaque;
use yura\Modelos\Pedido;
use DB;
use yura\Modelos\Submenu;
use yura\Modelos\DetalleCliente;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\ClienteAgenciaCarga;
use yura\Modelos\Envio;
use Carbon\Carbon;
use yura\Modelos\Especificacion;
use yura\Modelos\AgenciaCarga;

class PedidoVentaController extends Controller
{
    public function listar_pedidos(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Pedidos', 'subtitulo' => 'módulo de pedidos'],
                'clientes' => DB::table('cliente as c')
                    ->join('detalle_cliente as dc', 'c.id_cliente', '=', 'dc.id_cliente')
                    ->where('dc.estado', 1)->get(),
                'annos' => DB::table('pedido as p')->select(DB::raw('YEAR(p.fecha_pedido) as anno'))
                    ->distinct()->get(),
            ]);
    }

    public function buscar_pedidos(Request $request)
    {
        $busquedaCliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $busquedaAnno = $request->has('anno') ? $request->anno : '';
        $busquedaDesde = $request->has('desde') ? $request->desde : '';
        $busquedaHasta = $request->has('hasta') ? $request->hasta : '';

        $listado = DB::table('pedido as p')
            ->where('p.estado', $request->estado != '' ? $request->estado : 1)
            ->join('cliente_pedido_especificacion as cpe', 'p.id_cliente', '=', 'cpe.id_cliente')
            ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
            ->join('detalle_cliente as dc', 'p.id_cliente', '=', 'dc.id_cliente')
            ->join('detalle_pedido as dp', 'p.id_pedido', 'dp.id_pedido')
            ->select('p.*', 'dp.*', 'dc.nombre', 'p.fecha_pedido', 'p.id_cliente', 'dc.id_cliente')->where('dc.estado', 1);

        if ($request->anno != '')
            $listado = $listado->where(DB::raw('YEAR(p.fecha_pedido)'), $busquedaAnno);

        if ($busquedaDesde != '' && $request->hasta != '') {
            $listado = $listado->whereBetween('p.fecha_pedido', [$busquedaDesde, $busquedaHasta]);
            (Carbon::parse($busquedaHasta)->diffInDays($busquedaDesde) > 0)
                ? $a = true
                : $a = false;
        } else {
            $listado = $listado->where('p.fecha_pedido', Carbon::now()->toDateString());
            $a = false;
        }

        if ($request->id_cliente != '')
            $listado = $listado->where('p.id_cliente', $busquedaCliente);

        $listado = $listado->distinct()->orderBy('p.fecha_pedido', 'desc')->simplePaginate(20);

        $datos = [
            'listado' => $listado,
            'idCliente' => $request->id_cliente,
            'columnaFecha' => $a
        ];

        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.listado', $datos);
    }

    public function cargar_especificaciones(Request $request)
    {
        return [
            'especificaciones' => ClientePedidoEspecificacion::where('id_cliente', $request->id_cliente)
                ->join('especificacion as e', 'cliente_pedido_especificacion.id_especificacion', 'e.id_especificacion')
                ->select('cliente_pedido_especificacion.id_cliente_pedido_especificacion', 'e.nombre')->get(),
            'agencias_carga' => ClienteAgenciaCarga::where('id_cliente', $request->id_cliente)
                ->join('agencia_carga as ac', 'cliente_agenciacarga.id_agencia_carga', '=', 'ac.id_agencia_carga')
                ->select('ac.id_agencia_carga', 'ac.nombre')->get()
        ];
    }

    public function add_orden_semanal(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.add_orden_semanal', [
            'clientes' => Cliente::All()->where('estado', '=', 1),
            'empaques' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'C'),
            //'envolturas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'E'),
            'presentaciones' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'P'),
            'colores' => Color::All()->where('estado', '=', 1),
        ]);
    }

    public function editar_pedido(Request $request)
    {
        return Pedido::where([
            ['pedido.id_pedido', $request->id_pedido],
            ['dc.estado', 1]
        ])->join('detalle_pedido as dp', 'pedido.id_pedido', 'dp.id_pedido')
            ->join('detalle_cliente as dc', 'pedido.id_cliente', 'dc.id_cliente')
            ->join('cliente_pedido_especificacion as cpe', 'dp.id_cliente_especificacion', 'cpe.id_cliente_pedido_especificacion')
            ->select('dp.cantidad as cantidad_especificacion', 'dp.precio','dp.id_agencia_carga', 'dc.id_cliente', 'pedido.fecha_pedido', 'pedido.descripcion', 'cpe.id_especificacion')->get();
    }

    public function duplicar_especificacion(Request $request){
        $clienteEspecificacion = DB::table('cliente_pedido_especificacion as cpe')
            ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
            ->where([
                ['cpe.id_cliente', $request->id_cliente],
                ['esp.id_especificacion',$request->id_especificacion]
            ])->select('cpe.id_cliente_pedido_especificacion')->first();
        $agenciasCarga = AgenciaCarga::where('c_ac.id_cliente',$request->id_cliente)
            ->join('cliente_agenciacarga as c_ac','agencia_carga.id_agencia_carga','c_ac.id_agencia_carga')->get();
        $html= '';
        $a = 1;
        $total_precio_variedad = 0.00;
        $identificador_especificacion = $request->cant_esp+1;
        $tdRowspan = getCantidadDetallesByEspecificacion($request->id_especificacion);
        $b=1;
        foreach(getEspecificacion($request->id_especificacion)->especificacionesEmpaque as $y => $esp_emp){
            foreach($esp_emp->detalles as $z => $det_esp_emp){
                $options_precios = "";
                $options_agencias_carga = "";
                $html .="<tr style='border-top: 2px solid #9d9d9d' class='tr_remove_".$identificador_especificacion."'>";
                if($a==1)
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; '
                            class='text-center' rowspan='".$tdRowspan."'>
                            <input type='number' min='0' id='cantidad_piezas_".$identificador_especificacion."' style='border: none' onchange='calcular_precio_pedido()'
                                   name='cantidad_piezas_".$request->id_especificacion."' class='input_cantidad text-center form-control cantidad_".$identificador_especificacion."'>
                                   <input type='hidden' id='id_cliente_pedido_especificacion_".$identificador_especificacion."' value='".$clienteEspecificacion->id_cliente_pedido_especificacion."'>
                         </td>";
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'>"
                            .$det_esp_emp->variedad->siglas.
                            "<input type='hidden' class='input_variedad_".$identificador_especificacion."' id='id_variedad_".$identificador_especificacion."_".$b."' value='".$det_esp_emp->variedad->id_variedad."'>";
                         "</td>";
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'>"
                            .$det_esp_emp->clasificacion_ramo->nombre." ".$det_esp_emp->clasificacion_ramo->unidad_medida->siglas.
                            "<input type='hidden' id='id_detalle_especificacion_empaque_".$identificador_especificacion."_".$b."' value='".$det_esp_emp->id_detalle_especificacionempaque."'>";
                         "</td>";
                if($z == 0)
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'  rowspan=".count($esp_emp->detalles).">"
                                .explode('|',$esp_emp->empaque->nombre)[0].
                         "</td>";
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'>"
                            .$det_esp_emp->empaque_p->nombre.
                         "</td>";
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'>"
                            .$det_esp_emp->cantidad.
                            "<input type='hidden' class='input_ramos_x_caja_".$identificador_especificacion.'_'.$b."  td_ramos_x_caja_".$identificador_especificacion."' value='".$det_esp_emp->cantidad."'>";
                         "</td>";
                if($a==1)
                $html .= "<td id='td_total_ramos_".$identificador_especificacion."' style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center' rowspan='".$tdRowspan."'></td>";
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'>"
                            .$det_esp_emp->tallos_x_ramos.
                         "</td>";
                $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'>"
                            .(($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '') == 1 ? $det_esp_emp->longitud_ramo.' '.$det_esp_emp->unidad_medida->siglas : '-').
                        "</td>";
                foreach(explode('|',getPrecioByClienteDetEspEmp($request->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $precio){
                    $options_precios .= "<option value=".$precio.">".$precio."</option>";
                }
                $html .= "<td id='td_precio_variedad_".$det_esp_emp->id_detalle_especificacionempaque."_".$identificador_especificacion."' style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center'>";
                    if(getPrecioByClienteDetEspEmp($request->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != ''){
                        $htmlb ="<select name='precio_".$det_esp_emp->id_detalle_especificacionempaque."'
                                     ondblclick='cambiar_input_precio($det_esp_emp->id_detalle_especificacionempaque,$identificador_especificacion,$b)'
                                     id='precio_".$identificador_especificacion."_".$b."' style='background-color: beige; width: 100%' onchange='calcular_precio_pedido()' class='form-control precio_".$identificador_especificacion."' required>"
                            .$options_precios.
                            "</select>";
                    }else{
                        $htmlb = "<input type='number'
                                   name='precio_".$identificador_especificacion."' id='precio_".$identificador_especificacion."_".$b."' class='form-control text-center precio_".$identificador_especificacion." form-control'
                                   style='background-color: beige; width: 100%' onchange='calcular_precio_pedido()' min='0' value='0' required>";
                    }
                    $html .= $htmlb."</td>";
                if($a==1)
                $html .= "<td id='td_precio_especificacion_".$identificador_especificacion."' style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center' rowspan='".$tdRowspan."'>               
                         </td>";
                if($a==1) {
                    foreach($agenciasCarga as $ac){
                        $options_agencias_carga .= "<option value=".$ac->id_agencia_carga.">".$ac->nombre."</option>";
                    }
                    $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center' rowspan='" . $tdRowspan . "'>
                                    <select name='id_agencia_carga_".$request->id_especificacion."' id='id_agencia_carga_".$identificador_especificacion."'
                                         class='text-center form-control' style='border: none; width: 100%'>
                                             ".$options_agencias_carga."                      
                                    </select>
                             </td>";
                }
                if($a==1){
                    $html .= "<td style='border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;'  class='text-center' rowspan='".$tdRowspan."'>
                                 <button type='button' title='Duplicar especificación' class='btn btn-xs btn-primary' onclick='duplicar_especificacion($request->id_especificacion)'>
                                     <i class='fa fa-fw fa-copy'></i>
                                 </button>
                             </td>";
                }
                $html .= "</tr>";
                $b++;
                $a++;
            }
        }
        return $html;
    }
}
