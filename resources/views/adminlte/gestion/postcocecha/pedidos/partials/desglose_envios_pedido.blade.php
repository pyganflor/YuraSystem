<div id="table_envios" style="margin-top: 20px">
    {{--<table width="100%" class="table table-responsive table-bordered"
           style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_content_envios_facturar">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                ENVIO N#
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                FECHA DE ENVíO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                CANTIDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
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
                style="border-color: #9d9d9d">
                CLIENTE
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                AGENCIA DE TRANSPORTE
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                TIPO AGENCIA
            </th>
        </tr>
        </thead>
        @foreach($data_envios as $key => $item)
            @php $esp = getDetalleEspecificacion($item[0]->id_especificacion);@endphp
            <tr onmouseover="$(this).css('background-color','#add8e6')"
                onmouseleave="$(this).css('background-color','')" class="" id="row_pedidos_">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand">
                    {{str_pad($key,9,"0",STR_PAD_LEFT)}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    {{\Carbon\Carbon::parse($item[0]->fecha_pedido)->format('d-m-Y')}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    @foreach($item as $i)
                        {{$i->cantidad}}
                    @endforeach
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $key => $e)

                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["variedad"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as  $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["calibre"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["caja"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["rxc"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["presentacion"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["txr"] == null ? "-" : $e["txr"] }}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["longitud"] == null ? "-" : $e["longitud"] }} {{($e["unidad_medida_longitud"] == null || $e["longitud"] == null) ? "" : $e["unidad_medida_longitud"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <li style="list-style:none">{{$item[0]->nombre_cl}}</li>
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    <li style="list-style:none"> {{$item[0]->at_nombre}}</li>
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    <li style="list-style:none"> @if($item[0]->tipo_agencia == 'A')
                            AÉREA
                        @elseif($item[0]->tipo_agencia == 'T')
                            TERRESTRE
                        @elseif($item[0]->tipo_agencia == 'M')
                            MARíTIMA
                        @endif
                    </li>
                </td>
            </tr>
        @endforeach
    </table>--}}
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
                style="border-color: #9d9d9d">
                AGENCIA DE CARGA
            </th>
            @foreach($datos_exportacion as $key => $de)
                <th class="th_datos_exportacion text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    id="th_datos_exportacion_{{$key+1}}" style="border-color: #9d9d9d;width: 80px;">
                    {{strtoupper($de->nombre)}}
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody id="tbody_inputs_pedidos">
        @php $anterior = ''; @endphp
        @foreach(getPedido($id_pedido)->detalles as $x =>$det_ped)
            @php $b=1; @endphp
            @foreach(getEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
                @foreach($esp_emp->detalles as $z => $det_esp_emp)
                    @php
                        $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                    @endphp
                    <tr style="border-top: {{$det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}" >
                        @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                            <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                                class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                <input disabled type="number" min="0" id="cantidad_piezas_{{($x+1)}}" style="border: none"
                                       onchange="calcular_precio_pedido(this)"
                                       name="cantidad_piezas_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}"
                                       class="text-center form-control cantidad_{{($x+1)}} input_cantidad" value="{{$det_ped->cantidad}}">
                                @if($x ==0)
                                    <input type="hidden" id="cant_esp" value="">
                                    <input type="hidden" id="cant_esp_fijas" value="">
                                @endif
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
                            {{isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad}}
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
                            <input type="number" name="precio_{{($x+1)}}" id="precio_{{($x+1)}}_{{$b}}" readonly class="form-control text-center precio_{{($x+1)}} form-control"
                                   style="background-color: beige; width: 100%;text-align: left" min="0" onchange="calcular_precio_pedido()" value="{{explode(";",explode('|',$det_ped->precio)[$b-1])[0]}}"  required>
                        </td>
                        @if($det_ped->cliente_especificacion->especificacion->id_especificacion != $anterior)
                            {{-- <td id="td_precio_especificacion_{{($x+1)}}" style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                             </td>--}}
                            <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                                rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->especificacion->id_especificacion)}}">
                                <select name="id_agencia_carga_{{$det_ped->cliente_especificacion->especificacion->id_especificacion}}" id="id_agencia_carga_{{$x+1}}"
                                        class="text-center form-control" style="border: none; width: 100%" required>
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
                        @endif
                    </tr>
                    @php
                        $anterior = $det_ped->cliente_especificacion->especificacion->id_especificacion;
                    @endphp
                    @php $b++ @endphp
                @endforeach
            @endforeach
            @php $anterior = ''; @endphp
        @endforeach
        </tbody>
    </table>
</div>
<script>
    $(function () {
        $('[data-toggle="popover"]').popover()
    });
</script>
