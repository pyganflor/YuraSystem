@if(count($especificaciones) >0)
    @php
        $anterior = '';
    @endphp
    @foreach($especificaciones as $x => $item)
        @foreach(getEspecificacion($item->id_especificacion)->especificacionesEmpaque as $y => $esp_emp)
            @foreach($esp_emp->detalles as $z => $det_esp_emp)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    style="border-top: {{$item->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}">
                    @if($item->id_especificacion != $anterior)
                        <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                            class="text-center" rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                            <input type="number" id="cantidad_piezas_{{$item->id_especificacion}}" style="border: none"
                                   name="cantidad_piezas_{{$item->id_especificacion}}" class="text-center" value="">
                        </td>
                    @endif
                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                        class="text-center">
                        FULES
                    </td>
                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                        class="text-center">
                        {{$det_esp_emp->variedad->siglas}}
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
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
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        {{$det_esp_emp->cantidad}}
                    </td>
                    <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                        class="text-center">
                        RAMOS TOTALES
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        {{$det_esp_emp->tallos_x_ramo}}
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                        @if($det_esp_emp->longitud_ramo != '' && $det_esp_emp->id_unidad_medida != '')
                            {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                        @endif
                    </td>
                    <td style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center"
                        id="td_precio_{{$det_esp_emp->id_detalle_especificacionempaque}}">
                        @if(getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque) != '')
                            <select name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                    ondblclick="cambiar_input_precio('{{$det_esp_emp->id_detalle_especificacionempaque}}')"
                                    id="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}" style="background-color: beige; width: 100%">
                                @foreach(explode('|',getPrecioByClienteDetEspEmp($item->id_cliente, $det_esp_emp->id_detalle_especificacionempaque)->cantidad) as $precio)
                                    <option value="{{$precio}}">{{$precio}}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="number" id="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                   name="precio_{{$det_esp_emp->id_detalle_especificacionempaque}}" class="text-center"
                                   style="background-color: beige; width: 100%">
                        @endif
                    </td>
                    @if($item->id_especificacion != $anterior)
                        <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                            rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                            <select name="id_agencia_carga_{{$item->id_especificacion}}" id="id_agencia_carga_{{$item->id_especificacion}}"
                                    class="text-center" style="border: none; width: 100%">
                                @foreach($agenciasCarga as $agencia)
                                    <option value="{{$agencia->id_agencia_carga}}">{{$agencia->nombre}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d; vertical-align: middle"
                            rowspan="{{getCantidadDetallesByEspecificacion($item->id_especificacion)}}">
                            <button type="button" class="btn btn-xs btn-primary">
                                <i class="fa fa-fw fa-copy"></i>
                            </button>
                        </td>
                    @endif
                </tr>
                @php
                    $anterior = $item->id_especificacion;
                @endphp
            @endforeach
        @endforeach
    @endforeach
    <tr>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="vertical-align: middle"><b>TOTALES:</b></td>
        <td style="vertical-align: middle;font-size: 14px;text-align:center" id="total_ramos"></td>
        <td style="vertical-align: middle"></td>
        <td style="vertical-align: middle"></td>
        <td id="total_pedido" style="font-size: 14px;vertical-align: middle"></td>
    </tr>
@else
    <tr id="">
        <td colspan="13">
            <div class="alert alert-warning text-center">
                <p style="font-size: 11pt;"> Este usuario no posee especificaciones asignadas </p>
            </div>
        </td>
    </tr>
@endif
