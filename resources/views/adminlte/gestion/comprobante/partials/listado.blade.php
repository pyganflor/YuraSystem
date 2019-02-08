<div id="table_comprobante">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_comprobante">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ENVÍO
                </th>
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
                    <td style="border-color: #9d9d9d" class="text-center">ENV{{str_pad($item->id_envio,9,"0",STR_PAD_LEFT)}}</td>
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
                            <button class="btn btn-default btn-xs" title="Ver factura" onclick="ver_factura()">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-default btn-xs" title="Reenviar correo" onclick="reenviar_correo()">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if($item->estado == 1)
                            <button class="btn btn-default btn-xs">
                                <input type="checkbox" id="facturar_{{$key+1}}" name="facturar"  title="{{$item->nombre_comprobante=="FACTURA" ? "Facturar" : "Enviar al SRI"}}" value="{{$item->clave_acceso}}" style="margin:0;position:relative;top:3px">
                            </button>
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
    @if($item->estado !=5)
        @if($item->estado == 0)
            <div class="text-center">
                <button class="btn btn-success" onclick="firmar_comprobante()">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    Pre facturar
                </button>
            </div>
        @else
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
