@foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
    <legend style="font-size: 1em; margin-bottom: 0">
        <strong>
            Distribución EMP-{{$pos_esp_emp + 1}}
            <button type="button" class="btn btn-xs btn-primary" onclick="add_marcacion('{{$esp_emp->id_especificacion_empaque}}')">
                <i class="fa fa-fw fa-plus"></i> Marcación
            </button>
            <button type="button" class="btn btn-xs btn-primary" onclick="add_coloracion('{{$esp_emp->id_especificacion_empaque}}')">
                <i class="fa fa-fw fa-plus"></i> Coloración
            </button>
            @if($det_ped->haveDistribucionByEspEmp($esp_emp->id_especificacion_empaque))
                <button type="button" class="btn btn-xs btn-danger pull-right elemento_distribuir"
                        onclick="quitar_distribuciones('{{$det_ped->id_pedido}}','{{csrf_token()}}')">
                    <i class="fa fa-fw fa-times"></i> Quitar Distribuciones
                </button>
                <button type="button" class="btn btn-xs btn-primary pull-right elemento_distribuir"
                        onclick="ver_distribucion('{{$det_ped->id_detalle_pedido}}')">
                    <i class="fa fa-fw fa-eye"></i> Ver Distribución
                </button>
            @else
                <button type="button" class="btn btn-xs btn-primary pull-right elemento_distribuir"
                        onclick="distribuir_pedido_tinturado('{{$det_ped->id_detalle_pedido}}')">
                    <i class="fa fa-fw fa-exchange"></i> Distribuir
                </button>
                <button type="button" class="btn btn-xs btn-info pull-right elemento_distribuir"
                        onclick="distribuir_pedido_tinturado('{{$det_ped->id_detalle_pedido}}', true, '{{$esp_emp->id_especificacion_empaque}}', '{{csrf_token()}}')">
                    <i class="fa fa-fw fa-exchange"></i> Auto-Distribuir
                </button>
            @endif
        </strong>
    </legend>
    <div style="overflow-x: scroll">
        <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d"
               id="tabla_marcacion_coloracion_{{$esp_emp->id_especificacion_empaque}}">
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="150px">
                    Color
                </th>
                @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                    <th class="text-center" style="border-color: #9d9d9d" width="100px"
                        id="celda_col_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}">
                        <select name="color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}" style="width: 100px;font-size:11px"
                                onchange="cambiar_color($(this).val(), '{{$pos_color}}', '{{$esp_emp->id_especificacion_empaque}}')"
                                id="color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}">
                            @foreach(getColores() as $c)
                                <option value="{{$c->id_color}}" {{$c->id_color == $color->id_color ? 'selected' : ''}}>
                                    {{$c->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id_color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}"
                               name="id_color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}" value="{{$color->id_color}}">
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
                <th class="text-center elemento_distribuir" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                    Distribución
                </th>
            </tr>
            @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'] as $pos_marca => $marca)
                <tr style="border: 2px solid #9d9d9d">
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="nombre_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                               name="nombre_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                               value="{{getMarcacion($marca->id_marcacion)->nombre}}" width="150px" style="border: none" class="text-center">
                        <input type="hidden" id="id_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                               name="id_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                               value="{{$marca->id_marcacion}}">
                    </td>
                    @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                        <td class="text-center" style="border-color: #9d9d9d;" width="100px">
                            <ul class="list-unstyled">
                                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                    <li>
                                        <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                            @php
                                                $marc_col = getMarcacion($marca->id_marcacion)->getMarcacionColoracionByDetEsp($color->id_coloracion, $det_esp->id_detalle_especificacionempaque);
                                            @endphp
                                            <input type="number"
                                                   value="{{$marc_col != '' ? $marc_col->cantidad : 0}}"
                                                   id="ramos_marcacion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                   name="ramos_marcacion_{{$pos_marca}}_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                   onkeypress="return isNumber(event)"
                                                   style="width: 100%; background-color: {{getColor($color->id_color)->fondo}};
                                                           color: {{getColor($color->id_color)->texto}}" min="0"
                                                   class="text-center elemento_color_{{$pos_color}}_{{$esp_emp->id_especificacion_empaque}}"
                                                   onchange="calcular_totales_tinturado('{{$esp_emp->id_especificacion_empaque}}')">
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
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
                                                   id="parcial_marcacion_{{$pos_marca}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                   name="parcial_marcacion_{{$pos_marca}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                                   style="width: 100%; background-color: #357ca5; color: white" readonly
                                                   class="text-center">
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </th>
                    @endif
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="total_ramos_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                               name="total_ramos_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}" readonly
                               class="text-center" value="{{getMarcacion($marca->id_marcacion)->ramos}}"
                               style="background-color: #357ca5; color: white; width: 85px">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="total_piezas_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                               name="total_piezas_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}" readonly
                               class="text-center" value="{{getMarcacion($marca->id_marcacion)->piezas}}"
                               style="background-color: #357ca5; color: white; width: 85px">
                    </td>
                    <td class="text-center elemento_distribuir" style="border-color: #9d9d9d">
                        <select name="distribucion_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                id="distribucion_marcacion_{{$pos_marca}}_{{$esp_emp->id_especificacion_empaque}}"
                                style="background-color: #357ca5; color: white; width: 60px;">
                            @for($i = getMarcacion($marca->id_marcacion)->piezas; $i > 0; $i--)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </td>
                </tr>
            @endforeach
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Totales
                </th>
                @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                    <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                        <ul class="list-unstyled">
                            @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                <li>
                                    <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                        <input type="number"
                                               id="parcial_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                               name="parcial_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                               readonly style="width: 100%; background-color: #357ca5; color: white" class="text-center" min="0">
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
                                               id="parcial_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                               name="parcial_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                               style="width: 100%; background-color: #357ca5; color: white" readonly class="text-center">
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </th>
                @endif

                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" id="total_ramos_{{$esp_emp->id_especificacion_empaque}}"
                           name="total_ramos_{{$esp_emp->id_especificacion_empaque}}" readonly class="text-center"
                           style="background-color: #357ca5; color: white; width: 85px">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" id="total_piezas_{{$esp_emp->id_especificacion_empaque}}"
                           name="total_piezas_{{$esp_emp->id_especificacion_empaque}}" readonly class="text-center"
                           style="background-color: #357ca5; color: white; width: 85px">
                </td>
            </tr>
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef"
                    rowspan="{{count($esp_emp->detalles) > 1 ? 2 : ''}}">
                    Precios
                </th>
                @foreach($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                    <th class="text-center" style="border-color: #9d9d9d;" width="100px">
                        <ul class="list-unstyled">
                            @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                                <li>
                                    <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                        <input type="number"
                                               id="precio_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                               name="precio_color_{{$pos_color}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp->id_especificacion_empaque}}"
                                               style="width: 100%; background-color: #e9ecef" class="text-center" min="0"
                                               value="{{$color->getPrecioByDetEsp($det_esp->id_detalle_especificacionempaque)}}"
                                        >
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </th>
                @endforeach
                <th class="text-center" style="border-color: #9d9d9d">
                    PRECIO TOTAL
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    $$
                </th>
            </tr>
        </table>
    </div>

    <script>
        calcular_totales_tinturado('{{$esp_emp->id_especificacion_empaque}}', true);
    </script>
@endforeach
