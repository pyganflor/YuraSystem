<div id="table_comprobante">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_comprobante">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    @if($tipo_comprobante == ""  || $tipo_comprobante=="01")
                        ENVÍO
                    @elseif($tipo_comprobante == ""  || $tipo_comprobante=="06")
                        GUÍA DE REMISIÓN
                    @endif
                </th>
                @if($tipo_comprobante=="06")
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        FACTURA ATADA
                    </th>
                @endif
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    COMPROBANTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CLAVE DE ACCESO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CLIENTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    TOTAL
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    EMISIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ESTADO
                </th>
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
            @foreach($listado as $key => $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                     id="row_comprobante_{{$item->id_comprobante}}">
                    <td style="border-color: #9d9d9d" class="text-center">
                        @if($tipo_comprobante == ""  || $tipo_comprobante=="01")
                            ENV{{str_pad($item->id_envio,9,"0",STR_PAD_LEFT)}}
                        @elseif($tipo_comprobante == ""  || $tipo_comprobante=="06")
                            {{getDetallesClaveAcceso($item->clave_acceso, 'SERIE').getDetallesClaveAcceso($item->clave_acceso, 'SECUENCIAL')}}
                        @endif
                    </td>
                    @if($tipo_comprobante=="06")
                        <td style="border-color: #9d9d9d" class="text-center"> {{getComprobante($item->id_comprobante)->numero_comprobante}} </td>
                    @endif
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->nombre_comprobante}} </td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->clave_acceso}} </td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->nombre_cliente}}</td>
                    <td style="border-color: #9d9d9d" class="text-center"> ${{number_format($item->monto_total,2,".","")}}</td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{Carbon\Carbon::parse($item->fecha_emision)->format('d-m-Y')}} </td>
                    <td style="border-color: #9d9d9d" class="text-center">
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
                            Facturado
                        @elseif($item->estado == 00)
                            Lote
                        @endif
                    </td>
                    @if($columna_causa)
                    <td style="border-color: #9d9d9d" class="text-center"> {{!empty($item->causa) ? $item->causa : "-"}} </td>
                    @endif
                    <td style="border-color: #9d9d9d" class="text-center">
                        @if($item->estado==5)
                            <a target="_blank" href="{{url('comprobante/factura_aprobada_sri',$item->clave_acceso)}}" class="btn btn-info btn-xs" title="Ver factura" >
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            @if(getCantDespacho(getComprobante($item->id_comprobante)->envio->pedido->id_pedido)>0)
                                <button class="btn btn-success btn-xs" title="Crear Guía de Remisión" onclick="crear_guia_remision('{{$item->id_comprobante}}')">
                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                </button>
                            @endif
                            <button class="btn btn-warning btn-xs" title="Reenviar correo" onclick="reenviar_correo('{{$item->clave_acceso}}')">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if($item->estado == 1)
                            <button class="btn btn-default btn-xs">
                                <input type="checkbox" id="facturar_{{$key+1}}" name="facturar"  title="{{$item->nombre_comprobante=="FACTURA" ? "Facturar" : "Enviar al SRI"}}" value="{{$item->clave_acceso}}" style="margin:0;position:relative;top:3px">
                            </button>
                            <a target="_blank" href="{{url('comprobante/pre_factura',$item->clave_acceso)}}" class="btn btn-primary btn-xs" title="Ver comprobante elctrónico">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            {{--<button class="btn btn-danger btn-xs" title="Eliminar comprobante elctrónico" onclick="elimnar_comprobante()">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>--}}
                        @endif
                        {{--@if($item->estado == 1 || $item->estado == 3 || $item->estado == 4)
                            <button class="btn btn-warning btn-xs" title="Cancelar pre factura" onclick="cancelar_factura('{{$item->id_comprobante}}')">
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </button>
                        @endif--}}
                        @if($item->estado == 0)
                            <button class="btn btn-default btn-xs">
                                <input type="checkbox" id="firmar_{{$key+1}}" name="firmar" value="{{$item->id_comprobante}}" style="margin:0;position:relative;top:3px">
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
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
                    <button class="btn btn-success" onclick="facturar_comprobante()">
                        <i class="fa fa-file-text" aria-hidden="true"></i>
                        Facturar
                    </button>
                </div>
            @endif
        @endif
        <div id="pagination_listado_comprobante">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
