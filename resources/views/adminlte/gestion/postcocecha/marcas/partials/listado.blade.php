<div id="table_marcas">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_marcas">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    NOMBRE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DESCRIPCIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1 ? '':'error'}}" id="row_marcas_{{$item->id_marca}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->descripcion}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" class="btn btn-default btn-xs" title="Editar marca"
                           onclick="add_marca('{{$item->id_marca}}')" id="btn_view_marcas_{{$item->id_marca}}">
                            <i class="fa fa-fw fa-eye" style="color: black"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_marcas">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
