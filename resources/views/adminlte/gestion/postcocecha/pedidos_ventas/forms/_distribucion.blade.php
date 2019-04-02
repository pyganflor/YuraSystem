@foreach($data['arreglo_esp_emp'] as $pos_esp_emp => $esp_emp)
    <legend style="font-size: 1em; margin-bottom: 0">
        <strong>Distribución EMP-{{$pos_esp_emp + 1}}</strong>
    </legend>

    <div style="overflow-x: scroll">
        <table class="table-bordered" width="100%" style="border: 1px solid #9d9d9d">
            <tr>
                <th class="text-center" width="65px" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Marcaciones
                </th>
                @foreach($det_ped->coloracionesByEspEmp($esp_emp['id_esp_emp']) as $pos_col => $col)
                    <th class="text-center" id="th_coloracion_{{$col->id_coloracion}}_{{$esp_emp['id_esp_emp']}}"
                        style="border-color: #9d9d9d; background-color: {{$col->color->fondo}}; color: {{$col->color->texto}}"
                        width="100px" colspan="1">
                        {{$col->color->nombre}}
                        <button type="button" class="btn btn-xs btn-link pull-right" title="Mostrar/Ocultar Distribución"
                                style="color: {{$col->color->texto}}"
                                onclick="mostrar_ocultar_distribuciones('{{$col->id_coloracion}}', '{{$esp_emp['id_esp_emp']}}')">
                            <i class="fa fa-fw fa-eye"></i>
                        </button>
                        <input type="hidden" id="id_coloracion_{{$col->id_coloracion}}">
                    </th>
                @endforeach
                <th class="text-center" width="40px" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                    Ramos
                </th>
                <th class="text-center" width="40px" style="border-color: #9d9d9d; background-color: #e9ecef;">
                    Piezas
                </th>
                <th class="text-center" width="40px" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                    Nº Caja
                </th>
                <th class="text-center" width="65px" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Marcaciones
                </th>
            </tr>
            @php
                $anterior = '';
            @endphp
            @foreach($esp_emp['marcaciones'] as $pos_marc => $marc)
                @for($d = 1; $d <= $marc['distribucion']; $d++)
                    <tr style="border-top: {{$anterior != $marc['id'] ? '2px solid black' : ''}}">
                        @if($d == 1)
                            <th class="text-center" style="border-color: #9d9d9d" rowspan="{{$marc['distribucion']}}">
                                {{getMarcacion($marc['id'])->nombre}}
                                <input type="hidden" class="id_marcacion_{{$esp_emp['id_esp_emp']}}" value="{{$marc['id']}}">
                            </th>
                            <input type="hidden" id="total_piezas_{{$marc['id']}}_{{$esp_emp['id_esp_emp']}}"
                                   value="{{getMarcacion($marc['id'])->piezas}}">
                            <input type="hidden" id="cantidad_distribuciones_{{$marc['id']}}_{{$esp_emp['id_esp_emp']}}"
                                   value="{{$marc['distribucion']}}">
                        @endif
                        @foreach($det_ped->coloracionesByEspEmp($esp_emp['id_esp_emp']) as $col)
                            <th class="text-center" width="100px"
                                style="border-color: #9d9d9d; background-color: {{$col->color->fondo}}; color: {{$col->color->texto}}">
                                <ul class="list-unstyled">
                                    @foreach(getEspecificacionEmpaque($esp_emp['id_esp_emp'])->detalles as $pos_det_esp => $det_esp)
                                        <li>
                                            <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                                <input type="number"
                                                       id="distribucion_{{$marc['id']}}_{{$col->id_coloracion}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$d}}_{{$esp_emp['id_esp_emp']}}"
                                                       onkeypress="return isNumber(event)"
                                                       style="width: 100%; background-color: {{$col->color->fondo}};
                                                               color: {{$col->color->texto}}" class="text-center"
                                                       onchange="calcular_totales_distribucion('{{$esp_emp['id_esp_emp']}}')">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </th>
                            @if($d == 1)
                                <td class="text-center td_coloracion_{{$col->id_coloracion}}_{{$esp_emp['id_esp_emp']}}" width="100px"
                                    rowspan="{{$marc['distribucion']}}"
                                    style="border-color: #9d9d9d; background-color: {{$col->color->fondo}}; color: {{$col->color->texto}};
                                            display: none;">
                                    <ul class="list-unstyled">
                                        @foreach(getEspecificacionEmpaque($esp_emp['id_esp_emp'])->detalles as $pos_det_esp => $det_esp)
                                            @php
                                                $marc_col = getMarcacion($marc['id'])->getMarcacionColoracionByDetEsp($col->id_coloracion, $det_esp->id_detalle_especificacionempaque);
                                            @endphp
                                            <li>
                                                <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #add8e6">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                                    <input type="number"
                                                           id="marcacion_coloracion_{{$marc['id']}}_{{$col->id_coloracion}}_{{$det_esp->id_detalle_especificacionempaque}}_{{$esp_emp['id_esp_emp']}}"
                                                           onkeypress="return isNumber(event)"
                                                           style="width: 100%; background-color: {{$col->color->fondo}};
                                                                   color: {{$col->color->texto}}" class="text-center"
                                                           value="{{$marc_col != '' ? $marc_col->cantidad : 0}}"
                                                           onchange="calcular_totales_distribucion('{{$esp_emp['id_esp_emp']}}')">
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            @endif
                        @endforeach

                        <th class="text-center" width="40px" style="border-color: #9d9d9d; background-color: #add8e6"
                            id="celda_ramos_{{$marc['id']}}_{{$d}}_{{$esp_emp['id_esp_emp']}}">
                            0
                        </th>
                        <th class="text-center" width="40px" style="border-color: #9d9d9d; background-color: #add8e6"
                            id="celda_distribuir_{{$marc['id']}}_{{$d}}_{{$esp_emp['id_esp_emp']}}">
                            @if($d == 1)
                                <select id="select_distribuir_{{$marc['id']}}_{{$d}}_{{$esp_emp['id_esp_emp']}}"
                                        class="select_distribuir_{{$marc['id']}}_{{$esp_emp['id_esp_emp']}}
                                                select_distribuir_{{$esp_emp['id_esp_emp']}}"
                                        onchange="seleccionar_distribucion('{{$marc['id']}}', '{{$d}}', '{{$esp_emp['id_esp_emp']}}')">
                                    <option value="">...</option>
                                    @for($i = getMarcacion($marc['id'])->piezas - ($marc['distribucion'] - 1); $i > 0; $i--)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            @else
                                <input type="hidden" id="select_distribuir_{{$marc['id']}}_{{$d}}_{{$esp_emp['id_esp_emp']}}"
                                       class="select_distribuir_{{$marc['id']}}_{{$esp_emp['id_esp_emp']}}
                                               select_distribuir_{{$esp_emp['id_esp_emp']}}" value="0">
                            @endif
                        </th>
                        <th class="text-center celda_n_cajas_{{$esp_emp['id_esp_emp']}}" width="40px"
                            style="border-color: #9d9d9d; background-color: #add8e6"
                            id="celda_n_cajas_{{$marc['id']}}_{{$d}}_{{$esp_emp['id_esp_emp']}}">
                            {{$pos_marc == 0 && $d == 1 ? 1 : 0}}
                        </th>
                        <input type="hidden" id="n_cajas_{{$marc['id']}}_{{$d}}_{{$esp_emp['id_esp_emp']}}"
                               value="{{$pos_marc == 0 && $d == 1 ? 1 : 0}}" class="n_cajas_{{$esp_emp['id_esp_emp']}}">
                        @if($d == 1)
                            <th class="text-center" style="border-color: #9d9d9d" rowspan="{{$marc['distribucion']}}">
                                {{getMarcacion($marc['id'])->nombre}}
                            </th>
                        @endif
                    </tr>
                    @php
                        $anterior = $marc['id'];
                    @endphp
                @endfor
            @endforeach
        </table>
    </div>
