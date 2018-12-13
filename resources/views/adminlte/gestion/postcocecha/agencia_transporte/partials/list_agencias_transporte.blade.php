<div id="table_agencia_transporte">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_agencias_transporte">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    NOMBRE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TIPO DE AGENCIA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    ESTADO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $key => $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1  ? '':'error'}}" id="row_agencia_transporte_{{$item->id_agencia_transporte}}">
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->nombre}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->tipo_agencia === 'A' ? 'Aérea' : ''}}
                        {{$item->tipo_agencia === 'M' ? 'Maritima' : ''}}
                        {{$item->tipo_agencia === 'T' ? 'Terrestre' : ''}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->estado ==  1 ? 'Activo' : 'Inactivo'}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-xs" title="Editar"
                                    onclick="create_agencia_transporte('{{$item->id_agencia_transporte}}')" id="ver_agencia_transporte">
                                <i class="fa fa-fw fa-pencil" style="color: black"></i>
                            </button>
                            <button type="button" class="btn {{$item->estado == 1 ? 'btn-success' : 'btn-danger'}} btn-xs" title="Desactivar"
                                    onclick="actualizar_agencia_transporte('{{$item->id_agencia_transporte}}','{{$item->estado}}')"
                                    id="boton_agencia_transporte_{{$item->id_agencia_transporte}}">
                                <i class="fa fa-fw {{$item->estado == 1 ? 'fa-trash' : 'fa-unlock'}}" style="color: black"
                                   id="icon_agencia_transporte_{{$item->id_agencia_transporte}}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_agencia_transporte">
         {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
