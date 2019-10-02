<div id="table_agencia_carga">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_agencias_carga">
            <thead>
            <tr style="background-color: #dd4b39; color: white">

                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Nombre
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Identificacion
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Opciones
                </th>
            </tr>
            </thead>
            @foreach($listado as $key => $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1  ? '':'error'}}" id="row_agencia_{{$item->id_agencia_carga}}">
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->nombre}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$item->identificacion}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-xs" title="Editar"
                                    onclick="create_agencia_carga('{{$item->id_agencia_carga}}','{{csrf_token()}}')" id="ver_agencia_carga">
                                <i class="fa fa-fw fa-pencil" style="color: black"></i>
                            </button>
                            <button type="button" class="btn {{$item->estado == 1 ? 'btn-success' : 'btn-danger'}} btn-xs" title="Desactivar"
                                    onclick="actualizar_agencia_carga('{{$item->id_agencia_carga}}','{{$item->estado}}')"
                                    id="boton_agencia_carga_{{$item->id_agencia_carga}}">
                                <i class="fa fa-fw {{$item->estado == 1 ? 'fa-trash' : 'fa-unlock'}}" style="color: black"
                                   id="icon_agencia_carga_{{$item->id_agencia_carga}}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_recepciones">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
