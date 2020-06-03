    <div id="table_comprobante">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_comprobante">
            <thead>
                <tr style="background-color: #dd4b39; color: white">
                    {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        @if($tipo_comprobante == ""  || $tipo_comprobante=="01")
                            ENVÍO
                        @elseif($tipo_comprobante == ""  || $tipo_comprobante=="06")
                            GUÍA DE REMISIÓN
                        @endif
                    </th>--}}
                    @if($tipo_comprobante=="06")
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            FACTURA ATADA
                        </th>
                    @endif
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        COMPROBANTE
                    </th>
                    @if($tipo_comprobante=="01")
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            FACTURADO POR
                        </th>
                    @endif
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        CLAVE DE ACCESO
                    </th>
                    @if($tipo_comprobante!="06")
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            CLIENTE
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            DINERO
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            CAJAS FULL
                        </th>
                    @endif
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                       AGENCIA DE CARAGA
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        FECHA
                    </th>
                    {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        ESTADO
                    </th>--}}
                    @if($columna_causa)
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            CAUSA
                        </th>
                    @endif
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        OPCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
            @php $total_cajas_full = 0; $monto_total  =0; @endphp
            @foreach($listado as $key => $item)
                @php $cajas_full = 0; @endphp
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    {{$item->estado== 6 ? 'style=color:red' : ""}} id="row_comprobante_{{$item->id_comprobante}}">
                    {{--<td style="border-color: #9d9d9d" class="text-center">
                        @if($tipo_comprobante == ""  || $tipo_comprobante=="01")
                            ENV{{str_pad($item->id_envio,9,"0",STR_PAD_LEFT)}}
                        @elseif($tipo_comprobante == ""  || $tipo_comprobante=="06")
                            {{getDetallesClaveAcceso($item->clave_acceso, 'SERIE').getDetallesClaveAcceso($item->clave_acceso, 'SECUENCIAL')}}
                        @endif
                    </td>--}}
                    @if($tipo_comprobante=="06")
                        <td style="border-color: #9d9d9d" class="text-center">
                            {{getComprobante(getComprobanteRelacionadoGuia($item->id_comprobante)->id_comprobante_relacionado)->numero_comprobante}}
                        </td>
                    @endif
                        <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre_comprobante}} - {{$item->secuencial}}  {{-- {{$item->nombre_comprobante." N# "."001-".{{--getDetallesClaveAcceso($item->clave_acceso, 'PUNTO_ACCESO')."-".getDetallesClaveAcceso($item->clave_acceso, 'SECUENCIAL')}} COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE --}}</td>
                    @if($tipo_comprobante=="01")
                        <td style="border-color: #9d9d9d" class="text-center">
                           {{$item->envio->pedido->empresa->razon_social}}
                        </td>
                    @endif
                        <td style="border-color: #9d9d9d" class="text-center"> {{$item->clave_acceso}} </td>
                    @if($tipo_comprobante!="06")
                        <td style="border-color: #9d9d9d" class="text-center"> {{$item->nombre_cliente}}</td>
                        <td style="border-color: #9d9d9d" class="text-center">
                            ${{number_format($item->monto_total,2,".","")}}
                            @php $monto_total += $item->monto_total @endphp
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center">
                            @php
                                foreach($item->envio->pedido->detalles as $det_ped){
                                    foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp){
                                        $total_cajas_full +=($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1];
                                        $cajas_full +=  ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1];
                                    }
                                }
                            @endphp
                            {{$cajas_full}}
                        </td>
                    @endif
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->envio->pedido->detalles[0]->agencia_carga->nombre}} </td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->fecha_emision}} </td>
                    {{--<td style="border-color: #9d9d9d" class="text-center">
                        @if($item->estado == 0)
                            No firmado
                        @elseif($item->estado == 1)
                            Generado
                        @elseif($item->estado == 2)
                            Enviado al SRI
                        @elseif($item->estado == 3)
                            Devuelto por el SRI
                        @elseif($item->estado == 4)
                            No autoriazo por el SRI
                        @elseif($item->estado == 5)
                            Enviado al SRI
                        @elseif($item->estado == 6)
                            Anulada
                        @elseif($item->estado == 00)
                            Lote
                        @endif
                    </td>--}}
                    @if($columna_causa)
                        <td style="border-color: #9d9d9d;" class="text-center"> {{!empty($item->causa) ? $item->causa : "-"}} </td>
                    @endif
                    <td style="border-color: #9d9d9d;width:150px" class="text-center">
                        @if($item->estado==5)
                            <a target="_blank" href="{{url('comprobante/comprobante_aprobado_sri',$item->clave_acceso)}}" class="btn btn-info btn-xs" title="Ver factura" >
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            @if($tipo_comprobante=="01" && getComprobanteRelacionadFactura($item->id_comprobante) == null)
                                @if(getCantDespacho(getComprobante($item->id_comprobante)->envio->pedido->id_pedido)>0)
                                    {{-- COMENTADO PARA QUE LA FACTURACION  FUNCIONE CON EL VENTURE
                                    <button class="btn btn-success btn-xs" title="Crear Guía de Remisión" onclick="crear_guia_remision('{{$item->id_comprobante}}')">
                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                    </button>--}}
                                    <button class="btn btn-success btn-xs" title="Crear Guía de Remisión" onclick="crear_guia_remision_factura('{{$item->id_comprobante}}')">
                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                    </button>
                                @endif
                            @endif
                            @if($tipo_comprobante!="06")
                                {{-- COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
                                <a target="_blank" href="{{url('comprobante/pre_factura',[$item->clave_acceso,true])}}" class="btn btn-info btn-xs" title="Ver factura Cliente">
                                    <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                </a>
                                <button class="btn btn-warning btn-xs" title="Reenviar correo" onclick="reenviar_correo('{{$item->clave_acceso}}')">
                                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                </button>--}}

                                    <a target="_blank" href="{{url('comprobante/documento_pre_factura',[$item->secuencial,true])}}" class="btn btn-info btn-xs" title="Ver factura Cliente">
                                        <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                    </a>

                                @if($item->esatdo == 1)
                                    <a target="_blank" href="{{url('comprobante/documento_pre_factura',$item->secuencial)}}" class="btn btn-primary btn-xs" title="Ver factura SRI">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                @endif
                                <button class="btn btn-danger btn-xs" title="Anular factura" onclick="anular_factura('{{$item->id_comprobante}}')">
                                    <i class="fa fa-times" ></i>
                                </button>
                            @else
                                <a target="_blank" href="{{url('comprobante/guia_remision',$item->secuencial)}}" class="btn btn-primary btn-xs" title="Ver guía de remisión">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                            @endif
                        @endif
                        @if($item->estado == 1)
                            @if($item->integrado)
                                <button class="btn btn-warning btn-xs" title="Desvincular del venture" onclick="update_integrado('{{$item->id_comprobante}}')">
                                    <i class="fa fa-ban"></i>
                                </button>
                            @else
                                @if(!$item->ficticio)
                                    <button class="btn btn-default btn-xs" title="Integrar factura" onclick="integrar_factura_venture('{{$item->id_comprobante}}')">
                                        <i class="fa fa-calendar-check-o" ></i>
                                    </button>
                                @endif
                            @endif

                            {{--<button class="btn btn-default btn-xs">
                                <input type="checkbox" id="facturar_{{$key+1}}" name="enviar" {{$item->integrado ? "disabled" : "" }}  title="Enviar al SRI" value="{{$item->clave_acceso}}" style="margin:0;position:relative;top:3px">
                            </button>--}}
                            @if($tipo_comprobante!="06")
                                {{--<a target="_blank" href="{{url('comprobante/pre_factura',[$item->clave_acceso,true])}}" class="btn btn-info btn-xs" title="Ver factura Cliente"> COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE --}}
                                <a target="_blank" href="{{url('comprobante/documento_pre_factura',[$item->secuencial,true])}}" class="btn btn-info btn-xs" title="Ver factura Cliente">
                                    <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                </a>
                                {{--<a target="_blank" href="{{url('comprobante/pre_factura',$item->clave_acceso)}}" class="btn btn-primary btn-xs" title="Ver factura SRI"> COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE --}}
                                <a target="_blank" href="{{url('comprobante/documento_pre_factura',$item->secuencial)}}" class="btn btn-primary btn-xs" title="Ver factura SRI">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                            @else
                                {{--COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
                                <a target="_blank" href="{{url('comprobante/pre_guia_remision',$item->clave_acceso)}}" class="btn btn-primary btn-xs" title="Ver comprobante electrónico">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>--}}
                                <a target="_blank" href="{{url('comprobante/guia_remision',$item->secuencial)}}" class="btn btn-primary btn-xs" title="Ver guía de remisión">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                            @endif
                        @elseif($item->estado == 0)
                            <button class="btn btn-default btn-xs">
                                <input type="checkbox" id="firmar_{{$key+1}}" name="firmar" value="{{$item->id_comprobante}}" style="margin:0;position:relative;top:3px">
                            </button>
                        @endif
                        {{--PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE--}}
                        @if($item->estado != 6)
                            <button class="btn btn-success btn-xs" title="Enviar correo al cliente"
                             onclick="enviar_correo(
                                    '{{$item->id_comprobante}}',
                                    '{{$item->envio->pedido->tipo_especificacion}}',
                                    '{{$item->tipo_comprobante}}',1,
                                    '{{$item->envio->pedido->cliente->fc}}',
                                    '{{$item->envio->pedido->cliente->csv}}',
                                    '{{$item->envio->pedido->cliente->le}}',
                                    '{{$item->envio->pedido->cliente->dc}}',
                                    {{$item->envio->pedido->cliente->fc_sri}})">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-warning btn-xs" title="Enviar correo a la agencia de carga"
                             onclick="enviar_correo('{{$item->id_comprobante}}','{{isset($item->envio->pedido->tipo_especificacion) ? $item->envio->pedido->tipo_especificacion : null}}','{{$item->tipo_comprobante}}',0)">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3"></td>
                <td class="text-right"><b>TOTALES: </b></td>
                <td class="text-center"><b>${{number_format($monto_total,2,".","")}}</b></td>
                <td class="text-center"><b>{{$total_cajas_full}}</b></td>
                <td colspan="2"></td>
            </tr>
            </tbody>
        </table>
        @if($item->estado !=5 && $item->estado != 3 && $item->estado != 4)
            @if($item->estado == 0)
                <div class="text-center">
                    <button class="btn btn-success" onclick="firmar_comprobante()">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                        Firmar comprobante
                    </button>
                </div>
            @elseif($item->estado == 1)
                <div class="text-center">
                    {{--COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL SRI--}}
                    {{--<button class="btn btn-success" onclick="enviar_comprobante('{{$tipo_comprobante}}','{{$item->envio->pedido->id_configuracion_empresa}}')">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                        Enviar al SRI
                    </button>--}}
                    <button class="btn btn-success" title="Descargar txt" onclick="txt_venture()">
                        <i class="fa fa-download"></i>
                        Descargar archivo txt
                    </button>
                    <button class="btn btn-info" onclick="actualizar_comprobante()">
                        <i class="fa fa-refresh" ></i>
                        Actualizar comprobantes integrados
                    </button>
                </div>
            @endif
        @endif
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
