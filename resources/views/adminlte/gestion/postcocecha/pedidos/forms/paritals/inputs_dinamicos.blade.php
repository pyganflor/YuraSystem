    <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white" id="table_content_recepciones">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width: 80px">
                PIEZAS
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                VARIEDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                PESO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                CAJA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                PRESENTACIÓN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                RAMO X CAJA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                TOTAL RAMOS
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                TALLOS X RAMO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                LONGITUD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width:100px">
                PRECIO X VARIEDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width:100px">
                PRECIO X ESPECIFICACIÓN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                AGENCIA DE CARGA
            </th>
            @foreach($datos_exportacion as $key => $de)
                <th class="th_datos_exportacion text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    id="th_datos_exportacion_{{$key+1}}" style="border-color: #9d9d9d;width: 80px;">
                    {{strtoupper($de->nombre)}}
                </th>
            @endforeach
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width:100px;width: 20px;">
                OPCIONES
            </th>
        </tr>
        </thead>
        <tbody id="tbody_inputs_pedidos">
            @if(count($especificaciones) >0)
                @php $anterior = ''; @endphp
                @foreach($especificaciones as $x => $item)
                    @php $b=1 @endphp
                    @foreach(getEspecificacion($item->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
                        @foreach($esp_emp->detalles as $z => $det_esp_emp)
                            <tr style="border-top: {{$item->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}" >
                                @if($item->id_especificacion != $anterior)
                                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                        <input type="number" min="0" id="cantidad_piezas_{{($x+1)}}" style="border: none" onchange="calcular_precio_pedido(this)"
                                               name="cantidad_piezas_{{$item->id_especificacion}}" class="text-center form-control cantidad_{{($x+1)}} input_cantidad" value="">
                                        <input type="hidden" id="id_cliente_pedido_especificacion_{{($x+1)}}" value="{{$item->id_cliente_pedido_especificacion}}">
                                    </td>
                                @endif
                                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                                        {{$det_esp_emp->variedad->siglas}}
                                        <input type="hidden" class="input_variedad_{{$x+1}}" id="id_variedad_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                                    </td>
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 70px;" class="text-center">
                                        {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                        <input type="hidden" id="id_detalle_especificacion_empaque_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                    </td>
                                    @if($z == 0)
                                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center"
                                            rowspan="{{count($esp_emp->detalles)}}">
                                            {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                        </td>
                                    @endif
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                        {{$det_esp_emp->empaque_p->nombre}}
                                    </td>
                                    <td  style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                        {{$det_esp_emp->cantidad}}
                                        <input type="hidden" class="td_ramos_x_caja_{{$x+1}} input_ramos_x_caja_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->cantidad}}">
                                    </td>
                                    @if($item->id_especificacion != $anterior)
                                        <td id="td_total_ramos_{{$x+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
                                            class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                        </td>
                                    @endif
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                        {{$det_esp_emp->tallos_x_ramos}}
                                    </td>
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                        @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                                            {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                                        @endif
                                    </td>
                                    <td id="td_precio_variedad_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" >
                                        @if((getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != ''))
                                            <select name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                                    ondblclick="cambiar_input_precio('{{$det_esp_emp->id_detalle_especificacionempaque}}','{{($x+1)}}','{{$b}}')"
                                                    id="precio_{{($x+1)}}_{{$b}}" style="background-color: beige; width: 100%" onchange="calcular_precio_pedido()"
                                                    class="precio_{{($x+1)}} form-control" required>
                                                @foreach(explode('|',getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $precio)
                                                    <option value="{{$precio}}">{{$precio}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="number"
                                                   name="precio_{{($x+1)}}" id="precio_{{($x+1)}}_{{$b}}" class="form-control text-center precio_{{($x+1)}} form-control"
                                                   style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" onkeypress="setValueInput(this,0,true)" value="0" required>
                                        @endif
                                    </td>
                                    @if($item->id_especificacion != $anterior)
                                        <td id="td_precio_especificacion_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                            rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                            <select name="id_agencia_carga_{{$item->id_especificacion}}" id="id_agencia_carga_{{$x+1}}"
                                                    class="text-center form-control" style="border: none; width: 100%">
                                                @foreach($agenciasCarga as $agencia)
                                                    <option value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        @foreach($datos_exportacion as $de)
                                            <td rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}"
                                                style="border-color: #9d9d9d; vertical-align: middle">
                                                <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$x+1}}" id="input_{{strtoupper($de->nombre)}}_{{$x+1}}" class="form-control" style="border: none">
                                                <input type="hidden" name="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" id="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" value="{{$de->id_dato_exportacion}}">
                                            </td>
                                        @endforeach
                                        <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                            rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                            <button type="button" class="btn btn-xs btn-primary" onclick="duplicar_especificacion('{{$item->id_especificacion}}','{{$x+1}}')">
                                                <i class="fa fa-fw fa-copy"></i>
                                            </button>
                                        </td>
                                    @endif
                            </tr>
                            @php $b++; $anterior = $item->id_especificacion; @endphp
                        @endforeach
                    @endforeach
                @endforeach
            @else
                <tr id="">
                    <td colspan="14">
                        <div class="alert alert-warning text-center">
                            <p style="font-size: 11pt;"> Este usuario no posee especificaciones asignadas </p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>


