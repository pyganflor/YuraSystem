<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_modulos">
    <thead>
    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
        <th class="text-center" style="border-color: #9d9d9d" colspan="2">MÓDULO</th>
        <th class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-default" title="Añadir Módulo" onclick="add_modulo()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($modulos)>0)
        @foreach($modulos as $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$item->estado == 1 ? '' : 'error'}}" id="row_modulo_{{$item->id_modulo}}">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand" onclick="select_modulo('{{$item->id_modulo}}')">
                    <i class="fa fa-fw fa-check hidden icon_hidden_m" id="icon_modulo_{{$item->id_modulo}}"></i> {{$item->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$item->area != '' ? $item->area : 0}} ha
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-default" type="button" title="Editar"
                                onclick="edit_modulo('{{$item->id_modulo}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" type="button" title="{{$item->estado == 1 ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_modulo('{{$item->id_modulo}}','{{$item->estado}}')">
                            <i class="fa fa-fw fa-{{$item->estado == 1 ? 'trash' : 'unlock'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center" colspan="3">
                No hay menús registrados en este grupo
            </td>
        </tr>
    @endif
</table>
