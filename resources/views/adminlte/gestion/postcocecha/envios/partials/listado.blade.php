<div id="table_envios" style="margin-top: 20px">
     @if(sizeof($listado)>0)
        <form id="form_envios">
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_envios">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                   ENVÍO N#
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CLIENTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA DE ENVíO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CANTIDAD x ESPECIFICIACIONES
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    AGENCIA DE TRANSPORTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TIPO AGENCIA
                </th>
                @if(!empty(yura\Modelos\Usuario::where('id_usuario',session::get('id_usuario'))->first()->punto_acceso))
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    DESCUENTO $ {{--/ EN FACTURA--}}
                </th>
                {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    GENERAR COMPROBANTE ELECTRÓNICO
                </th>--}}
                @endif
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @php $x =1; @endphp
            @foreach($listado as $key => $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                     onmouseleave="$(this).css('background-color','')" class="" id="row_pedidos_">
                    <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center">
                        ENV{{str_pad($item[0]->id_envio,9,"0",STR_PAD_LEFT)}}
                    </td>
                    <td style="border-color: #9d9d9d;vertical-align: middle;" class="text-center">
                        {{$item[0]->c_nombre}}
                    </td>
                    <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand"  id="popover_pedidos">
                        {{\Carbon\Carbon::parse($item[0]->fecha_envio)->format('Y-m-d')}}
                    </td>
                    <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand">
                        <ul style="padding: 0;">
                            @foreach($item as $especificacion)
                                <li style="list-style: none">
                                {{$especificacion->cantidad}} x {{getDetalleEspecificacion($especificacion->id_especificacion)}}. {{--{{$especificacion->nombre}}--}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand"  id="popover_pedidos">
                        {{$item[0]->at_nombre}}
                    </td>
                    <td style="border-color: #9d9d9d;vertical-align: middle" class="text-center mouse-hand"  id="popover_pedidos">
                        @if($item[0]->tipo_agencia == 'A')
                            AÉREA
                        @elseif($item[0]->tipo_agencia == 'T')
                            TERRESTRE
                        @elseif($item[0]->tipo_agencia == 'M')
                            MARíTIMA
                        @endif
                    </td>
                    @if(!empty(yura\Modelos\Usuario::where('id_usuario',session::get('id_usuario'))->first()->punto_acceso))
                    <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                        <label>Descuento</label><br />
                        <input type="number" onkeypress="return isNumber(event)" id="descuento_{{$x}}" {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}} name="descuento_{{$x}}"
                               ondblclick="activar(this)" value="0.00" readonly><br />
                        <input type="text" style="margin-top:5px" placeholder="Guía madre" {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}
                                id="guia_madre_{{$x}}" name="guia_madre_{{$x}}"><br />
                        <input type="text" placeholder="Guía hija" id="guia_hija_{{$x}}" name="guia_hija_{{$x}}"
                                {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}><br />
                        <select id="codigo_pais_{{$x}}" name="codigo_pais_{{$x}}" style="margin-left: 1px;width: 128px;"
                            {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}>
                            @foreach($paises as $pais)
                                <option {{ ($item[0]->codigo_pais == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                            @endforeach
                        </select><br />
                        <input type="text" placeholder="Destino" id="destino_{{$x}}" name="destino_{{$x}}" value="{{$item[0]->provincia.", ".$item[0]->direccion}}"
                            {{getFacturado($item[0]->id_envio) != 0 ? "disabled='disabled'" : ""}}>
                    </td>
                    <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                        @if($item[0]->empaquetado == 0)
                        @endif
                        @if($item[0]->empaquetado == 1 )
                            @if(getFacturado($item[0]->id_envio) == 0 )
                                <button type="button" class="btn btn-default btn-xs">
                                    <input type="checkbox" id="{{$x}}" name="check_envio" onclick="input_required(this)" value="{{$item[0]->id_envio}}"
                                           style="margin: 0;position: relative;top: 3px;" title="Generar documento electrónico">
                                </button>
                            @else
                                <button class="btn btn-default btn-xs" title="Ver comprobante electrónico" onclick="ver_factura()">
                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                </button>
                            @endif
                        @else
                                <button class="btn  btn-default btn-xs" type="button" title="Editar envío" id="edit_envio"
                                        onclick="editar_envio('{{$item[0]->id_envio}}','{{$item[0]->id_detalle_envio}}','{{$item[0]->id_pedido}}','{{@csrf_token()}}')">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </button>
                                <button class="btn btn-default btn-xs" title="Se debe confirmar el pedido para poder generar el comprobante electrónico de este envío">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                </button>
                        @endif
                    </td>
                    @endif
                    {{--<td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                        @if($item[0]->empaquetado == 0)
                            <button class="btn  btn-default btn-xs" type="button" title="Editar envío" id="edit_envio"
                                    onclick="editar_envio('{{$item[0]->id_envio}}','{{$item[0]->id_detalle_envio}}','{{$item[0]->id_pedido}}','{{@csrf_token()}}')">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </button>
                        @endif
                    </td>--}}
                </tr>
                @php $x++; @endphp
            @endforeach
    </table>
        @if(!empty(yura\Modelos\Usuario::where('id_usuario',session::get('id_usuario'))->first()->punto_acceso))
            <div class="text-center">
                <button type="button" class="btn btn-success" onclick="genera_comprobante_cliente()">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    Generar factura
                </button>
            </div>
        @endif
        </form>
    <div id="pagination_listado_envios">
       {!! str_replace('/?','?',$listado->render()) !!}
        </div>
@else
<div class="alert alert-info text-center">No se han encontrado conincidencias</div>
@endif
</div>

<script>
$(function () {
$('[data-toggle="popover"]').popover()
});
</script>
