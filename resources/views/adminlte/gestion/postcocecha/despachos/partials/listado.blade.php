<div id="table_despachos">
    @if(count($listado)>0)
        <div style="width: 100%;overflow-x:auto">
            <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d;overflow-x: auto"
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
                    <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-right" colspan="{{$opciones ? "11" : "12"}}">
                        @if(!$opciones)
                            <button type="button" class="btn btn-xs btn-primary" onclick="ver_despachos()">
                                <i class="fa fa-eye" aria-hidden="true"></i> Ver despachos
                            </button>
                            <button type="button" class="btn btn-xs btn-primary" onclick="crear_despacho()">
                                <i class="fa fa-truck" aria-hidden="true"></i> Crear despacho
                            </button>
                        @endif
                        <button type="button" class="btn btn-xs btn-primary" onclick="exportar_listado_cuarto_frio('{{csrf_token()}}')">
                            <i class="fa fa-fw fa-file-excel-o"></i> Exportar a Excel Cuarto Frio
                        </button>
                        @if($opciones)
                            <button type="button" class="btn btn-xs btn-success"
                                    onclick="exportar_listado_despacho('{{csrf_token()}}',document.getElementById('id_configuracion_pedido').value)">
                                <i class="fa fa-fw fa-file-excel-o"></i> Exportar a Excel
                            </button>
                        @else
                            <button type="button" class="btn btn-xs btn-success"
                                    onclick="exportar_excel_listado_despacho('{{csrf_token()}}',document.getElementById('id_configuracion_empresa_despacho').value)">
                                <i class="fa fa-fw fa-file-excel-o"></i> Exportar a Excel
                            </button>
                        @endif
                    </th>
                </tr>
                <tr>
                    @if(!$opciones)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;width:50px">
                            ORDEN PEDIDO
                        </th>
                    @endif
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        CLIENTE
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;width:50px">
                        FACTURA N°
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        MARACACIONES
                    </th>
                    @if($opciones)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            FLOR
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            EMPAQUE
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            PRESENTACIÓN
                        </th>
                    @endif
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        PIEZAS
                    </th>
                    @if($opciones)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            CAJAS FULL
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            RAMOS
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            RAMOS x CAJA
                        </th>
                    @else
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            CAJAS FULL
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            HALF
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            CUARTOS
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            OCTAVOS
                        </th>
                    @endif
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        @if($opciones)
                            CUARTO FRÍO
                        @else
                            AGENCIA DE CARGA
                        @endif
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        FACTURADO POR:
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;width:220px">
                        OPCIONES
                    </th>
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
                @foreach($listado as $x=> $pedido)
                    @php
                        $listar = true;
                        if(!$opciones && getFacturaAnulada($pedido->id_pedido))
                            $listar = false;
                    @endphp
                    @if($listar)
                        @php
                            $despachado = getCantDespacho($pedido->id_pedido) ;
                            $ped = getPedido($pedido->id_pedido);
                            $getCantidadDetallesEspecificacionByPedido = getCantidadDetallesEspecificacionByPedido($pedido->id_pedido);
                            if(isset($ped->envios[0]->id_envio)){
                                $firmado = getFacturado($ped->envios[0]->id_envio,1);
                                $facturado = getFacturado($ped->envios[0]->id_envio,5);
                            }else{
                                $firmado = null;
                                $facturado = null;
                            }
                            $full = 0;
                            $half = 0;
                            $cuarto = 0;
                            $sexto = 0;
                            $octavo = 0;
                        @endphp
                        @foreach($ped->detalles as $pos_det_ped => $det_ped)
                            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                                @php
                                    $full += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                                    switch (explode("|",$esp_emp->empaque->nombre)[1]){
                                        case '0.5':
                                            $half += $det_ped->cantidad;
                                            break;
                                        case '0.25':
                                            $cuarto +=$det_ped->cantidad;
                                            break;
                                        case '0.17':
                                            $sexto +=$det_ped->cantidad;
                                            break;
                                        case '0.125':
                                            $octavo +=$det_ped->cantidad;
                                            break;
                                    }
                                    $piezas_despacho = $half+$cuarto+$sexto+$octavo;
                                @endphp
                            @endforeach
                        @endforeach
                        @foreach($ped->detalles as $pos_det_ped => $det_ped)
                            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                                @php
                                    if(!getFacturaAnulada($pedido->id_pedido))
                                        $piezas_totales += ($esp_emp->cantidad * $det_ped->cantidad);
                                    $getCantidadDetallesByEspecificacion = getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion);
                                @endphp
                                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                    <tr style="background-color: {{!in_array($det_esp->id_variedad,explode('|',$pedido->variedad)) ? '#b9ffb4' : ''}}; border-bottom: 2px solid #9d9d9d"
                                        title="{{!in_array($det_esp->id_variedad,explode('|',$pedido->variedad)) ? 'Confirmado' : 'Por confirmar'}}">
                                        @if($pos_det_esp == 0 && $pos_esp_emp == 0 && $pos_det_ped == 0)
                                            @if(!$opciones)
                                                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                    @if($despachado > 0)
                                                        <i class="fa fa-2x fa-check-circle text-success" title="Despachado"
                                                           aria-hidden="true"></i>
                                                    @else
                                                        <input type="number" name="orden_despacho" id="{{$pedido->id_pedido}}"
                                                               class="form-control orden_despacho id_configuracion_empresa_{{isset($ped->id_configuracion_empresa) ? $ped->id_configuracion_empresa : ""}}"
                                                               min="1"
                                                               style="width: 56px;border:none;text-align: center" {{isset($id_configuracion_empresa) ? "" : "disabled"}}>
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="text-center" style="border-color: #9d9d9d"
                                                rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                {{getCliente($pedido->id_cliente)->detalle()->nombre}}
                                                @php
                                                    $precio_pedido = $ped->getPrecioByPedido();
                                                @endphp
                                                @if($opciones)
                                                    <br>
                                                    <strong>
                                                        ${{number_format($precio_pedido, 2)}}
                                                    </strong>
                                                    {{--@php
                                                        $sum_precios = 0;
                                                    @endphp
                                                    @foreach($ped->detalles as $det)
                                                        @php
                                                            $parcial = $det->total_tallos();
                                                            $sum_precios += $parcial;
                                                        @endphp
                                                        @if($parcial > 0)
                                                            <br>
                                                            <small>
                                                                <em>
                                                                    {{$parcial}}
                                                                </em>
                                                            </small>
                                                        @endif
                                                    @endforeach
                                                    <br>
                                                    <small>{{number_format($sum_precios, 2)}}</small>--}}
                                                @endif
                                                @php
                                                    if(!getFacturaAnulada($pedido->id_pedido))
                                                        $valor_total += $precio_pedido;
                                                @endphp
                                                @if($facturado != null)
                                                    <br/>
                                                    <span class="badge bg-green" style="margin-top:8px;font-size: 13px;">
                                                        <i class="fa fa-check-circle-o" aria-hidden="true"></i> Facturado
                                                    </span>
                                                @endif
                                                @if(isset($ped->envios[0]->comprobante) && $ped->envios[0]->comprobante->estado == 6)
                                                    <br/>
                                                    <span class="badge bg-red" style="margin-top:8px;font-size: 13px;">
                                                        <i class="fa fa-times" aria-hidden="true"></i> Anulado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center" style="border-color: #9d9d9d"
                                                rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                <span style="padding:0px 5px"><b>{{isset($ped->envios[0]->comprobante) ? $ped->envios[0]->comprobante->secuencial : ""}}</b></span>
                                            </td>
                                        @endif
                                        @if($pos_det_esp == 0 && $pos_esp_emp == 0)
                                            <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"
                                                id="td_datos_exportacion_{{$pedido->id_pedido}}"
                                                rowspan="{{$getCantidadDetallesByEspecificacion}}">
                                                @if(count(getDatosExportacionCliente($det_ped->id_detalle_pedido))>0)
                                                    <ul style="padding: 0;margin-bottom: 0">
                                                        @foreach(getDatosExportacionCliente($det_ped->id_detalle_pedido) as $de)
                                                            <li style="list-style: none">{{--<b>{{strtoupper($de->nombre)}}:</b>--}} {{$de->valor}} </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                        @endif
                                        @if($opciones)
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
                                        @endif
                                        @if($opciones)
                                            @if($pos_det_esp == 0 && $pos_esp_emp == 0)
                                                <td class="text-center" style="border-color: #9d9d9d"
                                                    rowspan="{{$getCantidadDetallesByEspecificacion}}">
                                                    {{$esp_emp->cantidad * $det_ped->cantidad}}
                                                </td>
                                                @php
                                                    if(!getFacturaAnulada($pedido->id_pedido))
                                                        $cajas_full_totales += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1];
                                                @endphp
                                            @endif
                                        @else
                                            @if($pos_det_esp == 0 && $pos_esp_emp == 0 && $pos_det_ped == 0)
                                                <td class="text-center" style="border-color: #9d9d9d"
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                    {{$piezas_despacho}}
                                                </td>
                                                @php
                                                    if(!getFacturaAnulada($pedido->id_pedido))
                                                   $cajas_full_totales += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1];
                                                @endphp
                                            @endif
                                        @endif
                                        @php
                                            $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp->id_detalle_especificacionempaque);
                                            if(!getFacturaAnulada($pedido->id_pedido)){
                                                $ramos_totales += (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad;
                                                $ramos_totales_estandar += convertToEstandar((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad, $det_esp->clasificacion_ramo->nombre);
                                                if (!in_array($det_esp->id_variedad, $variedades)){
                                                    array_push($variedades, $det_esp->id_variedad);
                                                }

                                                array_push($ramos_x_variedades, [
                                                    'id_variedad' => $det_esp->id_variedad,
                                                    'cantidad' => convertToEstandar((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad, $det_esp->clasificacion_ramo->nombre),
                                                ]);
                                            }
                                        @endphp
                                        @if($opciones)
                                            @if($pos_det_esp == 0)
                                                <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                                    {{($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1]}}
                                                </td>
                                            @endif
                                            <td class="text-center" style="border-color: #9d9d9d">
                                                {{(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad}}
                                            </td>
                                            <td class="text-center" style="border-color: #9d9d9d">
                                                {{isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad}}
                                            </td>
                                        @else
                                            @if($pos_det_esp == 0 && $pos_esp_emp == 0 && $pos_det_ped == 0)
                                                <td class="text-center" style="border-color: #9d9d9d"
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">{{$full}}</td>
                                                <td class="text-center" style="border-color: #9d9d9d"
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">{{$half}}</td>
                                                <td class="text-center" style="border-color: #9d9d9d"
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">{{$cuarto}}</td>
                                                <td class="text-center" style="border-color: #9d9d9d"
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">{{$octavo}}</td>
                                            @endif
                                        @endif
                                        @if($pos_det_esp == 0 && $pos_esp_emp == 0 && $pos_det_ped == 0)
                                            <td style="border-color: #9d9d9d" class="text-center "
                                                rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                {{getAgenciaCarga($det_ped->id_agencia_carga)->nombre}}
                                            </td>
                                            @if($opciones)
                                                <td style="border-color: #9d9d9d" class="text-center "
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                    {{isset($ped->empresa->razon_social) ? $ped->empresa->razon_social : ""}}
                                                </td>
                                                <td class="text-center" style="border-color: #9d9d9d" id="td_opciones_{{$pedido->id_pedido}}"
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                    @if($pedido->tipo_especificacion == 'T')
                                                        <button type="button" class="btn btn-default btn-xs"
                                                                title="{{(isset($ped->envios[0]->comprobante) && $ped->envios[0]->comprobante->estado != 1 ) ? "Ver pedido" : "Editar pedido"}}"
                                                                onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', 0)">
                                                            @if((isset($ped->envios[0]->comprobante) && $ped->envios[0]->comprobante->estado != 1 ))
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                            @endif
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-default btn-xs"
                                                                title="{{(isset($ped->envios[0]->comprobante) && $ped->envios[0]->comprobante->estado != 1 ) ? "Ver pedido" : "Editar pedido"}}"
                                                                onclick="editar_pedido('{{$pedido->id_cliente}}','{{$pedido->id_pedido}}',
                                                                        '{{(isset($ped->envios[0]->comprobante) && $ped->envios[0]->comprobante->ficticio) ? $ped->envios[0]->comprobante->secuencial : ''}}',
                                                                        '{{(isset($ped->envios[0]->comprobante) && !$ped->envios[0]->comprobante->ficticio) ? $ped->envios[0]->comprobante->secuencial : ''}}')">
                                                            @if((isset($ped->envios[0]->comprobante) && $ped->envios[0]->comprobante->estado != 1 ))
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                            @endif
                                                        </button>
                                                    @endif
                                                    <a target="_blank" class="btn btn btn-success btn-xs"
                                                       href="{{url('pedidos/desglose_pedido',[$ped->id_pedido])}}"
                                                       title="Ver desglose del pedido"><i class="fa fa-file-text-o"></i>
                                                    </a>
                                                    @if((isset($ped->envios[0]->comprobante) && $ped->envios[0]->comprobante->estado != 6) || !isset($ped->envios[0]->comprobante))
                                                        @if($facturado==null)
                                                            <button class="btn  btn-{!! $det_ped->estado == 1 ? 'danger' : 'success' !!} btn-xs"
                                                                    type="button"
                                                                    title="{!! $det_ped->estado == 1 ? 'Cancelar pedido' : 'Activar pedido' !!}"
                                                                    id="edit_pedidos"
                                                                    onclick="cancelar_pedidos('{{$pedido->id_pedido}}','','{{$det_ped->estado}}','{{@csrf_token()}}')">
                                                                <i class="fa fa-{!! $det_ped->estado == 1 ? 'trash' : 'undo' !!}"
                                                                   aria-hidden="true"></i>
                                                            </button>
                                                            @if($pedido->empaquetado == 0)
                                                                @if($ped->haveDistribucion() == 1)
                                                                    <button type="button" class="btn btn-xs btn-info" title="Distribuir"
                                                                            onclick="distribuir_orden_semanal('{{$pedido->id_pedido}}')">
                                                                        <i class="fa fa-fw fa-gift"></i>
                                                                    </button>
                                                                @elseif($ped->haveDistribucion() == 2)
                                                                    <button onclick="ver_distribucion_orden_semanal('{{$pedido->id_pedido}}')"
                                                                            class="btn btn-default text-left"
                                                                            style="cursor:pointer;padding:5px 3px;width:100%;">
                                                                        <em> Ver distribución</em>
                                                                    </button>
                                                                @endif
                                                                <button onclick="duplicar_pedido('{{$pedido->id_pedido}}','{{$pedido->id_cliente}}')"
                                                                        class="btn btn-primary  btn-xs " title="Duplicar pedido">
                                                                    <i class="fa fa-files-o" aria-hidden="true"></i>
                                                                </button>
                                                                <button onclick="empaquetar_pedido('{{$pedido->id_pedido}}','{{csrf_token()}}')"
                                                                        class="btn btn-warning  btn-xs " title="Empaquetar pedido">
                                                                    <i class="fa fa-cube"></i>
                                                                </button>
                                                            @endif
                                                            @if(!isset($ped->envios[0]->comprobante) || (isset($ped->envios[0]->comprobante) && !$ped->envios[0]->comprobante->ficticio))
                                                                <button onclick="facturar_pedido('{{$pedido->id_pedido}}')"
                                                                        class="btn btn-success btn-xs" title="Generar factura">
                                                                    <i class="fa fa-usd" aria-hidden="true"></i>
                                                                </button>
                                                            @endif

                                                            <button class="btn btn-danger btn-xs" title="Modificar Comprobante"
                                                                    onclick="modificar_comprobante('{{$pedido->id_pedido}}')">
                                                                <i class="fa fa-fw fa-exclamation-triangle"></i>
                                                            </button>

                                                        @else
                                                            {{--<a target="_blank" href="{{url('pedidos/ver_factura_pedido',$pedido->id_pedido)}}" class="btn btn-default btn-xs" title="Ver factura SRI">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            </a>--}}
                                                            <a target="_blank"
                                                               href="{{url('pedidos/documento_pre_factura',[$pedido->id_pedido,true])}}"
                                                               class="btn btn-info btn-xs" title="Ver factura Cliente">
                                                                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                        @if($firmado != null)
                                                            <a target="_blank"
                                                               href="{{url('pedidos/documento_pre_factura',[$pedido->id_pedido,true])}}"
                                                               class="btn btn-info btn-xs" title="Ver factura Cliente">
                                                                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                                            </a>
                                                        @endif
                                                        <a target="_blank" href="{{url('pedidos/crear_packing_list',$pedido->id_pedido)}}"
                                                           class="btn btn-info btn-xs" title="Generar packing list">
                                                            <i class="fa fa-cubes"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
                                            @if(!$opciones)
                                                <td style="border-color: #9d9d9d" class="text-center "
                                                    rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                                    {{isset($ped->empresa->razon_social) ? $ped->empresa->nombre : ""}}
                                                </td>
                                            @endif
                                            <td rowspan="{{$getCantidadDetallesEspecificacionByPedido}}" class="text-center"
                                                style="border-color: #9d9d9d">
                                                @if(!$opciones && $pedido->tipo_especificacion === "T")
                                                    <a target="_blank" href="{{url('pedidos/crear_packing_list',[$pedido->id_pedido,true])}}"
                                                       class="btn btn-info btn-xs" title="Packing list">
                                                        <i class="fa fa-cubes"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endif
                @endforeach
            </table>
            <div class="row" style="margin-top: 10px">
            </div>
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
                                {{number_format($item->cantidad,2,".","")}}
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
                    @if($opciones)
                        <tr>
                            <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                                Valor Total
                            </th>
                            <td class="text-center" style="border-color: #9d9d9d">
                                ${{number_format($valor_total,2)}}
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado pedidos para esta fecha</div>
    @endif
</div>
