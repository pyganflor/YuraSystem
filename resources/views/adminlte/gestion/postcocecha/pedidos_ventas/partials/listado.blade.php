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
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CLIENTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CANTIDAD / ESPECIFICIACIONES
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    DESCRIPCIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    OPCIONES

                </th>
            </tr>
            </thead>
             @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                    onmouseleave="$(this).css('background-color','')" class=""
                    id="row_pedidos_">
                    <td style="border-color: #9d9d9d" class="text-center">
                         {{$item->fecha_pedido}}
                     </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->nombre}}
                    </td>
                     <td style="border-color: #9d9d9d" class="text-center mouse-hand">
                         <ul>
                            @foreach(getPedido($item->id_pedido)->detalles as $detalle)
                                 <li style="list-style: none">
                                     <a  tabindex="0" data-toggle="popover"
                                         title="Descripción" data-trigger="focus"
                                         data-content="
                                         @foreach($detalle->cliente_especificacion->especificacion->especificacionesEmpaque as $espEmp)
                                             {{$espEmp->cantidad}}
                                             {{$espEmp->empaque->nombre}} con las variedades
                                                @foreach($espEmp->detalles as $det)
                                                     {{$det->variedad->nombre}}
                                                         con {{$det->cantidad}}  ramos de
                                                                 {{$det->clasificacion_ramo->nombre}} gr c/u,
                                                                 con envoltura de {{$det->empaque_e->nombre}}
                                                         y presentación de {{$det->empaque_p->nombre}}  {{" y ".$det->tallos_x_ramos." Tallos por ramos"}}
                                                        @php
                                                            if(!empty($det->tallos_x_ramos)) {
                                                            echo  " y ".$det->tallos_x_ramos." Tallos por ramos";
                                                             }
                                                        @endphp
                                                @endforeach
                                         @endforeach">
                                         {{$detalle->cantidad}} {{$detalle->cliente_especificacion->especificacion->nombre}}
                                     </a>
                                 </li>
                             @endforeach
                        </ul>
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                      {{$item->descripcion}}
                    </td>
                    <td class="text-center"  style="border-color: #9d9d9d">
                       @if(now()->toDateString() < $item->fecha_pedido )
                            <button class="btn  btn-{!! $item->estado == 1 ? 'success' : 'warning' !!} btn-xs" type="button" title="{!! $item->estado == 1 ? 'Pedido activo' : 'Pedido cancelado' !!}"
                                    id="edit_pedidos" onclick="cancelar_pedidos('{{$item->id_pedido}}','','{{$item->estado}}','{{@csrf_token()}}')">
                                <i class="fa fa-{!! $item->estado == 1 ? 'check' : 'ban' !!}" aria-hidden="true"></i>
                            </button>
                       @endif
                       @if(yura\Modelos\Envio::where('id_pedido',$item->id_pedido)->count() <= 0)
                           <button class="btn btn-default btn-xs" title="Realizar envío" onclick="add_envio('{{$item->id_pedido}}','{{@csrf_token()}}')">
                               <i class="fa fa-plane" aria-hidden="true"></i>
                           </button>
                       @else
                            <button class="btn btn-default btn-xs" title="Ver envío" onclick="ver_envio('{{$item->id_pedido}}')">
                                <i class="fa fa-eye" aria-hidden="true"></i>
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


<script>
    $(function () {
        $('[data-toggle="popover"]').popover()
    });
</script>
