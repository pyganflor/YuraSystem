@php  $anterior = ''; @endphp
    @php $b=1 @endphp
    @foreach(getEspecificacion($id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
        @foreach($esp_emp->detalles as $z => $det_esp_emp)
            <tr style="border-top: {{$id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}"  class="tr_remove_{{$cant_esp+1}}">
                @if($id_especificacion != $anterior)
                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 15px; text-align:center"
                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}">
                        <input type="checkbox" class="seleccion_invidual"  name="seleccion_invidual" id="seleccion_invidual_{{($cant_esp+1)}}"
                               checked onclick="calcular_precio_pedido()">
                    </td>
                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle;width: 30px; text-align:center"
                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}" >
                        <input type="number" min="1" name="orden" class="orden" id="orden_{{($cant_esp+1)}}" style="border: none;text-align: center;width: 100%;">
                        <input type="hidden" min="1" name="codigo_presentacion_{{($cant_esp+1)}}" class="codigo_presentacion_{{($cant_esp+1)}}" id="codigo_presentacion_{{($cant_esp+1)}}" value="">
                        <input type="hidden" min="1" name="codigo_venture_{{($cant_esp+1)}}" class="codigo_venture_{{($cant_esp+1)}}" id="codigo_venture_{{($cant_esp+1)}}" value="">
                    </td>
                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle;width: 30px;"
                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}">
                        <input type="number" min="0" id="cantidad_piezas_{{($cant_esp+1)}}" style="border: none;height: 34px;width: 100%;" onkeyup="crear_orden_pedido(this)"  onchange="calcular_precio_pedido(this)"
                               name="cantidad_piezas_{{$id_especificacion}}" class="text-center cantidad_{{($cant_esp+1)}} input_cantidad" value="">
                        <input type="hidden" id="id_cliente_pedido_especificacion_{{($cant_esp+1)}}" value="{{getClienteEspecificacion($id_cliente,$id_especificacion)->id_cliente_pedido_especificacion}}">
                        <input type="hidden" id="cajas_mallas_{{($cant_esp+1)}}" name="cajas_mallas_{{($cant_esp+1)}}">
                    </td>
                @endif
                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                    {{$det_esp_emp->variedad->siglas}}
                    <input type="hidden" class="input_variedad_{{$cant_esp+1}}" id="id_variedad_{{$cant_esp+1}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 70px;" class="text-center td_calibre td_calibre_{{$cant_esp+1}} td_calibre_{{$cant_esp+1}}_{{$b}}">
                    <span>{{$det_esp_emp->clasificacion_ramo->nombre}}</span>{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                    <input type="hidden" id="id_detalle_especificacion_empaque_{{$cant_esp+1}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                    <input type="hidden" id="id_clasificacion_ramo_{{$cant_esp+1}}" name="id_clasificacion_ramo_{{$cant_esp+1}}" value="{{$det_esp_emp->clasificacion_ramo->id_clasificacion_ramo}}">
                    <input type="hidden" id="u_m_clasificacion_ramo_{{$cant_esp+1}}" name="u_m_clasificacion_ramo_{{$cant_esp+1}}" value="{{$det_esp_emp->clasificacion_ramo->unidad_medida->id_unidad_medida}}">
                </td>
                @if($z == 0)
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center" rowspan="{{count($esp_emp->detalles)}}">

                        <select id="empaque_{{$cant_esp+1}}" class="empaque_{{$cant_esp+1}} empaque" name="empaque_{{$cant_esp+1}}" style="border:none;width:100%;height: 34px;" onchange="cuenta_ramos(this)" >
                            @if($tipo_especificacion!=="O")
                                <option value="{{$esp_emp->empaque->id_empaque}}" >{{explode('|',$esp_emp->empaque->nombre)[0]}}</option>
                            @endif
                            @foreach($emp_tallos as $t)
                                <option value="{{$t->f_empaque}}">{{explode('|',$t->nombre)[0]}}</option>
                            @endforeach
                        </select>
                    </td>
                @endif
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:65px"
                    class="text-center td_presentacion_{{$cant_esp+1}} td_presentacion_{{$cant_esp+1}}_{{$b}}">
                    <span>{{$det_esp_emp->empaque_p->nombre}}</span>
                    <input type="hidden" id="input_presentacion_{{$cant_esp+1}}_{{$b}}" name="input_presentacion_{{$cant_esp+1}}_{{$b}}"
                           value="{{$det_esp_emp->empaque_p->nombre}}" class="input_presentacion_{{$cant_esp+1}}">
                </td>
                <td  style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center ramos_x_caja_{{$cant_esp+1}} ramos_x_caja_{{$cant_esp+1}}_{{$b}}">
                    <input type="number" min="0" id="ramos_x_caja_{{$cant_esp+1}}_{{$b}}"
                           value="{{$det_esp_emp->cantidad}}" style="width: 60px;text-align: center;border:none"
                           onchange="calcular_precio_pedido()" onkeyup="calcular_precio_pedido()"
                           class="input_ramos_x_caja_{{$cant_esp+1}} input_ramos_x_caja_{{$cant_esp+1}}_{{$b}}">
                    <input type="hidden" id="id_det_esp_{{$cant_esp+1}}_{{$b}}" class="id_det_esp_{{$cant_esp+1}}_{{$b}} id_det_esp_{{$cant_esp+1}}"
                           value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                    <input type="hidden" class="td_ramos_x_caja_{{$cant_esp+1}} td_ramos_x_caja_{{$cant_esp+1}}_{{$b}}"
                           value="{{$det_esp_emp->cantidad}}">
                    {{--<span>{{$det_esp_emp->cantidad}}</span>
                    <input type="hidden" class="td_ramos_x_caja_{{$cant_esp+1}} input_ramos_x_caja_{{$cant_esp+1}}_{{$b}}" value="{{$det_esp_emp->cantidad}}">--}}
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:40px"
                    class="{{$empaque !=="T" ? "hide" : "" }} td_tallos_x_malla td_tallos_x_malla_{{$cant_esp+1}} td_tallos_x_malla_{{$cant_esp+1}}_{{$b}}">
                    <input type="number" min="0" id="tallos_x_malla_{{$cant_esp+1}}_{{$b}}" name="tallos_x_malla_{{$cant_esp+1}}_{{$b}}"
                           class="text-center tallos_x_malla_{{$cant_esp+1}} tallos_x_malla_{{$cant_esp+1}}_{{$b}}" value="0"
                           onchange="calcular_precio_pedido(this)" style="border: none;width: 100%;height: 34px;" >
                    <input type="hidden" id="tallos_x_caja_{{$cant_esp+1}}_{{$b}}" name="tallos_x_caja_{{$cant_esp+1}}_{{$b}}"
                           class="text-center tallos_x_caja_{{$cant_esp+1}} tallos_x_caja_{{$cant_esp+1}}_{{$b}}">
                </td>
                @if($id_especificacion != $anterior)
                    <td id="td_total_ramos_{{$cant_esp+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}">
                        0
                    </td>
                @endif
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center td_tallos_x_ramo_{{$cant_esp+1}}_{{$b}} td_tallos_x_ramo_{{$cant_esp+1}}">
                    <span>
                         @if($tipo_especificacion!=="O")
                            {{$det_esp_emp->tallos_x_ramos}}
                        @else
                            <input type="text" id="input_tallos_{{$cant_esp+1}}" name="input_tallos_{{$cant_esp+1}}" class="input_tallos_{{$cant_esp+1}}"
                                   onkeyup="calcular_precio_pedido(this)" onchange="crear_orden_pedido(this)" value="0"
                                   style="width:100%;border:none;text-align:center;height: 34px;"
                                   title="Escribe la cantidad de tallos por malla">
                        @endif
                    </span>
                    <input id="tallos_x_ramo_{{$cant_esp+1}}_{{$b}}" name="tallos_x_ramo_{{$cant_esp+1}}_{{$b}}"
                           type="hidden"  value="{{$det_esp_emp->tallos_x_ramos}}" class="tallos_x_ramo_{{$cant_esp+1}}_{{$b}} tallos_x_ramo_{{$cant_esp+1}}">
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                        {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                        <input type="hidden"  id="longitud_ramo_{{$cant_esp+1}}_{{$b}}" name="" class="longitud_ramo_{{$cant_esp+1}}"
                               value="{{$det_esp_emp->longitud_ramo}}">
                        <input type="hidden" id="u_m_longitud_ramo_{{$cant_esp+1}}_{{$b}}" name="" class="u_m_longitud_ramo_{{$cant_esp+1}}"
                               value="{{$det_esp_emp->unidad_medida->id_unidad_medida}}">
                    @endif
                </td>
                <td id="td_precio_variedad_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($cant_esp+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" >
                    @if((getPrecioByClienteDetEspEmp($id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != ''))
                        <select name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                ondblclick="cambiar_input_precio('{{$det_esp_emp->id_detalle_especificacionempaque}}','{{($cant_esp+1)}}','{{$b}}')"
                                id="precio_{{($cant_esp+1)}}_{{$b}}" style="background-color: beige; width: 100%" onchange="calcular_precio_pedido()"
                                class="precio_{{($cant_esp+1)}} form-control" required>
                            @foreach(explode('|',getPrecioByClienteDetEspEmp($id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $precio)
                                <option value="{{$precio}}">{{$precio}}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="number"
                               name="precio_{{($cant_esp+1)}}" id="precio_{{($cant_esp+1)}}_{{$b}}" class="form-control text-center precio_{{($cant_esp+1)}} form-control"
                               style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" value="0" required>
                    @endif
                </td>
                @if($id_especificacion != $anterior)
                    <td id="td_precio_especificacion_{{($cant_esp+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                        rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}">
                        <select name="id_agencia_carga_{{$id_especificacion}}" id="id_agencia_carga_{{$cant_esp+1}}"
                                class="text-center" style="border: none; width: 100%;height: 34px;" required>
                            @foreach($agenciasCarga as $agencia)
                                <option value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                            @endforeach
                        </select>
                    </td>
                    @foreach($datos_exportacion as $de)
                        <td rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}"
                            style="border-color: #9d9d9d; vertical-align: middle">
                            <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$cant_esp+1}}" id="input_{{strtoupper($de->nombre)}}_{{$cant_esp+1}}" class="form-control" style="border: none">
                            <input type="hidden" name="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$cant_esp+1}}" id="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$cant_esp+1}}" value="{{$de->id_dato_exportacion}}">
                        </td>
                    @endforeach
                    <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                        rowspan="{{getCantidadDetallesByEspecificacion($id_especificacion)}}">
                        <button type="button" class="btn btn-xs btn-primary" onclick="duplicar_especificacion('{{$id_especificacion}}','{{$cant_esp+1}}')">
                            <i class="fa fa-fw fa-copy"></i>
                        </button>
                    </td>
                @endif
            </tr>
            @php
                $anterior = $id_especificacion;
            @endphp
            @php $b++ @endphp
        @endforeach
    @endforeach

