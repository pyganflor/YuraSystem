<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_menus">
    <thead>
    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
        <th class="text-center" style="border-color: #9d9d9d">MENÚ</th>
        <th class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-default" title="Añadir Menú" onclick="add_menu()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($menus)>0)
        @foreach($menus as $m)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$m->estado == 'A' ? '' : 'error'}}" id="row_menu_{{$m->id_menu}}">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand" onclick="select_menu('{{$m->id_menu}}')">
                    <i class="fa fa-fw fa-check hidden icon_hidden_m" id="icon_menu_{{$m->id_menu}}"></i> {{$m->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-default" type="button" title="Editar"
                                onclick="edit_menu('{{$m->id_menu}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" type="button" title="{{$m->estado == 'A' ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_menu('{{$m->id_menu}}','{{$m->estado}}')">
                            <i class="fa fa-fw fa-{{$m->estado == 'A' ? 'trash' : 'unlock'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center mouse-hand" colspan="2">
                No hay menús registrados en este grupo
            </td>
        </tr>
    @endif
</table>
