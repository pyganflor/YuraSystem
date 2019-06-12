<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_submenus">
    <thead>
    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
        <th class="text-center" style="border-color: #9d9d9d">SUBMENÚ</th>
        <th class="text-center" style="border-color: #9d9d9d">TIPO</th>
        <th class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-default" title="Añadir Submenú" onclick="add_submenu()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($submenus)>0)
        @foreach($submenus as $s)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="row_submenu {{$s->estado == 'A' ? '' : 'error'}}" id="row_submenu_{{$s->id_submenu}}">
                <td style="border-color: #9d9d9d" class="text-center">
                    {!! $s->nombre !!}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    @if($s->tipo == 'C')
                        CRM
                    @elseif($s->tipo == 'R')
                        Reporte
                    @elseif($s->tipo == 'N')
                        Normal
                    @endif
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-default" type="button" title="Editar"
                                onclick="edit_submenu('{{$s->id_submenu}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        @if($s->url != explode('/',substr(Request::getRequestUri(),1))[0])
                            <button class="btn btn-xs btn-danger" type="button" title="{{$s->estado == 'A' ? 'Desactivar' : 'Activar'}}"
                                    onclick="cambiar_estado_submenu('{{$s->id_submenu}}','{{$s->estado}}')">
                                <i class="fa fa-fw fa-{{$s->estado == 'A' ? 'trash' : 'unlock'}}"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
            class="row_submenu">
            <td style="border-color: #9d9d9d" class="text-center" colspan="2">
                No hay submenús registrados en este menú
            </td>
        </tr>
    @endif
</table>
