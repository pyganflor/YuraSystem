<div id="table_especificaciones">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_especificaciones">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ASIGNAR
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ESPECIFICACIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DESCRIPCIÓN
                </th>

                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1 ? '':'error'}}" id="row_especificacion_{{$item->id_especificacion}}">
                    <td style="border-color: #9d9d9d;vertical-align: middle;" class="text-center">
                        @php
                            $check = '';
                            if(count($id_especificaciones) > 0){
                                for ($i=0; $i< count($id_especificaciones); $i++){
                                    if($id_especificaciones[$i]->id_especificacion === $item->id_especificacion){
                                    $check = 'checked';
                                    }
                                }
                            }
                        @endphp
                        <input type="checkbox" {{$check}}  {{$item->estado == 1 ? '':'disabled'}}
                               name="especificacion_{{$item->id_especificacion}}" id="especificacion_{{$item->id_especificacion}}"
                               value="{{$item->id_especificacion}}"  onchange="asignar_especificacion_cliente(this.id,'{{$item->id_especificacion}}')">
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                       {{getDetalleEspecificacion($item->id_especificacion)}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->descripcion}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" class="btn btn-{{$item->estado == 1 ? 'success':'warning'}} btn-xs" title="{{$item->estado == 1 ? 'Habilitado':'Deshabilitado'}}"
                           onclick="update_especificacion('{{$item->id_especificacion}}','{{$item->estado}}','{{csrf_token()}}',true)">
                            <i class="fa fa-fw fa-{{$item->estado == 1 ? 'check':'ban'}}" style="color: white" ></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_clientes">
           {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">El cliente no posee especificaciones asignadas</div>
    @endif
</div>
<script>
    function asignar_especificacion_cliente(id_check,id_especificacion) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_especificacion: id_especificacion,
            accion           : $('#'+id_check).is(':checked') == true ? 1 : 0,
            id_cliente       : $('#id_cliente').val()
        };
        post_jquery('{{url('clientes/asignar_especificacion')}}', datos, function () {
            //cerrar_modals();
            //detalles_cliente($('#id_cliente').val());
            //setTimeout(function(){ admin_especificaciones($('#id_cliente').val());  },200);
            if($('#'+id_check).is(':checked') == false){
                ver_especificaciones(($('#id_cliente').val()));
            }
        });
        $.LoadingOverlay('hide');
    }

    function verifica_especificacion_cliente() {

    }
</script>
