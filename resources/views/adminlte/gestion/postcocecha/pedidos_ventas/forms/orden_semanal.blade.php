<script>
    function terminar_edicion() {
        cerrar_modals();
        listar_resumen_pedidos($('#fecha_pedidos_search').val(), true);
    }

    function calcular_totales_tinturado(esp_emp) {
        fil = $('#marcaciones_' + esp_emp).val();
        col = $('#coloraciones_' + esp_emp).val();
        ramos_x_caja = $('#ramos_x_caja_' + esp_emp).val();
        ids_det_esp = $('.id_det_esp_' + esp_emp);

        total = 0;
        for (f = 0; f < fil; f++) {
            total_fila = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (c = 0; c < col; c++) {
                    cant = $('#ramos_marcacion_' + f + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#parcial_marcacion_' + f + '_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
                total_fila += parcial;
            }
            $('#total_ramos_marcacion_' + f + '_' + esp_emp).val(total_fila);
            $('#total_piezas_marcacion_' + f + '_' + esp_emp).val(Math.round((total_fila / ramos_x_caja) * 100) / 100);
            total += total_fila;
        }
        $('#total_ramos_' + esp_emp).val(total);
        $('#total_piezas_' + esp_emp).val(Math.round((total / ramos_x_caja) * 100) / 100);

        for (c = 0; c < col; c++) {
            total_col = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (f = 0; f < fil; f++) {
                    cant = $('#ramos_marcacion_' + f + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#parcial_color_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
                total_col += parcial;
            }
        }

        for (det = 0; det < ids_det_esp.length; det++) {
            parcial = 0;
            for (c = 0; c < col; c++) {
                cant = $('#parcial_color_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp).val();
                if (cant != '') {
                    parcial += parseInt(cant);
                }
            }
            $('#parcial_' + ids_det_esp[det].value + '_' + esp_emp).val(parcial);
        }
    }

    function add_coloracion(esp_emp) {
        col = parseInt($('#coloraciones_' + esp_emp).val());

        tabla = $('#tabla_marcacion_coloracion_' + esp_emp);
        columna = col;
        num_colum_a_insertar = 0;
        $(tabla).find('tr').each(function (f, row) { // recorremos todas sus rows
            var primer_td = $(row).find('td,th')[columna]; // obtenemos  columna (por que insertaremos despues de esta)
            if (primer_td.tagName == 'TH') {
                for (var i = 0; i <= num_colum_a_insertar; i++) {
                    if (f == 0) {
                        //insertamos una cabecera despues de la primera cabecera
                        $('<th class="text-center" style="border-color: #9d9d9d" width="100px">' +
                            '<select name="color_' + col + '_' + esp_emp + '" id="color_' + col + '_' + esp_emp + '"' +
                            ' onchange="cambiar_color($(this).val(), ' + col + ', ' + esp_emp + ')">' +
                            $('#select_colores').html() +
                            '</select>' +
                            '<input type="hidden" id="id_color_' + col + '_' + esp_emp + '" name="id_color_' + col + '_' + esp_emp + '" ' +
                            'value="' + $('#select_colores').val() + '">' +
                            '</th>').insertAfter(primer_td);
                    } else {
                        ids_det_esp = $('.id_det_esp_' + esp_emp);
                        inputs = '';
                        for (det = 0; det < ids_det_esp.length; det++) {
                            inputs += '<li>' +
                                '<div class="input-group" style="width: 100px">' +
                                '<span class="input-group-addon" style="background-color: #e9ecef">' +
                                'P-' + (det + 1) +
                                '</span>' +
                                '<input type="number" id="parcial_color_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                                'name="parcial_color_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" value="0" ' +
                                'style="width: 100%; background-color: #357ca5; color: white" class="text-center">' +
                                '</div>' +
                                '</li>';
                        }
                        $('<th class="text-center" style="border-color: #9d9d9d" width="100px">' +
                            '<ul class="list-unstyled">' +
                            inputs +
                            '</ul>' +
                            '</th>').insertAfter(primer_td);
                    }
                }
            } else {
                for (var i = 0; i <= num_colum_a_insertar; i++) {
                    ids_det_esp = $('.id_det_esp_' + esp_emp);
                    inputs = '';
                    for (det = 0; det < ids_det_esp.length; det++) {
                        inputs += '<li>' +
                            '<div class="input-group" style="width: 100px">' +
                            '<span class="input-group-addon" style="background-color: #e9ecef">' +
                            'P-' + (det + 1) +
                            '</span>' +
                            '<input type="number" value="0" id="ramos_marcacion_' + (f - 1) + '_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                            'name="ramos_marcacion_' + (f - 1) + '_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                            'onkeypress="return isNumber(event)" style="width: 100%;" ' +
                            'class="text-center elemento_color_' + col + '_' + esp_emp + '" onchange="calcular_totales_tinturado(' + esp_emp + ')">' +
                            '</div>' +
                            '</li>';
                    }
                    //insertamos un valor despues del primer valor de la primera columna
                    $('<td class="text-center" style="border-color: #9d9d9d;" width="100px">' +
                        '<ul class="list-unstyled">' +
                        inputs +
                        '</ul>' +
                        '</td>').insertAfter(primer_td);
                }
            }
        });
        cambiar_color($('#select_colores').val(), col, esp_emp);
        col++;
        $('#coloraciones_' + esp_emp).val(col);
    }

    function add_marcacion(esp_emp) {
        fil = $('#marcaciones_' + esp_emp).val();

        tabla = $('#tabla_marcacion_coloracion_' + esp_emp);

        html = '';
        $('<tr></tr>').insertAfter($(tabla).find('tr')[fil]);

        $('#marcaciones' + esp_emp).val(fil++);
    }
</script>

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
                @php
                    $ramos_x_caja = 0;
                @endphp
                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                    @php
                        $ramos_x_caja += $det_esp->cantidad;
                    @endphp
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
                                       name="marcaciones_{{$esp_emp->id_especificacion_empaque}}" readonly
                                       value="{{count($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'])}}"
                                       required min="1" style="border: none" class="text-center"
                                       width="50px">
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                <input type="number" id="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                       onkeypress="return isNumber(event)" readonly
                                       name="coloraciones_{{$esp_emp->id_especificacion_empaque}}"
                                       value="{{count($det_ped->getColoracionesMarcacionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'])}}"
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
                    <input type="hidden" class="id_det_esp_{{$esp_emp->id_especificacion_empaque}}"
                           value="{{$det_esp->id_detalle_especificacionempaque}}">
                @endforeach
                <input type="hidden" id="ramos_x_caja_{{$esp_emp->id_especificacion_empaque}}" value="{{$ramos_x_caja}}">
            @endforeach
        </table>
    </div>
</form>

@include('adminlte.gestion.postcocecha.pedidos_ventas.forms._tabla')

<div class="text-center" style="margin-top: 10px">
    @if($have_prev)
        <button type="button" class="btn btn-xs btn-success"
                onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped - 1}}', false)">
            <i class="fa fa-fw fa-long-arrow-left"></i> Anterior
        </button>
    @endif
    @if($have_next)
        <button type="button" class="btn btn-xs btn-success"
                onclick="editar_pedido_tinturado('{{$pedido->id_pedido}}', '{{$pos_det_ped + 1}}', false)">
            <i class="fa fa-fw fa-long-arrow-right"></i> Siguiente
        </button>
    @else
        <button type="button" class="btn btn-xs btn-default" onclick="terminar_edicion()">
            <i class="fa fa-fw fa-times"></i> Terminar
        </button>
    @endif
</div>