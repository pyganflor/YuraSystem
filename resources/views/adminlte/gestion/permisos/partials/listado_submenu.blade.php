<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_submenus">
    <thead>
    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
        <th class="text-center" style="border-color: #9d9d9d">SUBMENÚ</th>
        <th class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-default" title="Añadir submenú" onclick="add_submenu()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($rol->submenus)>0)
        @foreach($rol->submenus as $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$item->estado == 'A' ? '' : 'error'}}" id="row_menu_{{$item->id_rol_submenu}}">
                <td style="border-color: #9d9d9d" class="text-center">
                    <ol class="breadcrumb" style="margin-bottom: 0">
                        <li class="active">
                            {{$item->submenu->menu->grupo_menu->nombre}}
                        </li>
                        <li class="active">
                            {{$item->submenu->menu->nombre}}
                        </li>
                        <li>
                            <strong>{!! $item->submenu->nombre !!}</strong>
                        </li>
                    </ol>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-danger" type="button"
                                title="{{$item->estado == 'A' ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_rol_submenu('{{$item->id_rol_submenu}}','{{$item->estado}}')">
                            <i class="fa fa-fw fa-{{$item->estado == 'A' ? 'trash' : 'unlock'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center mouse-hand" colspan="2">
                No hay submenús registrados en este rol
            </td>
        </tr>
    @endif
</table>
