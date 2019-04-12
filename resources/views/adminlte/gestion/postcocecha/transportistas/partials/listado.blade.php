<div id="table_transportista">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_transportista">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    NOMBRE EMPRESA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    RUC
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ENCARGADO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    RUC DEL ENCARGADO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    TELÉFONO DEL ENCARGADO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DIRECCIÓN EMPRESA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ESTADO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1 ? '':'error'}}" id="row_transportista_{{$item->id_marca}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre_empresa}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->ruc}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->encargado}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->ruc_encargado}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->telefono_encargado}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->direccion_empresa}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->estado == 1 ? "Activa" : "Inactiva"}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <button class="btn btn-default btn-xs" title="Asignar transportes y conductores"
                           onclick="add_camiones_condutores('{{$item->id_transportista}}')" {{$item->estado == 0 ? "disabled": ""}} >
                            <i class="fa fa-truck" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-{{$item->estado == 0 ? "success": "danger"}} btn-xs" title="{{$item->estado == 0 ? "Activar": "Desactivar"}} transportes y conductores"
                                onclick="desactivar_transportista('{{$item->id_transportista}}','{{$item->estado}}')">
                            <i class="fa fa-{{$item->estado == 0 ? "undo": "trash"}}" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_transportista">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
