<div id="table_detalles_reales">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_detalles_reales">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Hora
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Variedad
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Clasificación unitaria
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Ramos
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Tallos por ramo
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Total
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                    onmouseleave="$(this).css('background-color','')" class="{{$item->estado == 1?'':'error'}}"
                    id="row_detalles_reales_{{$item->id_clasificacion_verde}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{substr($item->fecha_registro,11,5)}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre_variedad}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{explode('|',$item->nombre_unitaria)[0].''.$item->unidad_medida}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->cantidad_ramos}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->tallos_x_ramos}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->cantidad_ramos * $item->cantidad_ramos}}
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_detalles_reales">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>