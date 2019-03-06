<div id="table_pedidos" style="margin-top: 20px">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_pedidos">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    VARIEDAD
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CALIBRE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CAJA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    RAMO X CAJA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    PRESENTACIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    TALLOS X RAMO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    LONGITUD
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                @php
                    foreach(getPedido($item->id_pedido)->detalles as $detalle)
                        $esp = getDetalleEspecificacion($detalle->cliente_especificacion->especificacion->id_especificacion);
                @endphp
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                    onmouseleave="$(this).css('background-color','')" class=""
                    id="row_pedidos_">
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{getDias()[transformDiaPhp(date('w', strtotime($item->fecha_pedido)))]}}
                        {{convertDateToText($item->fecha_pedido)}}
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        <ul style="padding: 0;margin:0">
                            @foreach($esp as $e)
                                <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                    {{$e["variedad"]}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        <ul style="padding: 0;margin:0">
                            @foreach($esp as  $e)
                                <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">

                                    {{$e["calibre"]}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        <ul style="padding: 0;margin:0">
                            @foreach($esp as $e)
                                <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                    {{$e["caja"]}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        <ul style="padding: 0;margin:0">
                            @foreach($esp as $e)
                                <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                    {{$e["rxc"]}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        <ul style="padding: 0;margin:0">
                            @foreach($esp as $e)
                                <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                    {{$e["presentacion"]}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        <ul style="padding: 0;margin:0">
                            @foreach($esp as $e)
                                <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                    {{$e["txr"] == null ? "-" : $e["txr"] }}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        <ul style="padding: 0;margin:0">
                            @foreach($esp as $e)
                                <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                    {{$e["longitud"] == null ? "-" : $e["longitud"] }} {{($e["unidad_medida_longitud"] == null || $e["longitud"] == null) ? "" : $e["unidad_medida_longitud"]}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    {{--<td style="border-color: #9d9d9d" class="text-center mouse-hand" id="popover_pedidos">
                        {{$item->descripcion}}
                    </td>--}}
                    <td class="text-center" style="border-color: #9d9d9d">
                        @if($item->empaquetado == 0)
                            <button class="btn  btn-{!! $item->estado == 1 ? 'success' : 'warning' !!} btn-xs" type="button"
                                    title="{!! $item->estado == 1 ? 'Pedido activo' : 'Pedido cancelado' !!}" id="edit_pedidos"
                                    onclick="cancelar_pedidos('{{$item->id_pedido}}','{{$idCliente}}','{{$item->estado}}','{{@csrf_token()}}')">
                                <i class="fa fa-{!! $item->estado == 1 ? 'check' : 'ban' !!}" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if(yura\Modelos\Envio::where('id_pedido',$item->id_pedido)->count() <= 0)
                            <button class="btn btn-default btn-xs" title="Realizar envío"
                                    onclick="add_envio('{{$item->id_pedido}}','{{@csrf_token()}}')">
                                <i class="fa fa-plane" aria-hidden="true"></i>
                            </button>
                        {{--@else
                            <button class="btn btn-default btn-xs" title="Ver envío" onclick="ver_envio('{{$item->id_pedido}}')">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>--}}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_pedidos">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han creado pedidos</div>
    @endif
</div>

<script>
    $(function () {
        $('[data-toggle="popover"]').popover()
    });
</script>
