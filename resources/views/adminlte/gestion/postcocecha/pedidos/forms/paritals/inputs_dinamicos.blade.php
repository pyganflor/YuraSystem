    <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white" id="table_content_recepciones">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width: 15px;text-align:center"> </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width: 30px">
                ORDEN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width: 30px">
                PIEZAS
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                VARIEDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:40px">
                PESO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:75px">
                CAJA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:65px">
                PRESENTACIÓN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:45px">
                RAMO X CAJA
            </th>
            <th class="text-center hide th_tallo_x_malla table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:45px">
                TALLOS X MALLA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                TOTAL RAMOS
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:35px">
                TALLOS X RAMO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:35px">
                LONGITUD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width:60px">
                PRECIO X VARIEDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width:70px">
                PRECIO X ESPECIFICACIÓN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d;width:75px">
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
                                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 15px; text-align:center"
                                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                        <input type="checkbox" class="seleccion_invidual"  name="seleccion_invidual" id="seleccion_invidual_{{($x+1)}}"
                                               checked onclick="calcular_precio_pedido()">
                                    </td>
                                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle;width: 30px; text-align:center"
                                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}" >
                                        <input type="number" min="1" name="orden" class="orden" id="orden_{{($x+1)}}"
                                               style="border: none;text-align: center; width: 100%;height:34px">
                                        <input type="hidden" min="1" name="codigo_presentacion_{{($x+1)}}" class="codigo_presentacion_{{($x+1)}}" id="codigo_presentacion_{{($x+1)}}" value="">
                                        <input type="hidden" min="1" name="codigo_venture_{{($x+1)}}" class="codigo_venture_{{($x+1)}}" id="codigo_venture_{{($x+1)}}" value="">
                                    </td>
                                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 30px;"
                                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                        <input type="number" min="0" id="cantidad_piezas_{{($x+1)}}" style="border: none;width:100%;height: 34px;"
                                               onkeyup="crear_orden_pedido(this)" onchange="calcular_precio_pedido(this), calcular_precio_pedido()"
                                               name="cantidad_piezas_{{$item->id_especificacion}}"  class="text-center cantidad_{{($x+1)}} input_cantidad" value="">
                                        <input type="hidden" id="id_cliente_pedido_especificacion_{{($x+1)}}" value="{{$item->id_cliente_pedido_especificacion}}">
                                        <input type="hidden" id="cajas_mallas_{{($x+1)}}" name="cajas_mallas_{{($x+1)}}">
                                    </td>
                                @endif
                                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                                        {{$det_esp_emp->variedad->siglas}}
                                        <input type="hidden" class="input_variedad_{{$x+1}}" id="id_variedad_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                                    </td>
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:40px" class="text-center td_calibre td_calibre_{{$x+1}}_{{$b}} td_calibre_{{$x+1}}">
                                        <span>{{$det_esp_emp->clasificacion_ramo->nombre}}</span>{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                        <input type="hidden" id="id_detalle_especificacion_empaque_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                        <input type="hidden" id="id_clasificacion_ramo_{{$x+1}}" name="id_clasificacion_ramo_{{$x+1}}" value="{{$det_esp_emp->clasificacion_ramo->id_clasificacion_ramo}}">
                                        <input type="hidden" id="u_m_clasificacion_ramo_{{$x+1}}" name="u_m_clasificacion_ramo_{{$x+1}}" value="{{$det_esp_emp->clasificacion_ramo->unidad_medida->id_unidad_medida}}">
                                    </td>
                                    @if($z == 0)
                                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:75px" class="text-center"  rowspan="{{count($esp_emp->detalles)}}">
                                            <select id="empaque_{{$x+1}}" class="empaque_{{$x+1}} empaque" name="empaque_{{$x+1}}" style="border:none;width:100%;height: 34px;" onchange="cuenta_ramos(this)" >
                                                <option value="{{$esp_emp->empaque->id_empaque}}" >{{explode('|',$esp_emp->empaque->nombre)[0]}}</option>
                                                @foreach($emp_tallos as $t)
                                                    <option value="{{$t->f_empaque}}">{{explode('|',$t->nombre)[0]}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:65px"
                                        class="text-center td_presentacion_{{$x+1}} td_presentacion_{{$x+1}}_{{$b}}">
                                        <span>{{$det_esp_emp->empaque_p->nombre}}</span>
                                        <input type="hidden" id="input_presentacion_{{$x+1}}_{{$b}}" name="input_presentacion_{{$x+1}}_{{$b}}"
                                               value="{{$det_esp_emp->empaque_p->nombre}}" class="input_presentacion_{{$x+1}}">
                                    </td>
                                    <td  style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:40px" class="text-center ramos_x_caja_{{$x+1}} ramos_x_caja_{{$x+1}}_{{$b}}">
                                        <input type="number" min="0" id="ramos_x_caja_{{$x+1}}_{{$b}}"
                                               value="{{$det_esp_emp->cantidad}}" style="width: 60px;text-align: center;border:none"
                                               onchange="calcular_precio_pedido()" onkeyup="calcular_precio_pedido()"
                                               class="input_ramos_x_caja_{{$x+1}} input_ramos_x_caja_{{$x+1}}_{{$b}}">
                                        <input type="hidden" id="id_det_esp_{{$x+1}}_{{$b}}" class="id_det_esp_{{$x+1}}_{{$b}} id_det_esp_{{$x+1}}"
                                               value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                        <input type="hidden" class="td_ramos_x_caja_{{$x+1}} td_ramos_x_caja_{{$x+1}}_{{$b}}"
                                                 value="{{$det_esp_emp->cantidad}}">
                                    </td>
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:40px"
                                        class="td_tallos_x_malla td_tallos_x_malla_{{$x+1}} td_tallos_x_malla_{{$x+1}}_{{$b}}
                                        {{(isset($det_ped->cliente_especificacion->especificacion->tipo) && $det_ped->cliente_especificacion->especificacion->tipo == "O") ? "" : "hide"}}">
                                        <input type="number" min="0" id="tallos_x_malla_{{$x+1}}_{{$b}}" name="tallos_x_malla_{{$x+1}}_{{$b}}"
                                               class="text-center tallos_x_malla_{{$x+1}} tallos_x_malla_{{$x+1}}_{{$b}}" value="0"
                                               onchange="calcular_precio_pedido(this)" style="border: none;width: 100%;height: 34px;" >
                                        <input type="hidden" id="tallos_x_caja_{{$x+1}}_{{$b}}" name="tallos_x_caja_{{$x+1}}_{{$b}}"
                                               class="text-center tallos_x_caja_{{$x+1}} tallos_x_caja_{{$x+1}}_{{$b}}" >
                                    </td>
                                    @if($item->id_especificacion != $anterior)
                                        <td id="td_total_ramos_{{$x+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 45px; "
                                            class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                            0
                                        </td>
                                    @endif
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:35px" class="text-center td_tallos_x_ramo_{{$x+1}}_{{$b}} td_tallos_x_ramo_{{$x+1}}">
                                        <span>{{$det_esp_emp->tallos_x_ramos}}</span>
                                        <input id="tallos_x_ramo_{{$x+1}}_{{$b}}" name="tallos_x_ramo_{{$x+1}}_{{$b}}"
                                               type="hidden"  value="{{$det_esp_emp->tallos_x_ramos}}" class="tallos_x_ramo_{{$x+1}}_{{$b}} tallos_x_ramo_{{$x+1}}">
                                    </td>
                                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:35px" class="text-center">
                                        @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                                            {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                                            <input type="hidden"  id="longitud_ramo_{{$x+1}}_{{$b}}" name="" class="longitud_ramo_{{$x+1}}"
                                                   value="{{$det_esp_emp->longitud_ramo}}">
                                            <input type="hidden" id="u_m_longitud_ramo_{{$x+1}}_{{$b}}" name="" class="u_m_longitud_ramo_{{$x+1}}"
                                                   value="{{$det_esp_emp->unidad_medida->id_unidad_medida}}">
                                        @endif
                                    </td>
                                    <td id="td_precio_variedad_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;width:60px" >
                                        @if((getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != ''))
                                            <select name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                                    ondblclick="cambiar_input_precio('{{$det_esp_emp->id_detalle_especificacionempaque}}','{{($x+1)}}','{{$b}}')"
                                                    id="precio_{{($x+1)}}_{{$b}}" style="background-color: beige; width: 100%;height: 34px;" onchange="calcular_precio_pedido()"
                                                    class="precio_{{($x+1)}} form-control" required>
                                                @foreach(explode('|',getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $precio)
                                                    <option value="{{$precio}}">{{$precio}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="number"
                                                   name="precio_{{($x+1)}}" id="precio_{{($x+1)}}_{{$b}}" class="form-control text-center precio_{{($x+1)}} form-control"
                                                   style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" value="0" required>
                                        @endif
                                    </td>
                                    @if($item->id_especificacion != $anterior)
                                        <td id="td_precio_especificacion_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;width:70px" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}"></td>
                                        <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle;width:75px"
                                            rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                            <select name="id_agencia_carga_{{$item->id_especificacion}}" id="id_agencia_carga_{{$x+1}}"
                                                    class="text-center" style="border: none; width:100%;height: 34px;" required>
                                                @foreach($agenciasCarga as $agencia)
                                                    <option value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        @foreach($datos_exportacion as $de)
                                            <td rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}"
                                                style="border-color: #9d9d9d; vertical-align: middle">
                                                <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$x+1}}" id="input_{{strtoupper($de->nombre)}}_{{$x+1}}" class="" style="border: none;width:100%;height:34px">
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
                    @php $anterior = ''; @endphp
                @endforeach
            @else
                <tr id="">
                    <td colspan="15">
                        <div class="alert alert-warning text-center">
                            <p style="font-size: 11pt;"> Este usuario no posee especificaciones asignadas </p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
