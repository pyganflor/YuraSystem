<div id="table_despachos">
    @if(count($listado)>0)
        <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_aperturas">
            <tr>
                <th style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2">
                    <ul class="list-unstyled">
                        <li>
                            Semana: {{isset(getSemanaByDate($fecha)->codigo) ? getSemanaByDate($fecha)->codigo : "Semana no programada"}}
                        </li>
                        <li>
                            Día: {{getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime($fecha)))]}}
                        </li>
                    </ul>
                </th>
                <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-right" colspan="{{$opciones ? "10" : "9"}}">
                    @if(!$opciones)
                        <button type="button" class="btn btn-xs btn-primary" onclick="ver_despachos()">
                            <i class="fa fa-eye" aria-hidden="true"></i> Ver despachos
                        </button>
                        <button type="button" class="btn btn-xs btn-primary" onclick="crear_despacho()">
                            <i class="fa fa-truck" aria-hidden="true"></i> Crear despacho
                        </button>
                    @endif
                    <button type="button" class="btn btn-xs btn-success">
                        <i class="fa fa-fw fa-file-excel-o"></i> Exportar a Excel
                    </button>
                </th>
            </tr>
            <tr>
                @if(!$opciones)
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;width:50px">
                        DESPACHO
                    </th>
                @endif
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    CLIENTE
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    MARACACIONES
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    FLOR
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    EMPAQUE
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    PRESENTACIÓN
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    PIEZAS
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    CAJAS FULL
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    RAMOS
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    RAMOS x CAJA
                </th>
                @if($opciones)
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        CUARTO FRÍO
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;width:140px">
                        OPCIONES
                    </th>
                @endif
            </tr>
            @php
                $piezas_totales = 0;
                $ramos_totales = 0;
                $ramos_totales_estandar = 0;
                $cajas_full_totales = 0;
                $variedades = [];
                $ramos_x_variedades = [];
                $valor_total = 0;
            @endphp
            @foreach($listado as $pedido)
                @php $despachado = getCantDespacho($pedido->id_pedido) @endphp
                @foreach(getPedido($pedido->id_pedido)->detalles as $pos_det_ped => $det_ped)
                    @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                        @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                            @php
                                if(isset(getPedido($pedido->id_pedido)->envios[0]->id_envio)){
                                    $firmado = getFacturado(getPedido($pedido->id_pedido)->envios[0]->id_envio,1);
                                    $facturado = getFacturado(getPedido($pedido->id_pedido)->envios[0]->id_envio,5);
                                }else{
                                    $firmado = null;
                                    $facturado = null;
                                }
                            @endphp
                            <tr style="background-color: {{!in_array($det_esp->id_variedad,explode('|',$pedido->variedad)) ? '#b9ffb4' : ''}}; border-bottom: 2px solid #9d9d9d"
                                title="{{!in_array($det_esp->id_variedad,explode('|',$pedido->variedad)) ? 'Confirmado' : 'Por confirmar'}}">
                                @if($pos_det_esp == 0 && $pos_esp_emp == 0 && $pos_det_ped == 0)
                                    @if(!$opciones)
                                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"
                                            rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                            @if($despachado > 0)
                                                <i class="fa fa-2x fa-check-circle text-success" title="Despachado" aria-hidden="true"></i>
                                            @else
                                                <input type="number" name="orden_despacho" id="{{$pedido->id_pedido}}"
                                                       class="form-control orden_despacho"
                                                       min="1" style="width: 56px;border:none;text-align: center">
                                            @endif

                                        </td>
                                    @endif
                                    <td class="text-center" style="border-color: #9d9d9d"
                                        rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                        {{getCliente($pedido->id_cliente)->detalle()->nombre}}
                                        <br>
                                        <strong>
                                            ${{number_format(getPedido($pedido->id_pedido)->getPrecio(), 2)}}
                                        </strong>
                                        @php
                                            $valor_total += getPedido($pedido->id_pedido)->getPrecio();
                                        @endphp
                                        @if($facturado!=null)
                                            <br />
                                            <span class="badge bg-green" style="margin-top:8px;font-size: 13px;">
                                                <i class="fa fa-check-circle-o" aria-hidden="true"></i> Facturado
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                @if($pos_det_esp == 0 && $pos_esp_emp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"
                                        id="td_datos_exportacion_{{$pedido->id_pedido}}"
                                        rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)}}">
                                        @if(count(getDatosExportacionCliente($det_ped->id_detalle_pedido))>0)
                                            <ul style="padding: 0;margin-bottom: 0">
                                                @foreach(getDatosExportacionCliente($det_ped->id_detalle_pedido) as $de)
                                                    <li style="list-style: none">{{--<b>{{strtoupper($de->nombre)}}:</b>--}} {{$de->valor}} </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp->variedad->siglas}}
                                    {{explode('|',$det_esp->clasificacion_ramo->nombre)[0]}}{{$det_esp->clasificacion_ramo->unidad_medida->siglas}}
                                </td>
                                @if($pos_det_esp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                        {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                    </td>
                                @endif
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{--{{explode('|',$det_esp->empaque_e->nombre)[0]}}--}}
                                    {{explode('|',$det_esp->empaque_p->nombre)[0]}}
                                </td>

                                    @if($pos_det_esp == 0 && $pos_esp_emp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)}}">
                                        {{$esp_emp->cantidad * $det_ped->cantidad}}
                                        @php
                                            $piezas_totales += ($esp_emp->cantidad * $det_ped->cantidad);
                                        @endphp
                                    </td>
                                @endif
                                @if($pos_det_esp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                        {{($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1]}}
                                        @php
                                            $cajas_full_totales += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1];
                                        @endphp
                                    </td>
                                @endif
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad}}
                                    @php
                                        $ramos_totales += $det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad;
                                        $ramos_totales_estandar += convertToEstandar($det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad, $det_esp->clasificacion_ramo->nombre);

                                    if (!in_array($det_esp->id_variedad, $variedades)){
                                        array_push($variedades, $det_esp->id_variedad);
                                    }
                                    array_push($ramos_x_variedades, [
                                        'id_variedad' => $det_esp->id_variedad,
                                        'cantidad' => convertToEstandar($det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad, $det_esp->clasificacion_ramo->nombre),
                                    ]);
                                    @endphp
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp->cantidad}}
                                </td>
                                @if($opciones && $pos_det_esp == 0 && $pos_esp_emp == 0 && $pos_det_ped == 0)
                                    <td style="border-color: #9d9d9d" class="text-center "
                                        rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                        {{getAgenciaCarga($det_ped->id_agencia_carga)->nombre}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d" id="td_opciones_{{$pedido->id_pedido}}"
                                        rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                        @if($facturado==null)
                                            @if($pedido->empaquetado == 0)
                                                <button class="btn  btn-{!! $det_ped->estado == 1 ? 'danger' : 'success' !!} btn-xs"
                                                        type="button"  title="{!! $det_ped->estado == 1 ? 'Cancelar pedido' : 'Activar pedido' !!}"
                                                        id="edit_pedidos" onclick="cancelar_pedidos('{{$pedido->id_pedido}}','','{{$det_ped->estado}}','{{@csrf_token()}}')">
                                                        <i class="fa fa-{!! $det_ped->estado == 1 ? 'trash' : 'undo' !!}" aria-hidden="true"></i>
                                                </button>
                                            @if($pedido->tipo_especificacion == 'T')
                                                <button type="button" class="btn btn-default btn-xs" title="Editar pedido"
                                                        onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', 0)">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-default btn-xs" title="Editar pedido"
                                                        onclick="editar_pedido('{{$pedido->id_cliente}}','{{$pedido->id_pedido}}')">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                            @if(getPedido($pedido->id_pedido)->haveDistribucion() == 1)
                                                    <button type="button" class="btn btn-xs btn-info" title="Distribuir"
                                                            onclick="distribuir_orden_semanal('{{$pedido->id_pedido}}')">
                                                        <i class="fa fa-fw fa-gift"></i>
                                                    </button>
                                                @elseif(getPedido($pedido->id_pedido)->haveDistribucion() == 2)
                                                    <button type="button" class="btn btn-xs btn-info" title="Ver distribución"
                                                            onclick="ver_distribucion_orden_semanal('{{$pedido->id_pedido}}')">
                                                        <i class="fa fa-fw fa-gift"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-primary btn-xs" title="Duplicar pedido"
                                                        onclick="duplicar_pedido('{{$pedido->id_pedido}}','{{$pedido->id_cliente}}')">
                                                    <i class="fa fa-files-o" aria-hidden="true"></i>
                                                </button>

                                            @endif
                                                <button type="button" class="btn btn-success btn-xs" title="Facturar pedido"
                                                        onclick="facturar_pedido('{{$pedido->id_pedido}}')">
                                                    <i class="fa fa-usd" aria-hidden="true"></i>
                                                </button>
                                            @else
                                            <a href="#" class="btn btn-default btn-xs" title="Ver factura"
                                               onclick="ver_factura('{{$pedido->id_pedido}}')">
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                            <a target="_blank" href="{{url('pedidos/crear_packing_list',$pedido->id_pedido)}}" class="btn btn-info btn-xs" title="Generar packing list">
                                                <i class="fa fa-cubes" ></i>
                                            </a>

                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </table>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-4">
                <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" colspan="2">
                            TOTALES RAMOS POR VARIEDAD
                        </th>
                    </tr>
                    @foreach($ramos_x_variedad as $item)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{getVariedad($item->id_variedad)->siglas}}
                                {{explode('|',getCalibreRamoById($item->id_clasificacion_ramo)->nombre)[0]}}{{getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                                @if($item->tallos_x_ramos != '')
                                    {{$item->tallos_x_ramos}} tallos
                                @endif
                                @if($item->longitud_ramo != '')
                                    {{$item->longitud_ramo}} {{getUnidadMedida($item->id_unidad_medida)->siglas}}
                                @endif
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$item->cantidad}}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Ramos Totales Pedidos
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$ramos_totales}}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" colspan="2">
                            CAJAS EQUIVALENTES
                        </th>
                    </tr>
                    @php
                        $cajas_equivalentes = [];
                        foreach ($variedades as $variedad){
                            $cantidad = 0;
                            foreach($ramos_x_variedades as $ramos){
                                if($ramos['id_variedad'] == $variedad){
                                    $cantidad += $ramos['cantidad'];
                                }
                            }
                            array_push($cajas_equivalentes, [
                                'id_variedad' => $variedad,
                                'cantidad' => round($cantidad / getConfiguracionEmpresa()->ramos_x_caja, 2),
                            ]);
                        }
                    @endphp
                    @foreach($cajas_equivalentes as $item)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{getVariedad($item['id_variedad'])->nombre}}
                                ({{getVariedad($item['id_variedad'])->siglas}})
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$item['cantidad']}}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Cajas Equivalentes Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{round($ramos_totales_estandar / getConfiguracionEmpresa()->ramos_x_caja,2)}}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Piezas Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$piezas_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Ramos Totales Pedidos
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$ramos_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Cajas Full Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$cajas_full_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Cajas Equivalentes Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{round($ramos_totales_estandar / getConfiguracionEmpresa()->ramos_x_caja,2)}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Valor Total
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            ${{number_format($valor_total,2)}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado pedidos para esta fecha</div>
    @endif
</div>
