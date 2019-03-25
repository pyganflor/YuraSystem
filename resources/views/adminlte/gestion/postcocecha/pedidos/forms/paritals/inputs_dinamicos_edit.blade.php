<form id="form_add_pedido">
    <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white"
           id="table_content_recepciones">
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
            {{--ESPECIFICACIONES SOLICITADAS EN EL PEDIDO--}}
            @php $anterior = ''; @endphp
            @foreach(getPedido($id_pedido)->detalles as $x =>$det_ped)
                @php $b=1; @endphp
                @foreach(getEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
                    @foreach($esp_emp->detalles as $z => $det_esp_emp)
                        <tr style="border-top: {{$det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}" >
                            @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                    <input type="number" min="0" id="cantidad_piezas_{{($x+1)}}" style="border: none" onchange="calcular_precio_pedido(this)"
                                           name="cantidad_piezas_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" class="text-center form-control cantidad_{{($x+1)}} input_cantidad" value="{{$det_ped->cantidad}}">
                                    @if($x ==0) <input type="hidden" id="cant_esp" value="">
                                    <input type="hidden" id="cant_esp_fijas" value="">  @endif
                                    <input type="hidden" id="id_cliente_pedido_especificacion_{{($x+1)}}" value="{{$det_ped->cliente_especificacion->id_cliente_pedido_especificacion}}">
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
                                @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                    <td id="td_total_ramos_{{$x+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
                                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
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
                                    {{--@if((getPrecioByClienteDetEspEmp($det_ped->cliente_especificacion->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != ''))

                                        @php $arrPrecio = explode('|',$det_ped->precio); $option_extra = false; @endphp
                                        <select name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                                ondblclick="cambiar_input_precio('{{$det_esp_emp->id_detalle_especificacionempaque}}','{{($x+1)}}','{{$b}}')"
                                                id="precio_{{($x+1)}}_{{$b}}" style="background-color: beige; width: 100%" onchange="calcular_precio_pedido()"
                                                class="precio_{{($x+1)}} form-control" required>
                                            @foreach(explode('|',getPrecioByClienteDetEspEmp($det_ped->cliente_especificacion->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $l => $precio)
                                                @php
                                                    $selected ="";
                                                       foreach($arrPrecio as $p){
                                                           if( explode(";",$p)[0] == $precio){
                                                               $selected = "selected";
                                                           }
                                                       }
                                                @endphp
                                                <option {{$selected}} value="{{$precio}}">{{$precio}}</option>
                                            @endforeach
                                        </select>
                                    @else--}}
                                        <input type="number"
                                               name="precio_{{($x+1)}}" id="precio_{{($x+1)}}_{{$b}}" class="form-control text-center precio_{{($x+1)}} form-control"
                                               style="background-color: beige; width: 100%;text-align: left" min="0"
                                               onchange="calcular_precio_pedido()" value="{{explode(";",explode('|',$det_ped->precio)[$b-1])[0]}}" required>
                                    {{--@endif--}}
                                </td>
                                @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                    <td id="td_precio_especificacion_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                        rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                        <select name="id_agencia_carga_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" id="id_agencia_carga_{{$x+1}}"
                                                class="text-center form-control" style="border: none; width: 100%">
                                            @foreach($agenciasCarga as $agencia)
                                                <option {!! ($det_ped->id_agencia_carga == $agencia->id_agencia_carga) ? "selected" : ""!!} value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @foreach($datos_exportacion as $de)
                                        <td rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}"
                                            style="border-color: #9d9d9d; vertical-align: middle">
                                            <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$x+1}}" id="input_{{strtoupper($de->nombre)}}_{{$x+1}}" class="form-control" style="border: none"
                                                   value="{{isset(getDatosExportacion($det_ped->id_detalle_pedido,$de->id_dato_exportacion)->valor) ? getDatosExportacion($det_ped->id_detalle_pedido,$de->id_dato_exportacion)->valor : ""}}">
                                            <input type="hidden" name="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" id="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" value="{{$de->id_dato_exportacion}}">
                                        </td>
                                    @endforeach
                                    <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                        rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                        <button type="button" class="btn btn-xs btn-primary" onclick="duplicar_especificacion('{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}','{{$x+1}}')">
                                            <i class="fa fa-fw fa-copy"></i>
                                        </button>
                                    </td>
                                @endif
                        </tr>
                        @php
                            $anterior = $det_ped->cliente_especificacion->especificacion->id_especificacion;
                        @endphp
                        @php $b++ @endphp
                    @endforeach
                @endforeach
            @endforeach
            {{--FIN ESPECIFICACIONES SOLICITADAS EN EL PEDIDO--}}

            {{--ESPECIFICACIONES RESTANTES--}}
            @php $anterior_2 = ''; $cant_esp_creadas = count(getPedido($id_pedido)->detalles)+1; @endphp
            @foreach($especificaciones as $x => $item)
                @php $b=1;  @endphp
                @foreach(getEspecificacion($item->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
                    @foreach($esp_emp->detalles as $z => $det_esp_emp)
                        <tr style="border-top: {{$item->id_especificacion != $anterior_2 ? '2px solid #9d9d9d' : ''}}" >
                            @if($item->id_especificacion != $anterior_2)
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                    <input type="number" min="0" id="cantidad_piezas_{{($x+$cant_esp_creadas)}}" style="border: none" onchange="calcular_precio_pedido(this)"
                                           name="cantidad_piezas_{{$item->id_especificacion}}" class="text-center form-control cantidad_{{($x+$cant_esp_creadas)}} input_cantidad" value="">
                                    <input type="hidden" id="id_cliente_pedido_especificacion_{{($x+$cant_esp_creadas)}}" value="{{$item->id_cliente_pedido_especificacion}}">
                                </td>
                            @endif
                            <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                                {{$det_esp_emp->variedad->siglas}}
                                <input type="hidden" class="input_variedad_{{$x+$cant_esp_creadas}}" id="id_variedad_{{$x+$cant_esp_creadas}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 70px;" class="text-center">
                                {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                <input type="hidden" id="id_detalle_especificacion_empaque_{{$x+$cant_esp_creadas}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
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
                                <input type="hidden" class="td_ramos_x_caja_{{$x+$cant_esp_creadas}} input_ramos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}" value="{{$det_esp_emp->cantidad}}">
                            </td>
                            @if($item->id_especificacion != $anterior_2)
                                <td id="td_total_ramos_{{$x+$cant_esp_creadas}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
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
                            <td id="td_precio_variedad_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+$cant_esp_creadas)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" >
                                @if((getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != ''))
                                    <select name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                            ondblclick="cambiar_input_precio('{{$det_esp_emp->id_detalle_especificacionempaque}}','{{($x+$cant_esp_creadas)}}','{{$b}}')"
                                            id="precio_{{($x+$cant_esp_creadas)}}_{{$b}}" style="background-color: beige; width: 100%;text-align:center;" onchange="calcular_precio_pedido()"
                                            class="precio_{{($x+$cant_esp_creadas)}} form-control" required>
                                        @foreach(explode('|',getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $precio)
                                            <option value="{{$precio}}">{{$precio}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="number"
                                           name="precio_{{($x+$cant_esp_creadas)}}" id="precio_{{($x+$cant_esp_creadas)}}_{{$b}}"
                                           class="form-control text-center precio_{{($x+$cant_esp_creadas)}} form-control"
                                           style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" value="0" required>
                                @endif
                            </td>
                            @if($item->id_especificacion != $anterior_2)
                                <td id="td_precio_especificacion_{{($x+$cant_esp_creadas)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                    rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                    <select name="id_agencia_carga_{{$item->id_especificacion}}" id="id_agencia_carga_{{$x+$cant_esp_creadas}}"
                                            class="text-center form-control" style="border: none; width: 100%">
                                        @foreach($agenciasCarga as $agencia)
                                            <option value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @foreach($datos_exportacion as $de)
                                    <td rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}"
                                        style="border-color: #9d9d9d; vertical-align: middle">
                                        <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$x+$cant_esp_creadas}}" id="input_{{strtoupper($de->nombre)}}_{{$x+$cant_esp_creadas}}" class="form-control" style="border: none">
                                        <input type="hidden" name="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+$cant_esp_creadas}}" id="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+$cant_esp_creadas}}" value="{{$de->id_dato_exportacion}}">
                                    </td>
                                @endforeach
                                <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                    rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                    <button type="button" class="btn btn-xs btn-primary" onclick="duplicar_especificacion('{{$item->id_especificacion}}','{{$x+$cant_esp_creadas}}')">
                                        <i class="fa fa-fw fa-copy"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                        @php
                            $anterior_2 = $item->id_especificacion;
                        @endphp
                        @php $b++; @endphp
                    @endforeach
                @endforeach
            @endforeach
            {{--FIN ESPECIFICACIONES RESTANTES--}}
        </tbody>
    </table>
</form>
