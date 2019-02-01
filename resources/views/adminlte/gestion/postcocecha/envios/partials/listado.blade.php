<div id="table_envios" style="margin-top: 20px">
     @if(sizeof($listado)>0)
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
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FACTURAR
                </th>
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
                        {{str_pad($item[0]->id_envio,9,"0",STR_PAD_LEFT)}}
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
                        <input type="number" onkeypress="return isNumber(event)" min="1" id="descuento_{{$x}}" name="descuento_{{$x}}"
                               ondblclick="activar(this)" value="0.00" readonly>
                        <input type="text" style="margin-top:5px" placeholder="Guía madre" id="guia_madre_{{$x}}" name="guia_madre_{{$x}}">
                        <input type="text" placeholder="Guía hija" id="guia_hija_{{$x}}" name="guia_hija_{{$x}}">
                    </td>
                    <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                        @if($item[0]->empaquetado == 1 )
                        <input type="checkbox" id="{{$x}}" name="check_envio" value="{{$item[0]->id_envio}}">
                        @else
                            Se debe confirmar para poder facturar este envío
                        @endif
                    </td>
                    @endif
                    <td class="text-center"  style="border-color: #9d9d9d;vertical-align: middle">
                        @if($item[0]->empaquetado == 0)
                            <button class="btn  btn-default btn-xs" type="button" title="Editar envío" id="edit_envio"
                                    onclick="editar_envio('{{$item[0]->id_envio}}','{{$item[0]->id_detalle_envio}}','{{$item[0]->id_pedido}}','{{@csrf_token()}}')">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </button>
                        @endif
                    </td>
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
    <div id="pagination_listado_envios">
       {!! str_replace('/?','?',$listado->render()) !!}
        </div>
@else
<div class="alert alert-info text-center">No se han creado envíos</div>
@endif
</div>

<script>
$(function () {
$('[data-toggle="popover"]').popover()
});
</script>
