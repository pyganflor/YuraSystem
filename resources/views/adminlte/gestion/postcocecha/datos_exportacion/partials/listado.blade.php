<div id="table_dato_exportacion">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_dato_exportacion">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    NOMBRE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ESTADO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                    <td style="border-color: #9d9d9d" class="text-center">{{strtoupper($item->nombre)}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{!! $item->estado == 1 ? "Activo" : "Inactivo" !!}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <button type="button" class="btn btn-default btn-xs" title="Asignar dato de exportación" onclick="form_asignacion_dato_exportacion('{{$item->id_dato_exportacion}}')">
                            <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                        </button>
                        @if($item->estado == 1)
                        <button type="button" class="btn btn-warning btn-xs" title="Editar dato de exportación" onclick="add_dato_exportacion('{{$item->id_dato_exportacion}}')">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </button>
                        @endif
                        <a href="javascript:void(0)" class="btn btn-{{$item->estado == 1 ? 'danger':'success'}} btn-xs" title="{{$item->estado == 1 ? 'Deshabilitar':'Habilitar'}}"
                           onclick="update_estado_dato_exportacion('{{$item->id_dato_exportacion}}','{{$item->estado}}')">
                            <i class="fa fa-fw fa-{{$item->estado == 1 ? 'trash':'undo'}}" style="color: white" ></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
