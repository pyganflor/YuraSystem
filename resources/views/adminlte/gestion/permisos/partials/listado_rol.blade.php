@if(sizeof($roles)>0)
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_content_roles">
        <thead>
        <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
            <th class="text-center" style="border-color: #9d9d9d">ROL</th>
            <th class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-default" title="Añadir Rol" onclick="add_rol()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </th>
        </tr>
        </thead>
        @foreach($roles as $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$item->estado == 'A' ? '' : 'error'}}" id="row_rol_{{$item->id_rol}}">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand" onclick="select_rol('{{$item->id_rol}}')">
                    <i class="fa fa-fw fa-check hidden icon_hidden_r" id="icon_rol_{{$item->id_rol}}"></i> {{$item->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        @if($item->tipo == 'S')
                            <button class="btn btn-xs btn-danger" type="button" title="{{$item->estado == 'A' ? 'Desactivar' : 'Activar'}}"
                                    onclick="cambiar_estado_rol('{{$item->id_rol}}','{{$item->estado}}')">
                                <i class="fa fa-fw fa-{{$item->estado == 'A' ? 'trash' : 'unlock'}}"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
@else
    <div class="alert alert-info text-center">No hay grupos de menú registrados</div>
@endif
