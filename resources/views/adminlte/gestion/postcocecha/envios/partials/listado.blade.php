<div id="table_envios" style="margin-top: 20px">
     @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_envios">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA DE ENVíO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CANTIDAD / ESPECIFICIACIONES
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CLIENTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    AGENCIA DE TRANSPORTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TIPO AGENCIA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $key => $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                     onmouseleave="$(this).css('background-color','')" class="" id="row_pedidos_">
                    <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                        {{$item->fecha_envio}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center mouse-hand">
                        {{$item->cantidad}} {{$item->nombre}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->c_nombre}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                        {{$item->at_nombre}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                        @if($item->tipo_agencia == 'A')
                            AÉREA
                        @elseif($item->tipo_agencia == 'T')
                            TERRESTRE
                        @elseif($item->tipo_agencia == 'M')
                            MARíTIMA
                        @endif

                    </td>
                    <td class="text-center"  style="border-color: #9d9d9d">
                        {{--@if(now()->toDateString() <> $item->fecha_envio )--}}
                            <button class="btn  btn-default btn-xs" type="button" title="Editar envío" id="edit_envio"
                                    onclick="editar_envio('{{$item->id_envio}}','{{$item->id_detalle_envio}}','{{$item->id_pedido}}','{{@csrf_token()}}')">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </button>
                            {{--<button class="btn  btn-danger btn-xs" type="button" title="Eliminar envío" id="edit_envio"
                                    onclick="eliminar_envio()">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>--}}
                        {{-- @endif--}}
                    </td>
                </tr>
            @endforeach
    </table>
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
