@if(sizeof($grupos_menu)>0)
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_content_grupos_menu">
        <thead>
        <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
            <th class="text-center" style="border-color: #9d9d9d">GRUPO de MENÚ</th>
            <th class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-default" title="Añadir Grupo" onclick="add_grupo_menu()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </th>
        </tr>
        </thead>
        @foreach($grupos_menu as $g)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$g->estado == 'A' ? '' : 'error'}}" id="row_grupo_menu_{{$g->id_grupo_menu}}">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand" onclick="select_grupo_menu('{{$g->id_grupo_menu}}')">
                    <i class="fa fa-fw fa-check hidden icon_hidden_g" id="icon_grupo_menu_{{$g->id_grupo_menu}}"></i> {{$g->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-default" type="button" title="Editar"
                                onclick="edit_grupo_menu('{{$g->id_grupo_menu}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" type="button" title="{{$g->estado == 'A' ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_grupo_menu('{{$g->id_grupo_menu}}','{{$g->estado}}')">
                            <i class="fa fa-fw fa-{{$g->estado == 'A' ? 'trash' : 'unlock'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
@else
    <div class="alert alert-info text-center">No hay grupos de menú registrados</div>
@endif
