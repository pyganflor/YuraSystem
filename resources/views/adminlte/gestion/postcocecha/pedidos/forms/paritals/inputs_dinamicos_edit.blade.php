    <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white"
           id="table_content_recepciones">
        <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"  style="border-color: #9d9d9d;width: 15px;text-align:center"></th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"  style="border-color: #9d9d9d;width: 30px">
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
                <th class="th_tallo_x_malla {{(isset(getPedido($id_pedido)->detalles[0]->cliente_especificacion->especificacion->tipo) && getPedido($id_pedido)->detalles[0]->cliente_especificacion->especificacion->tipo == "O") ? "" : "hide"}}
                    text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:45px">
                    TALLOS X MALLA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    TOTAL RAMOS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:45px">
                    TALLOS X RAMO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:60px">
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
                    <th class="th_datos_exportacion th_dato_exportacion_{{$de->id_dato_exportacion}} text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
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
                    {{--@php $empaque_edit = $esp_emp->especificacion->tipo @endphp--}}
                    @foreach($esp_emp->detalles as $z => $det_esp_emp)
                        @php $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque); @endphp
                        <tr style="border-top: {{$det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}" >
                            @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 15px; text-align:center"
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                    <input type="checkbox" class="seleccion_invidual no_edit" style="font-size: 14px;"  name="seleccion_invidual" id="seleccion_invidual_{{($x+1)}}"
                                           checked onclick="calcular_precio_pedido()">
                                    <input type="hidden" id="id_det_ped_{{$x+1}}" name="id_det_ped_{{$x+1}}" value="{{$det_ped->id_detalle_pedido}}">
                                </td>
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 30px; text-align:center"
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}" >
                                    <input type="number" min="1" name="orden" class="orden no_edit" id="orden_{{($x+1)}}" style="border: none;text-align: center;width: 100%" value="{{$det_ped->orden}}">
                                    <input type="hidden" name="codigo_presentacion_{{($x+1)}}" class="codigo_presentacion_{{($x+1)}}" id="codigo_presentacion_{{($x+1)}}"
                                           value="{{$det_esp_emp->variedad->planta->id_planta."|".$det_esp_emp->variedad->id_variedad."|".$det_esp_emp->clasificacion_ramo->id_clasificacion_ramo."|".$det_esp_emp->clasificacion_ramo->unidad_medida->id_unidad_medida."|"."tallos_x_malla"."|".$det_esp_emp->longitud_ramo."|".$det_esp_emp->unidad_medida->id_unidad_medida}}">
                                    <input type="hidden" name="codigo_venture_{{($x+1)}}" class="codigo_venture_{{($x+1)}}" id="codigo_venture_{{($x+1)}}"
                                           value="{{getCodigoVenturePresentacion($det_esp_emp->variedad->planta->id_planta, $det_esp_emp->variedad->id_variedad, $det_esp_emp->clasificacion_ramo->id_clasificacion_ramo, $det_esp_emp->clasificacion_ramo->unidad_medida->id_unidad_medida, $det_esp_emp->tallos_x_ramos, $det_esp_emp->longitud_ramo, $det_esp_emp->unidad_medida->id_unidad_medida, getPedido($id_pedido)->id_configuracion_empresa,getPedido($id_pedido)->detalles[0]->cliente_especificacion->especificacion->tipo)}}">
                                </td>
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 30px;"
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                    <input type="number" min="0" id="cantidad_piezas_{{($x+1)}}" style="border: none;height: 34px;" onkeyup="crear_orden_pedido(this)" onchange="calcular_precio_pedido(this)"
                                           name="cantidad_piezas_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" class="text-center cantidad_{{($x+1)}} input_cantidad no_edit"
                                           value="{{isset($det_ped->data_tallos->mallas) ? $det_ped->data_tallos->mallas : $det_ped->cantidad}}">
                                    @if($x ==0)
                                        <input type="hidden" id="cant_esp" value="">
                                        <input type="hidden" id="cant_esp_fijas" value="">
                                    @endif
                                    <input type="hidden" id="id_cliente_pedido_especificacion_{{($x+1)}}" value="{{$det_ped->cliente_especificacion->id_cliente_pedido_especificacion}}">
                                    <input type="hidden" id="cajas_mallas_{{($x+1)}}" name="cajas_mallas_{{($x+1)}}">
                                </td>
                            @endif
                            <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                                {{$det_esp_emp->variedad->siglas}}
                                <input type="hidden" class="input_variedad_{{$x+1}}" id="id_variedad_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 70px;" class="text-center td_calibre_{{$x+1}}_{{$b}} td_calibre_{{$x+1}} ">
                                <span>{{$det_esp_emp->clasificacion_ramo->nombre}}</span>{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                <input type="hidden" id="id_detalle_especificacion_empaque_{{$x+1}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                <input type="hidden" id="id_clasificacion_ramo_{{$x+1}}" name="id_clasificacion_ramo_{{$x+1}}" value="{{$det_esp_emp->clasificacion_ramo->id_clasificacion_ramo}}">
                                <input type="hidden" id="u_m_clasificacion_ramo_{{$x+1}}" name="u_m_clasificacion_ramo_{{$x+1}}" value="{{$det_esp_emp->clasificacion_ramo->unidad_medida->id_unidad_medida}}">
                            </td>
                            @if($z == 0)
                                 <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center"  rowspan="{{count($esp_emp->detalles)}}">
                                     <select id="empaque_{{$x+1}}" class="empaque_{{$x+1}} empaque" name="empaque_{{$x+1}}" style="width: 100%;border: none;text-align: center;height: 34px;" onchange="cuenta_ramos(this)" >
                                         @if($esp_emp->especificacion->tipo != "O")
                                            <option value="{{$esp_emp->empaque->id_empaque}}" >{{explode('|',$esp_emp->empaque->nombre)[0]}}</option>
                                         @endif
                                         @isset($emp_tallos)
                                             @foreach($emp_tallos as $eT)
                                                <option {{$esp_emp->especificacion->tipo == "O" ? "selected" : ""}} value="{{$eT->f_empaque}}">{{explode('|',$eT->nombre)[0]}}</option>
                                             @endforeach
                                         @endisset
                                     </select>
                                </td>
                            @endif
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center td_presentacion_{{$x+1}}_{{$b}} td_presentacion_{{$x+1}}">
                                {{$det_esp_emp->empaque_p->nombre}}
                                <input type="hidden" id="input_presentacion_{{$x+1}}_{{$b}}" name="input_presentacion_{{$x+1}}_{{$b}}"
                                       value="{{$det_esp_emp->empaque_p->nombre}}" class="input_presentacion_{{$x+1}}">
                            </td>
                            <td  style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center ramos_x_caja_{{$x+1}} ramos_x_caja_{{$x+1}}_{{$b}}">

                                <input type="number" min="0" id="ramos_x_caja_{{$x+1}}_{{$b}}"
                                       value="{{isset($det_ped->data_tallos->ramos_x_caja)
                                                ? $det_ped->data_tallos->ramos_x_caja
                                                : (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad)}}"
                                       style="width: 60px;text-align: center;border:none"
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
                                       class="text-center tallos_x_malla_{{$x+1}} tallos_x_malla_{{$x+1}}_{{$b}}" value="{{isset($det_ped->data_tallos->tallos_x_malla) ? $det_ped->data_tallos->tallos_x_malla : ""}}"
                                       style="border: none;width: 100%;height: 34px;">
                                <input type="hidden" id="tallos_x_caja_{{$x+1}}_{{$b}}" name="tallos_x_caja_{{$x+1}}_{{$b}}"
                                       class="text-center tallos_x_caja_{{$x+1}} tallos_x_caja_{{$x+1}}_{{$b}}" >
                            </td>
                            @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                    <td id="td_total_ramos_{{$x+1}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
                                        class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                        {{isset($det_ped->cliente_especificacion->especificacion->ramos_x_caja) ? ($det_ped->cliente_especificacion->especificacion->ramos_x_caja*$det_ped->cantidad) : 0}}
                                    </td>
                            @endif
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center td_tallos_x_ramo_{{$x+1}}_{{$b}} td_tallos_x_ramo_{{$x+1}}">
                                <span>
                                    @if(isset($det_ped->data_tallos->tallos_x_ramo))
                                        <input type="text" id="input_tallos_{{$x+1}}" name="input_tallos_{{$x+1}}_{{$b}}" class="input_tallos_{{$x+1}} input_tallos_{{$x+1}}_{{$b}}"
                                               onkeyup="calcular_precio_pedido(this)" onchange="crear_orden_pedido(this)"
                                               value="{{$det_ped->data_tallos->tallos_x_ramo}}" style="width:100%;border:none;text-align:center;height: 34px;"
                                               title="Escribe la cantidad de tallos por malla">
                                    @else
                                        {{$det_esp_emp->tallos_x_ramos}}
                                    @endif
                                </span>
                                <input id="tallos_x_ramo_{{$x+1}}_{{$b}}" name="tallos_x_ramo_{{$x+1}}_{{$b}}"
                                       type="hidden" value="{{$det_esp_emp->tallos_x_ramos}}" class="tallos_x_ramo_{{$x+1}}_{{$b}} tallos_x_ramo_{{$x+1}}">
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                                   {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                                    <input type="hidden" id="longitud_ramo_{{$x+1}}_{{$b}}" name="" class="longitud_ramo_{{$x+1}}"
                                           value="{{$det_esp_emp->longitud_ramo}}">
                                    <input type="hidden" id="u_m_longitud_ramo_{{$x+1}}_{{$b}}" name="" class="u_m_longitud_ramo_{{$x+1}}"
                                           value="{{$det_esp_emp->unidad_medida->id_unidad_medida}}">
                                @endif
                            </td>
                            <td id="td_precio_variedad_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" >
                                    <input type="number" name="precio_{{($x+1)}}" id="precio_{{($x+1)}}_{{$b}}" class="text-center precio_{{($x+1)}} form-control no_edit"
                                           style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" value="{{explode(";",explode('|',$det_ped->precio)[$b-1])[0]}}"  required>
                                </td>
                            @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                                <td id="td_precio_especificacion_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}"></td>
                                <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                   rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                   <select name="id_agencia_carga_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" id="id_agencia_carga_{{$x+1}}"
                                            class="text-center agencia_carga" style="border: none; width: 100%" onchange="agencia_selected(this)" required>
                                       @foreach($agenciasCarga as $agencia)
                                           <option {!! ($det_ped->id_agencia_carga == $agencia->id_agencia_carga) ? "selected" : ""!!} value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                       @endforeach
                                   </select>
                                </td>
                                @foreach($datos_exportacion as $de)
                                   <td rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}" style="border-color: #9d9d9d; vertical-align: middle;">
                                       <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$x+1}}" id="input_{{strtoupper($de->nombre)}}_{{$x+1}}" class="" style="border: none;height:34px"
                                              value="{{isset(getDatosExportacion($det_ped->id_detalle_pedido,$de->id_dato_exportacion)->valor) ? getDatosExportacion($det_ped->id_detalle_pedido,$de->id_dato_exportacion)->valor : ""}}">
                                       <input type="hidden" name="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" id="id_dato_exportacion_{{strtoupper($de->nombre)}}_{{$x+1}}" value="{{$de->id_dato_exportacion}}">
                                   </td>
                                @endforeach
                                <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                        rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                    @if($x == 0 && isset(getPedido($id_pedido)->envios[0]->comprobante) && getPedido($id_pedido)->envios[0]->comprobante->estado == 5 || isset(getPedido($id_pedido)->envios[0]->comprobante->integrado) && getPedido($id_pedido)->envios[0]->comprobante->integrado)
                                        <button type="button" class="btn btn-xs btn-success" onclick="store_especificacion_pedido('{{$det_ped->id_agencia_carga}}','{{$id_pedido}}')" title="Actualizar datos">
                                            <i class="fa fa-floppy-o" ></i> Actualizar
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-xs btn-primary" onclick="duplicar_especificacion('{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}','{{$x+1}}')">
                                            <i class="fa fa-fw fa-copy"></i>
                                        </button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @php
                            $anterior = $det_ped->cliente_especificacion->especificacion->id_especificacion;
                            $b++;
                        @endphp
                    @endforeach
                @endforeach
                @php $anterior = ''; @endphp
            @endforeach
            {{--FIN ESPECIFICACIONES SOLICITADAS EN EL PEDIDO--}}

            {{--ESPECIFICACIONES RESTANTES--}}
            @php $anterior_2 = ''; $cant_esp_creadas = count(getPedido($id_pedido)->detalles)+1; @endphp
            @foreach($especificaciones_restante as $x => $item)
                @php $b=1;  @endphp
                @foreach(getEspecificacion($item->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
                    @foreach($esp_emp->detalles as $z => $det_esp_emp)
                        <tr style="border-top: {{$item->id_especificacion != $anterior_2 ? '2px solid #9d9d9d' : ''}}" >
                            @if($item->id_especificacion != $anterior_2)
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 15px; text-align:center"
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                    <input type="checkbox" class="seleccion_invidual"  name="seleccion_invidual" id="seleccion_invidual_{{($x+$cant_esp_creadas)}}"
                                           checked onclick="calcular_precio_pedido()">
                                </td>
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 30px; text-align:center"
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}" >
                                    <input type="number" min="1" name="orden" class="orden" id="orden_{{($x+$cant_esp_creadas)}}" style="border: none;text-align: center;width: 100%">
                                    <input type="hidden" min="1" name="codigo_presentacion_{{($x+$cant_esp_creadas)}}" class="codigo_presentacion_{{($x+$cant_esp_creadas)}}" id="codigo_presentacion_{{($x+$cant_esp_creadas)}}" value="">
                                    <input type="hidden" min="1" name="codigo_venture_{{($x+$cant_esp_creadas)}}" class="codigo_venture_{{($x+$cant_esp_creadas)}}" id="codigo_venture_{{($x+$cant_esp_creadas)}}" value="">
                                </td>
                                <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 30px;"
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                    <input type="number" min="0" id="cantidad_piezas_{{($x+$cant_esp_creadas)}}" style="border: none;height: 34px;" onkeyup="crear_orden_pedido(this)" onchange="calcular_precio_pedido(this)"
                                           name="cantidad_piezas_{{$item->id_especificacion}}" class="text-center  cantidad_{{($x+$cant_esp_creadas)}} input_cantidad" value="">
                                    <input type="hidden" id="id_cliente_pedido_especificacion_{{($x+$cant_esp_creadas)}}" value="{{$item->id_cliente_pedido_especificacion}}">
                                    <input type="hidden" id="cajas_mallas_{{($x+$cant_esp_creadas)}}" name="cajas_mallas_{{($x+$cant_esp_creadas)}}">
                                </td>
                            @endif
                            <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 60px;"  class="text-center">
                                {{$det_esp_emp->variedad->siglas}}
                                <input type="hidden" class="input_variedad_{{$x+$cant_esp_creadas}}" id="id_variedad_{{$x+$cant_esp_creadas}}_{{$b}}" value="{{$det_esp_emp->variedad->id_variedad}}">
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 70px;" class="text-center td_calibre td_calibre_{{$x+$cant_esp_creadas}}_{{$b}} td_calibre_{{$x+$cant_esp_creadas}}">
                                <span>{{$det_esp_emp->clasificacion_ramo->nombre}}</span>{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                <input type="hidden" id="id_detalle_especificacion_empaque_{{$x+$cant_esp_creadas}}_{{$b}}" value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                <input type="hidden" id="id_clasificacion_ramo_{{$x+$cant_esp_creadas}}" name="id_clasificacion_ramo_{{$x+$cant_esp_creadas}}" value="{{$det_esp_emp->clasificacion_ramo->id_clasificacion_ramo}}">
                                <input type="hidden" id="u_m_clasificacion_ramo_{{$x+$cant_esp_creadas}}" name="u_m_clasificacion_ramo_{{$x+$cant_esp_creadas}}" value="{{$det_esp_emp->clasificacion_ramo->unidad_medida->id_unidad_medida}}">
                            </td>
                            @if($z == 0)
                                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center"
                                    rowspan="{{count($esp_emp->detalles)}}">
                                    <select id="empaque_{{$x+$cant_esp_creadas}}" class="empaque_{{$x+$cant_esp_creadas}}" name="empaque_{{$x+$cant_esp_creadas}}"
                                            style="width: 100%;border: none;text-align: center;height: 34px;background:transparent" onchange="cuenta_ramos(this)" >
                                        {{--@if(isset($empaque_edit) && $empaque_edit != "O")--}}
                                            <option value="{{$esp_emp->empaque->id_empaque}}" >{{explode('|',$esp_emp->empaque->nombre)[0]}}</option>
                                        {{--@endif--}}
                                        @isset($emp_tallos)
                                            @foreach($emp_tallos as $eT)
                                                <option value="{{$eT->f_empaque}}">{{explode('|',$eT->nombre)[0]}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </td>
                            @endif
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center td_presentacion_{{$x+$cant_esp_creadas}}_{{$b}} td_presentacion_{{$x+$cant_esp_creadas}}">
                                <span>{{$det_esp_emp->empaque_p->nombre}}</span>
                                <input type="hidden" id="input_presentacion_{{$x+$cant_esp_creadas}}_{{$b}}" name="input_presentacion_{{$x+$cant_esp_creadas}}_{{$b}}"
                                       value="{{$det_esp_emp->empaque_p->nombre}}" class="input_presentacion_{{$x+$cant_esp_creadas}}">
                            </td>
                            <td  style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center ramos_x_caja_{{$x+$cant_esp_creadas}} ramos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}">
                                <input type="number" min="0" id="ramos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}"
                                       value="{{$det_esp_emp->cantidad}}" style="width: 60px;text-align: center;border:none"
                                       onchange="calcular_precio_pedido()" onkeyup="calcular_precio_pedido()"
                                       class="input_ramos_x_caja_{{$x+$cant_esp_creadas}} input_ramos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}">
                                <input type="hidden" id="id_det_esp_{{$x+$cant_esp_creadas}}_{{$b}}" class="id_det_esp_{{$x+$cant_esp_creadas}}_{{$b}} id_det_esp_{{$x+$cant_esp_creadas}}"
                                       value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                {{--<span>{{$det_esp_emp->cantidad}}</span>--}}
                                <input type="hidden" class="td_ramos_x_caja_{{$x+$cant_esp_creadas}} td_ramos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}"
                                       value="{{$det_esp_emp->cantidad}}">
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width:40px"
                                class="td_tallos_x_malla td_tallos_x_malla_{{$x+$cant_esp_creadas}} td_tallos_x_malla_{{$x+$cant_esp_creadas}}_{{$b}}
                                {{(isset($det_ped->cliente_especificacion->especificacion->tipo) && $det_ped->cliente_especificacion->especificacion->tipo == "O") ? "" : "hide"}}">
                                <input type="number" min="0" id="tallos_x_malla_{{$x+$cant_esp_creadas}}_{{$b}}" name="tallos_x_malla_{{$x+$cant_esp_creadas}}_{{$b}}"
                                       class="text-center tallos_x_malla_{{$x+$cant_esp_creadas}} tallos_x_malla_{{$x+$cant_esp_creadas}}_{{$b}}" value="0"
                                       onchange="calcular_precio_pedido(this)" style="border: none;width: 100%;height: 34px;" >
                                <input type="hidden" id="tallos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}" name="tallos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}"
                                       class="text-center tallos_x_caja_{{$x+$cant_esp_creadas}} tallos_x_caja_{{$x+$cant_esp_creadas}}_{{$b}}" >
                            </td>
                            @if($item->id_especificacion != $anterior_2)
                                <td id="td_total_ramos_{{$x+$cant_esp_creadas}}" style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 70px; "
                                    class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                    0
                                </td>
                            @endif
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center td_tallos_x_ramo_{{$x+$cant_esp_creadas}}_{{$b}} td_tallos_x_ramo_{{$x+$cant_esp_creadas}}">
                                <span>
                                    @if(getPedido($id_pedido)->detalles[0]->cliente_especificacion->especificacion->tipo == "O")
                                        <input type="text" id="input_tallos_{{$x+1}}" name="input_tallos_{{$x+1}}_{{$b}}" class="input_tallos_{{$x+1}}_{{$b}}"
                                               onkeyup="calcular_precio_pedido(this)" onchange="crear_orden_pedido(this)"
                                               value="25" style="width:100%;border:none;text-align:center;height: 34px;"
                                               title="Escribe la cantidad de tallos por malla">
                                    @else
                                        {{$det_esp_emp->tallos_x_ramos}}
                                    @endif
                                </span>
                                <input type="hidden" id="tallos_x_ramo_{{$x+$cant_esp_creadas}}_{{$b}}" name="tallos_x_ramo_{{$x+$cant_esp_creadas}}_{{$b}}"
                                       value="{{$det_esp_emp->tallos_x_ramos}}" class="tallos_x_ramo_{{$x+$cant_esp_creadas}}_{{$b}} tallos_x_ramo_{{$x+$cant_esp_creadas}}">
                            </td>
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                                @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                                    {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                                    <input type="hidden" id="longitud_ramo_{{$x+$cant_esp_creadas}}_{{$b}}" name="" class="longitud_ramo_{{$x+$cant_esp_creadas}}"
                                           value="{{$det_esp_emp->longitud_ramo}}">
                                    <input type="hidden" id="u_m_longitud_ramo_{{$x+$cant_esp_creadas}}_{{$b}}" name="" class="u_m_longitud_ramo_{{$x+$cant_esp_creadas}}"
                                           value="{{$det_esp_emp->unidad_medida->id_unidad_medida}}">
                                @endif
                            </td>
                            <td id="td_precio_variedad_{{$det_esp_emp->id_detalle_especificacionempaque}}_{{($x+$cant_esp_creadas)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" >
                                @if((getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != ''))
                                    <select name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                            ondblclick="cambiar_input_precio('{{$det_esp_emp->id_detalle_especificacionempaque}}','{{($x+$cant_esp_creadas)}}','{{$b}}')"
                                            id="precio_{{($x+$cant_esp_creadas)}}_{{$b}}" style="background-color: beige; width: 100%;height: 34px;text-align:center;" onchange="calcular_precio_pedido()"
                                            class="precio_{{($x+$cant_esp_creadas)}} form-control no_edit" required>
                                        @foreach(explode('|',getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $precio)
                                            <option value="{{$precio}}">{{$precio}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="number"
                                           name="precio_{{($x+$cant_esp_creadas)}}" id="precio_{{($x+$cant_esp_creadas)}}_{{$b}}"
                                           class="text-center precio_{{($x+$cant_esp_creadas)}} form-control  no_edit"
                                           style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" value="0" required>
                                @endif
                            </td>
                            @if($item->id_especificacion != $anterior_2)
                                <td id="td_precio_especificacion_{{($x+$cant_esp_creadas)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                    rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                                    <select name="id_agencia_carga_{{$item->id_especificacion}}" id="id_agencia_carga_{{$x+$cant_esp_creadas}}"
                                            class="text-center agencia_carga" style="border: none; width: 100%;height: 34px;background:transparent" >
                                        @foreach($agenciasCarga as $agencia)
                                            <option value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @foreach($datos_exportacion as $de)
                                    <td rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}"
                                        style="border-color: #9d9d9d; vertical-align: middle">
                                        <input type="text" name="input_{{strtoupper($de->nombre)}}_{{$x+$cant_esp_creadas}}" id="input_{{strtoupper($de->nombre)}}_{{$x+$cant_esp_creadas}}" class="" style="border: none;height:34px">
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
                        @php $b++; $anterior_2 = $item->id_especificacion; @endphp
                    @endforeach
                @endforeach
            @endforeach
            {{--FIN ESPECIFICACIONES RESTANTES--}}
        </tbody>
    </table>
    @if(isset(getPedido($id_pedido)->envios[0]->comprobante) && getPedido($id_pedido)->envios[0]->comprobante->estado == 5 )
        <script>
            $.each($(".modal-content input.no_edit"),function (i,j) {
                $(j).attr('disabled', true);
            });
            $.each($(".modal-content select.no_edit"),function (i,j) {
                $(j).attr('disabled', true);
            });
            function agencia_selected(select){
                $.each($(".agencia_carga option[value='"+select.value+"']"),function(i,j){
                    $(j).removeAttr('selected');
                    $(j).attr('selected', true);
                });
            }
        </script>
    @endif
    <script>
        $(function () {
            calibre_estandar = parseInt($("#calibre_estandar").val());
            ramos_x_caja_conf_empresa = parseInt($("#ramos_x_caja_conf_empresa").val());
            $.each($(".input_cantidad"),function (i) {
                $.each($("td.td_calibre_"+(i+1)),function(k,l) {
                    calibre = parseInt($(l).find("span")[0].innerText);
                    calibre_actual = parseInt($("td.td_calibre_"+(i+1)+"_"+(k+1)+" span").html());
                    calibre_promedio = calibre_actual / calibre_estandar;
                    ramos_x_caja = ramos_x_caja_conf_empresa / calibre_promedio;
                    @if(getPedido($id_pedido)->detalles[0]->cliente_especificacion->especificacion->tipo === "O")
                        $("td.ramos_x_caja_"+(i+1)+"_"+(k + 1)+" span").html(ramos_x_caja.toFixed(0));
                    @endif
                });
            });

            $("#cambia_tinturado").removeClass('hide');
        });

        function cambia_tipo_pedido(){ ///CAMBIA UN PEDIDO NORMAL A UN PEDIDO TINTURADO

            texto="<div class='alert alert-warning text-center' style='margin:0'>Esta seguro de cambiar el pedido normal a uno tinturado.?</div>";

            modal_quest('modal_cambia_tipo_pedido', texto, 'Cambiar pedido', true, false, '40%', function () {

                $.LoadingOverlay('show');
                datos = {
                    _token: '{{csrf_token()}}',
                    id_pedido : '{{$id_pedido}}'
                };
                post_jquery('pedidos/cambia_tipo_pedido', datos, function () {
                    cerrar_modals()
                    editar_pedido_tinturado('{{$id_pedido}}', 0)
                });
                $.LoadingOverlay('hide');

            })


        }

       /*data = {
            id_variedad : $(".input_variedad_"+id).val(),
            id_clasificacion_ramo : $("#id_clasificacion_ramo_"+id).val(),
            id_u_m_clasificacion_ramo : $("#u_m_clasificacion_ramo_"+id).val(),
            tallos_x_ramos : $(".tallos_x_ramo_"+id).val(),
            longitud_ramo : $(".longitud_ramo_"+id).val(),
            id_u_m_logitud_ramo : $(".u_m_longitud_ramo_"+id).val(),
            id_configuracion_empresa : $("#id_configuracion_empresa").val(),
            tallos_x_malla : "tallos_x_malla"
        };*/
        /*$.get('clientes/buscar_codigo_venture', data, function (retorno) {
            if(retorno['presentacion_venture'] === "" || retorno['codigo_venture'] === ""){
                $("#error_codigo_venture").html('<b>La presentación base transformada a '+$("select.empaque_"+id+" option:selected").text()+' no esta vinculada aún con su código del venture de la empresa '+$("select#id_configuracion_empresa option:selected").text()+"</b>");
                $("button.store_pedido_normal").attr('disabled',true);

            }else{
                $("#codigo_presentacion_"+id).val(retorno['presentacion_venture']);
                $("#codigo_venture_"+id).val(retorno['codigo_venture']);
                $("button.store_pedido_normal").removeAttr('disabled');
                $("#error_codigo_venture").html("");
            }
        }).always(function () {
            $.LoadingOverlay('hide');
        });*/
    </script>

