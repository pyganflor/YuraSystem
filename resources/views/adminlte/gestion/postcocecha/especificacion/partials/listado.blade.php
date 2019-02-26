<div id="table_especificaciones">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_especificaciones">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    NOMBRE ESPECIFACIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DETALLE ESPECIFACIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DESCRIPCIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    TIPO
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
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1 ? '':'error'}}" id="row_especificaciones_{{$item->id_especificacion}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre_especificacicon}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{getDetalleEspecificacion($item->id_especificacion)}}</td>
                    <td style="border-color: #9d9d9d" class="text-center"> {{$item->descripcion}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">  {{$item->tipo == "N" ? "Normal": "Otros"}} </td>
                    <td style="border-color: #9d9d9d" class="text-center">  {{$item->estado == 0 ? "Descativado": "Activo"}} </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @if($item->tipo == "N" && $item->estado == 1)
                        <button type="button" class="btn btn-default btn-xs" title="Ver asignaciones" onclick="asignar_especificacicon('{{$item->id_especificacion}}','{{getDetalleEspecificacion($item->id_especificacion)}}')">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                        @endif
                            <a href="javascript:void(0)" class="btn btn-{{$item->estado == 1 ? 'success':'warning'}} btn-xs" title="{{$item->estado == 1 ? 'Habilitada':'Deshabilitada'}}"
                               onclick="update_especificacion('{{$item->id_especificacion}}','{{$item->estado}}','{{csrf_token()}}')">
                                <i class="fa fa-fw fa-{{$item->estado == 1 ? 'check':'ban'}}" style="color: white" ></i>
                            </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_especificaciones">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>

