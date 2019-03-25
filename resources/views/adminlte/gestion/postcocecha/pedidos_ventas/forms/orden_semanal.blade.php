<form id="form-update_orden_semanal">
    <table class="table-bordered" width="100%" style="margin-bottom: 10px">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Fecha
            </th>
            <th class="text-center" style="border-color: #9d9d9d" width="85px">
                <input type="date" id="fecha_pedido" name="fecha_pedido" value="{{$pedido->fecha_pedido}}" required width="100%"
                       class="form-control">
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Cliente
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{$pedido->cliente->detalle()->nombre}}
            </th>
        </tr>
    </table>

    <legend style="font-size: 1.1em; margin-bottom: 0">
        <strong>Detalles de la especificación</strong>
    </legend>

    <div style="overflow-x: scroll">
        <table class="table-bordered" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
            <tr style="background-color: #e9ecef">
                <th class="text-center" style="border-color: #9d9d9d" width="85px">
                    CANTIDAD
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Nº Empaque
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Nº Presentación
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    VARIEDAD
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    CALIBRE
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    CAJA
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    RAMOS x CAJA
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    PRESENTACIÓN
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    TALLOS x RAMO
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    LONGITUD
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    U. MEDIDA
                </th>
                <th class="text-center" style="border-color: #9d9d9d" width="50px">
                    PRECIO
                </th>
                <th class="text-center" style="border-color: #9d9d9d" width="50px">
                    MARCACIONES
                </th>
                <th class="text-center" style="border-color: #9d9d9d" width="85px">
                    COLORACIONES
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    AGENCIA CARGA
                </th>
            </tr>
            @php
                $det_ped = $pedido->detalles[$pos_det_ped];
            @endphp
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                    <tr>
                        @if($pos_esp_emp == 0 && $pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d"
                                rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                <input type="number" id="cantidad_piezas" name="cantidad_piezas" value="{{$det_ped->cantidad}}" required
                                       onkeypress="return isNumber(event)" style="border: none" class="text-center">
                            </td>
                        @endif
                        @if($pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                EMP-{{$pos_esp_emp + 1}}
                            </td>
                        @endif
                        <td class="text-center" style="border-color: #9d9d9d">
                            P-{{$pos_det_esp + 1}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->variedad->nombre}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->clasificacion_ramo->nombre}}
                            {{$det_esp->clasificacion_ramo->unidad_medida->siglas}}
                        </td>
                        @if($pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                {{explode('|',$esp_emp->empaque->nombre)[0]}}
                            </td>
                        @endif
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->cantidad}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->empaque_p->nombre}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->tallos_x_ramo}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->longitud_ramo}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            @if($det_esp->longitud_ramo)
                                {{$det_esp->unidad_medida->siglas}}
                            @endif
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="number" id="precio_det_esp_{{$det_esp->id_detalle_especificacionempaque}}"
                                   style="width: 50px; background-color: #e9ecef" min="0"
                                   name="precio_det_esp_{{$det_esp->id_detalle_especificacionempaque}}"
                                   value="{{getPrecioByDetEsp($det_ped->precio, $det_esp->id_detalle_especificacionempaque)}}"
                                   class="text-center">
                        </td>
                        @if($pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                <input type="number" id="marcaciones_{{$esp_emp->id_especificacion_empaque}}" onkeypress="return isNumber(event)"
                                       name="marcaciones_{{$esp_emp->id_especificacion_empaque}}"
                                       value="{{count($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'])}}"
                                       required min="1" style="border: none" class="text-center"
                                       width="50px">
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                <input type="number" id="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                       onkeypress="return isNumber(event)"
                                       name="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                       value="{{count($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'])}}"
                                       required min="1" style="border: none" class="text-center"
                                       width="50px">
                            </td>
                        @endif
                        @if($pos_esp_emp == 0 && $pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d"
                                rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                <select name="id_agencia_carga" id="id_agencia_carga" required style="width: 100%; border: none">
                                    @foreach($pedido->cliente->cliente_agencia_carga as $item)
                                        <option value="{{$item->id_agencia_carga}}" {{$item->id_agencia_carga == $det_ped->id_agencia_carga ? 'selected' : ''}}>
                                            {{$item->agencia_carga->nombre}}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </table>
    </div>
</form>

@include('adminlte.gestion.postcocecha.pedidos_ventas.forms._tabla')
