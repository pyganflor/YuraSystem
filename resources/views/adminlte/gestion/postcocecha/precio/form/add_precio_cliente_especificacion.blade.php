<form id="form_add_precio_cliente_especificicacion">
    <input type="hidden" id="id_cliente"
           value="{{isset($especificaciones_cliente[0]->id_cliente) ? $especificaciones_cliente[0]->id_cliente : ""}}">
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 1em; border: 2px solid #9d9d9d"
           id="table_content_empaque_c">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                VARIEDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                CALIBRE
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                CAJA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                RAMO X CAJA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                PRESENTACIÓN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                TALLOS X RAMO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                LONGITUD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width: 10%;">
                PRECIO $
            </th>
        </tr>
        </thead>
        @if(count($especificaciones_cliente) > 0)
            @php
                $anterior = '';
            @endphp
            @foreach($especificaciones_cliente as $x => $item)
                @foreach($item->especificacion->especificacionesEmpaque as $y => $esp_emp)
                    @foreach($esp_emp->detalles as $z => $det_esp_emp)
                        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                            style="border-top: {{$item->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}">
                            <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                                class="text-center">
                                {{$det_esp_emp->variedad->siglas}}
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                {{$det_esp_emp->clasificacion_ramo->nombre}}
                            </td>
                            @if($z == 0)
                                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center"
                                    rowspan="{{count($esp_emp->detalles)}}">
                                    {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                </td>
                            @endif
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                {{$det_esp_emp->cantidad}}
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                {{$det_esp_emp->empaque_p->nombre}}
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                {{$det_esp_emp->tallos_x_ramos}}
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                                    {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                                @endif
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center"
                                id="td_precio_{{$x+1}}">
                                <input type="text" class="form-control" id="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                       name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                       value="{{getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != '' ?
                                       getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad : ''}}"
                                       onkeypress="return barra_string(this,event)" required style="background-color: beige">
                                <input type="hidden" id="id_detalle_especificacion_empaque_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                       class="id_detalle_especificacion_empaque"
                                       value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                            </td>
                        </tr>
                        @php
                            $anterior = $item->id_especificacion;
                        @endphp
                    @endforeach
                @endforeach
            @endforeach
        @else
            <tr>
                <td colspan="8">
                    <div class="alert alert-info text-center">No se han asginado especificaciones a este cliente</div>
                </td>
            </tr>
        @endif
    </table>
</form>