@endforeach

<script>
    function seleccionar_distribucion(marc, pos, esp_emp) {
        pos = parseInt(pos);
        num_piezas = parseInt($('#select_distribuir_' + marc + '_' + pos + '_' + esp_emp).val());   // piezas seleccionadas
        total_piezas = parseInt($('#total_piezas_' + marc + '_' + esp_emp).val());  // total de piezas de la marcacion
        cant_distr = parseInt($('#cantidad_distribuciones_' + marc + '_' + esp_emp).val()); // cantidad de distribuciones de la marcacion
        prev_piezas = 0;
        for (x = 0; x < $('.select_distribuir_' + marc + '_' + esp_emp).length; x++) {
            prev_piezas += parseInt($('.select_distribuir_' + marc + '_' + esp_emp)[x].value);  //  piezas seleccionadas anteriormente
        }
        rest_piezas = total_piezas - prev_piezas;
        rest_distr = cant_distr - pos;
        $('#celda_distribuir_' + marc + '_' + pos + '_' + esp_emp).html(num_piezas +
            '<input type="hidden" id="select_distribuir_' + marc + '_' + pos + '_' + esp_emp + '" value="' + num_piezas + '"' +
            ' class="select_distribuir_' + marc + '_' + esp_emp + ' select_distribuir_' + esp_emp + '">');
        max = rest_piezas - (rest_distr - 1);
        min = pos == cant_distr - 1 ? max : 1;
        options = '';
        for (i = max; i >= min; i--) {
            options += '<option value="' + i + '">' + i + '</option>';
        }
        $('#celda_distribuir_' + marc + '_' + (pos + 1) + '_' + esp_emp).html('<select ' +
            'id="select_distribuir_' + marc + '_' + (pos + 1) + '_' + esp_emp + '" ' +
            'class="select_distribuir_' + marc + '_' + esp_emp + ' select_distribuir_' + esp_emp + '" ' +
            'onchange="seleccionar_distribucion(' + marc + ', ' + (pos + 1) + ', ' + esp_emp + ')">' +
            '<option value="">...</option>' +
            options +
            '</select>');
        $('select_distribuir_' + marc + '_' + (pos + 1) + '_' + esp_emp).focus();

        /* ===== CALCULAR RAMOS y NºCAJAS ====== */
        ramox_x_caja = parseInt($('#ramos_x_caja_' + esp_emp).val());
        $('#celda_ramos_' + marc + '_' + pos + '_' + esp_emp).html(num_piezas * ramox_x_caja);

        celdas_n_cajas = $('.celda_n_cajas_' + esp_emp);
        n_cajas = $('.n_cajas_' + esp_emp);
        piezas_distr = $('.select_distribuir_' + esp_emp);
        for (i = 1; i < n_cajas.length; i++) {
            prev_cajas = parseInt(n_cajas[i - 1].value);
            prev_piezas = parseInt(piezas_distr[i - 1].value);
            n_cajas[i].value = prev_cajas + prev_piezas;
            $('#' + celdas_n_cajas[i].id).html(prev_cajas + prev_piezas);
        }
    }

    function mostrar_ocultar_distribuciones(col, esp_emp) {
        if ($('#th_coloracion_' + col + '_' + esp_emp).prop('colspan') == 2) {
            $('#th_coloracion_' + col + '_' + esp_emp).prop('colspan', 1);
            $('.td_coloracion_' + col + '_' + esp_emp).hide();
        } else {
            $('#th_coloracion_' + col + '_' + esp_emp).prop('colspan', 2);
            $('.td_coloracion_' + col + '_' + esp_emp).show();
        }
    }
</script>