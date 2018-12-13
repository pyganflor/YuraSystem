<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_usuarios">
    <thead>
    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
        <th class="text-center" style="border-color: #9d9d9d">USUARIO</th>
        <th class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-default" title="Asignar a un usuario" onclick="add_usuario()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($rol->usuarios)>0)
        @foreach($rol->usuarios as $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$item->estado == 'A' ? '' : 'error'}}" id="row_menu_{{$item->id_usuario}}">
                <td style="border-color: #9d9d9d" class="text-center" onclick="select_usuario('{{$item->id_usuario}}')" colspan="2">
                    <i class="fa fa-fw fa-check hidden icon_hidden_u"
                       id="icon_usuario_{{$item->id_usuario}}"></i> {{$item->nombre_completo}}
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center mouse-hand" colspan="2">
                No hay usuarios registrados en este rol
            </td>
        </tr>
    @endif
</table>