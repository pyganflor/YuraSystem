{{--<div id="table_pedidos" style="margin-top: 20px">
    @if(sizeof($listado)>0)
        @if(!$columnaFecha)
            <table>
                <tr>
                    <td style="padding: 0px 0px 0px 5px">
                        <b>Fecha: </b> {{Carbon\Carbon::parse($listado[0]->fecha_pedido)->format('d-m-Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px 0px 0px 5px">
                        <b>Día: </b> {{getDias()[transformDiaPhp(date('w', strtotime($listado[0]->fecha_pedido)))]}}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px 0px 0px 5px">
                        <b>Semana:</b> {{getSemanaByDate($listado[0]->fecha_pedido)->codigo}}
                    </td>
                </tr>
            </table>
        @endif
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.85em; border-color: #9d9d9d"
               id="table_content_pedidos">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                @if($columnaFecha)
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        FECHA
                    </th>
                @endif
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;">
                    CLIENTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    PIEZAS
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
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width: 32px;">
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
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    AGENCIA DE CARGA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 85px;">
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
                    @if($columnaFecha)
                        <td style="border-color: #9d9d9d" class="text-center">
                            {{$item->fecha_pedido}}
                        </td>
                    @endif
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->nombre}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->cantidad * getCantidadCajas($item->id_pedido)}}
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
                    <td style="border-color: #9d9d9d" class="text-center mouse-hand" id="popover_pedidos">
                        {{getAgenciaCarga($item->id_agencia_carga)->nombre}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        @if($item->empaquetado == 0)
                            <button class="btn  btn-{!! $item->estado == 1 ? 'success' : 'warning' !!} btn-xs" type="button"
                                    title="{!! $item->estado == 1 ? 'Pedido activo' : 'Pedido cancelado' !!}"
                                    id="edit_pedidos"
                                    onclick="cancelar_pedidos('{{$item->id_pedido}}','','{{$item->estado}}','{{@csrf_token()}}')">
                                <i class="fa fa-{!! $item->estado == 1 ? 'check' : 'ban' !!}" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if(yura\Modelos\Envio::where('id_pedido',$item->id_pedido)->count() == 0)
                            <button class="btn btn-default btn-xs" title="Realizar envío"
                                    onclick="add_envio('{{$item->id_pedido}}','{{@csrf_token()}}')">
                                <i class="fa fa-plane" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-default btn-xs" title="Ver envío" onclick="ver_envio('{{$item->id_pedido}}')">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if($item->empaquetado == 0)
                            <button type="button" class="btn btn-default btn-xs" title="Editar pedido"
                                    onclick="editar_pedido('{{$item->id_cliente}}','{{$item->id_pedido}}')">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </button>
                        @endif

                        @if(getPedido($item->id_pedido)->haveDistribucion() == 1)
                            <button type="button" class="btn btn-xs btn-info" title="Distribuir"
                                    onclick="distribuir_orden_semanal('{{$item->id_pedido}}')">
                                <i class="fa fa-fw fa-gift"></i>
                            </button>
                        @elseif(getPedido($item->id_pedido)->haveDistribucion() == 2)
                            <button type="button" class="btn btn-xs btn-info" title="Ver distribución"
                                    onclick="ver_distribucion_orden_semanal('{{$item->id_pedido}}')">
                                <i class="fa fa-fw fa-gift"></i>
                            </button>
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
<style>
    div#table_content_pedidos_wrapper div.row:first-child{
        display: none;
    }
</style>--}}

