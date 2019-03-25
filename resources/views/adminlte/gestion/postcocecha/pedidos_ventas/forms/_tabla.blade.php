@foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
    <legend style="font-size: 1em; margin-bottom: 0">
        <strong>Distribución EMP-{{$pos_esp_emp + 1}}</strong>
    </legend>
    <div style="overflow-x: scroll">
        <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d">
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="150px">
                    Color
                </th>
                @foreach($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                    <th class="text-center"
                        style="border-color: #9d9d9d; background-color: {{getColor($color->id_color)->fondo}}"
                        width="100px">
                        <select name="color_{{$pos_color}}" id="color_{{$pos_color}}">
                            @foreach(getColores() as $c)
                                <option value="{{$c->id_color}}" {{$c->id_color == $color->id_color ? 'selected' : ''}}>
                                    {{$c->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id_color_{{$pos_color}}" name="id_color_{{$pos_color}}" value="{{$color->id_color}}">
                    </th>
                @endforeach
                @if(count($esp_emp->detalles) > 1)
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="100px">
                        Parcial
                    </th>
                @endif
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                    Total
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                    Piezas
                </th>
            </tr>
            @foreach($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'] as $pos_marca => $marca)
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="nombre_marcacion_{{$pos_marca}}" name="nombre_marcacion_{{$pos_marca}}"
                               value="{{getMarcacion($marca->id_marcacion)->nombre}}" width="150px" style="border: none" class="text-center">
                        <input type="hidden" id="nombre_marcacion_{{$pos_marca}}" name="nombre_marcacion_{{$pos_marca}}"
                               value="{{$marca->id_marcacion}}">
                    </td>
                    @foreach($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                        <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                            <ul class="list-unstyled">
                                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                    <li>
                                        <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                            @php
                                                $coloracion = getMarcacion($marca->id_marcacion)->getColoracionByColorDetEsp($color->id_color, $det_esp->id_detalle_especificacionempaque);
                                            @endphp
                                            <input type="number"
                                                   value="{{$coloracion != '' ? $coloracion->cantidad : ''}}"
                                                   id="ramos_marcacion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}"
                                                   name="ramos_marcacion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}"
                                                   onkeypress="return isNumber(event)"
                                                   style="width: 100%; background-color: {{getColor($color->id_color)->fondo}};
                                                           color: {{getColor($color->id_color)->texto}}"
                                                   class="text-center elemento_color_{{$pos_color}}"
                                                   onchange="calcular_totales_tinturado()">
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </th>
                    @endforeach
                    @if(count($esp_emp->detalles) > 1)
                        <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                            <ul class="list-unstyled">
                                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                    <li>
                                        <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                            <input type="number"
                                                   id="parcial_marcacion_{{$pos_marca}}_{{$det_esp->id_detalle_especificacionempaque}}"
                                                   name="parcial_marcacion_{{$pos_marca}}_{{$det_esp->id_detalle_especificacionempaque}}"
                                                   style="width: 100%; background-color: #357ca5; color: white" readonly
                                                   class="text-center">
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </th>
                    @endif
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="total_ramos_marcacion_{{$pos_marca}}" name="total_ramos_marcacion_{{$pos_marca}}" readonly
                               class="text-center" value="{{getMarcacion($marca->id_marcacion)->ramos}}"
                               style="background-color: #357ca5; color: white; width: 85px">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="total_piezas_marcacion_{{$pos_marca}}" name="total_piezas_marcacion_{{$pos_marca}}" readonly
                               class="text-center" value="{{getMarcacion($marca->id_marcacion)->piezas}}"
                               style="background-color: #357ca5; color: white; width: 85px">
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef"
                    rowspan="{{count($esp_emp->detalles) > 1 ? 2 : ''}}">
                    Totales
                </td>
                @if(count($esp_emp->detalles) > 1)
                    @foreach($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                        <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                            <ul class="list-unstyled">
                                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                    <li>
                                        <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                            <input type="number" id="parcial_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}"
                                                   name="parcial_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}"
                                                   readonly style="width: 100%; background-color: #357ca5; color: white" class="text-center">
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </th>
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                        <ul class="list-unstyled">
                            @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                <li>
                                    <div class="input-group" style="width: 100px">
                                        <span class="input-group-addon" style="background-color: #e9ecef">
                                            P-{{$pos_det_esp + 1}}
                                        </span>
                                        <input type="number" id="parcial_{{$det_esp->id_detalle_especificacionempaque}}"
                                               name="parcial_{{$det_esp->id_detalle_especificacionempaque}}"
                                               style="width: 100%; background-color: #357ca5; color: white" readonly class="text-center">
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </th>
                @endif

                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" id="total_ramos_marcacion_{{$pos_marca}}" name="total_ramos_marcacion_{{$pos_marca}}" readonly
                           class="text-center" value="{{getMarcacion($marca->id_marcacion)->ramos}}"
                           style="background-color: #357ca5; color: white; width: 85px">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" id="total_piezas_marcacion_{{$pos_marca}}" name="total_piezas_marcacion_{{$pos_marca}}" readonly
                           class="text-center" value="{{getMarcacion($marca->id_marcacion)->piezas}}"
                           style="background-color: #357ca5; color: white; width: 85px">
                </td>
            </tr>
        </table>
    </div>
@endforeach