<div id="table_usuarios">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_usuarios">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    NOMBRE COMPLETO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CORREO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    USUARIO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ROL
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 'A'?'':'error'}}" id="row_usuarios_{{$item->id_usuario}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre_completo}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->correo}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->username}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->rol}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" class="btn btn-default btn-xs" title="Detalles"
                           onclick="ver_usuario('{{$item->id_usuario}}')" id="btn_view_usuario_{{$item->id_usuario}}">
                            <i class="fa fa-fw fa-eye" style="color: black"></i>
                        </a>
                        @if(getUsuario($item->id_usuario)->rol()->tipo == 'S')
                            <a href="javascript:void(0)" class="btn {{$item->estado == 'A' ? 'btn-success' : 'btn-danger'}} btn-xs"
                               title="{{$item->estado == 'A' ? 'Desactivar' : 'Activar'}}"
                               onclick="eliminar_usuario('{{$item->id_usuario}}', '{{$item->estado}}')"
                               id="btn_usuarios_{{$item->id_usuario}}">
                                <i class="fa fa-fw {{$item->estado == 'A' ? 'fa-trash' : 'fa-unlock'}}" style="color: black"
                                   id="icon_usuarios_{{$item->id_usuario}}"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>