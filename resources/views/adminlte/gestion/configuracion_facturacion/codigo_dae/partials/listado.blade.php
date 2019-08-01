<div id="table_codigo_dae">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_codigo_dae">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    PAÍS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DAE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CÓDIGO DAE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    EMPRESA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    MES
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    AÑO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1 ? '':'error'}}" id="row_codigo_dae_{{$item->id_codigo_dae}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{getPais($item->codigo_pais)->nombre}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->dae}}</td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->codigo_dae}} </td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->empresa->razon_social}} </td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->mes}} </td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->anno}} </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <button class="btn btn-warning btn-xs" title="Desactivar código" onclick="desactivar_codigo('{{$item->id_codigo_dae}}')">
                            <i class="fa fa-ban" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_codigo_dae">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
