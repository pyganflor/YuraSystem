<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Distribución del pedido
        </h3>

        <button type="button" class="btn btn-default btn-xs pull-right" onclick="update_pedido()">
            <i class="fa fa-fw fa-save"></i> Actualizar pedido
        </button>
        <label for="checkbox_auto_editar" class="pull-right mouse-hand" style="margin-left: 5px; margin-right: 5px">
            Editar automáticamente
        </label>
        <input type="checkbox" id="checkbox_auto_editar" name="checkbox_auto_editar" class="pull-right" checked>
    </div>
    <div class="box-body">
        <form id="form-update_pedido">
            <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d">
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" width="7%">
                        Fecha
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" width="7%">
                        Cant. Piezas
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        Pieza
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" width="7%">
                        Cant. Ramos
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        Calibre
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        Variedad
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        Envoltura
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        Presentación
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" width="7%">
                        Tallos
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" width="7%">
                        Longitud
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        U. Medida
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                        Agencia
                    </th>
                </tr>
                <tr id="row_form_1">
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="date" id="fecha_pedido" name="fecha_pedido" value="{{$pedido->fecha_pedido}}" required>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="cantidad_piezas" name="cantidad_piezas" required style="width: 100%"
                               onkeypress="return isNumber(event)" min="1"
                               value="{{$esp_emp->cantidad}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_empaque" id="id_empaque" required style="width: 100%">
                            @foreach($cajas as $caja)
                                <option value="{{$caja->id_empaque}}" {{$caja->id_empaque == $esp_emp->id_empaque ? 'selected' : ''}}>
                                    {{explode('|',$caja->nombre)[0]}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="cantidad_ramos" name="cantidad_ramos" required style="width: 100%"
                               onkeypress="return isNumber(event)" min="1" value="{{$det_esp->cantidad}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_clasificacion_ramo" id="id_clasificacion_ramo" required style="width: 100%">
                            @foreach($calibres as $item)
                                @if($item->unidad_medida->tipo == 'P')
                                    <option value="{{$item->id_clasificacion_ramo}}"
                                            {{$item->id_clasificacion_ramo == $det_esp->id_clasificacion_ramo ? 'selected' : ''}}>
                                        {{$item->nombre}}{{$item->unidad_medida->siglas}}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_variedad" id="id_variedad" required style="width: 100%">
                            @foreach($variedades as $item)
                                <option value="{{$item->id_variedad}}"
                                        {{$item->id_variedad == $det_esp->id_variedad ? 'selected' : ''}}>
                                    {{$item->siglas}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_empaque_e" id="id_empaque_e" required style="width: 100%">
                            @foreach($envolturas as $item)
                                <option value="{{$item->id_empaque}}" {{$item->id_empaque == $det_esp->id_empaque_e ? 'selected' : ''}}>
                                    {{explode('|',$item->nombre)[0]}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_empaque_p" id="id_empaque_p" required style="width: 100%">
                            @foreach($presentaciones as $item)
                                <option value="{{$item->id_empaque}}" {{$item->id_empaque == $det_esp->id_empaque_p ? 'selected' : ''}}>
                                    {{explode('|',$item->nombre)[0]}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="tallos_x_ramo" name="tallos_x_ramo" style="width: 100%"
                               onkeypress="return isNumber(event)" min="1" value="{{$det_esp->tallos_x_ramos}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="longitud_ramo" name="longitud_ramo" style="width: 100%"
                               onkeypress="return isNumber(event)" min="1" value="{{$det_esp->longitud_ramo}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_unidad_medida" id="id_unidad_medida" style="width: 100%">
                            @foreach($unidades_medida as $item)
                                <option value="{{$item->id_unidad_medida}}" {{$item->id_unidad_medida == $det_esp->id_unidad_medida ? 'selected' : ''}}>
                                    {{$item->siglas}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_agencia_carga" id="id_agencia_carga" style="width: 100%">
                            @foreach($agencias as $item)
                                <option value="{{$item->id_agencia_carga}}"
                                        {{$item->id_agencia_carga == $pedido->detalles[0]->id_agencia_carga ? 'selected' : ''}}>
                                    {{$item->agencia_carga->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>
        </form>
        <div style="width: 100%; overflow-x: scroll" id="div_tabla_distribucion_pedido">
            <table class="table-responsive table-striped table-bordered" style="border: 2px solid #9d9d9d; margin-top: 10px" width="100%">
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        <button type="button" class="btn btn-default btn-xs" onclick="update_distribucion('{{$pedido->id_pedido}}')">
                            <i class="fa fa-fw fa-pencil"></i> Guardar
                        </button>
                    </th>
                    @foreach($coloraciones as $color)
                        <th class="text-center"
                            style="border-color: #9d9d9d; width: 150px">
                            <form id="form_coloracion_{{str_replace(' ','_',espacios($color->nombre))}}">
                                <input type="color" value="{{$color->texto}}"
                                       id="texto_coloracion_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       onchange="cambiar_colores('{{str_replace(' ','_',espacios($color->nombre))}}'); editar_coloracion('{{str_replace(' ','_',espacios($color->nombre))}}')"
                                       name="texto_coloracion_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       class="text-center" style="width: 100%" required>
                                <input type="color" value="{{$color->fondo}}" required
                                       id="fondo_coloracion_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       onchange="cambiar_colores('{{str_replace(' ','_',espacios($color->nombre))}}'); editar_coloracion('{{str_replace(' ','_',espacios($color->nombre))}}')"
                                       name="fondo_coloracion_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       class="text-center" style="width: 100%">
                                <input type="text" value="{{str_replace(' ','_',espacios($color->nombre))}}" maxlength="250"
                                       onchange="editar_coloracion('{{str_replace(' ','_',espacios($color->nombre))}}')"
                                       id="nombre_coloracion_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       name="nombre_coloracion_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       class="text-center element_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       style="background-color: {{$color->fondo}}; color: {{$color->texto}}" required>
                            </form>
                        </th>
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        <p style="padding: 10px">Ramos</p>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        <p style="padding: 10px">Piezas</p>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        <button type="button" class="btn btn-default btn-xs" onclick="update_distribucion('{{$pedido->id_pedido}}')">
                            <i class="fa fa-fw fa-pencil"></i> Guardar
                        </button>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        <p style="padding: 10px">Distribuciones</p>
                    </th>
                </tr>
                @php
                    $matriz = [];
                    $total_ramos = 0;
                @endphp
                @foreach($marcaciones as $marca)
                    <tr id="row_marcacion_{{$marca->id_marcacion}}">
                        <td class="text-center" style="border-color: #9d9d9d; padding: 0">
                            <form id="form_marcacion_{{$marca->id_marcacion}}" style="padding: 0;">
                                <input type="text" value="{{$marca->nombre}}" id="nombre_marcacion_{{$marca->id_marcacion}}"
                                       name="nombre_marcacion_{{$marca->id_marcacion}}" class="text-center form-control"
                                       onchange="editar_marcacion('{{$marca->id_marcacion}}')" required
                                       style="width: 150px">
                            </form>
                            <input type="hidden" id="id_marcacion_{{$marca->id_marcacion}}" name="id_marcacion_{{$marca->id_marcacion}}"
                                   class="id_marcacion" value="{{$marca->id_marcacion}}">
                        </td>
                        @php
                            $fila = [];
                        @endphp
                        @foreach($coloraciones as $color)
                            <td class="text-center" style="border-color: #9d9d9d">
                                <input type="number"
                                       onchange="editar_marcacion_coloracion('{{$marca->id_marcacion}}','{{str_replace(' ','_',espacios($color->nombre))}}')"
                                       id="cant_marc_{{$marca->id_marcacion}}_color_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       name="cant_marc_{{$marca->id_marcacion}}_color_{{str_replace(' ','_',espacios($color->nombre))}}"
                                       width="100%" class="text-center form-control element_{{str_replace(' ','_',espacios($color->nombre))}}
                                        input_color_{{str_replace(' ','_',espacios($color->nombre))}} input_marca_{{$marca->id_marcacion}}"
                                       style="width: 100%; background-color: {{$color->fondo}}; color: {{$color->texto}};"
                                       value="{{$marca->getColoracionByName($color->nombre) != '' ? $marca->getColoracionByName($color->nombre)->cantidad : ''}}">
                            </td>
                            @php
                                array_push($fila, $marca->getColoracionByName($color->nombre) != '' ? $marca->getColoracionByName($color->nombre)->cantidad : 0);
                            @endphp
                        @endforeach
                        <td class="text-center" style="border-color: #9d9d9d" id="total_marca_{{$marca->id_marcacion}}">
                            {{$marca->getTotalRamos()}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" id="total_piezas_{{$marca->id_marcacion}}">
                            {{round($marca->getTotalRamos() / $det_esp->cantidad, 2)}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="text" value="{{$marca->nombre}}" class="text-center form-control" disabled style="width: 150px">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d; padding: 0">
                            <select name="cantidad_distribuciones_{{$marca->id_marcacion}}" class="form-control cantidad_distribuciones"
                                    id="cantidad_distribuciones_{{$marca->id_marcacion}}">
                                @for($d = 1; $d <= round($marca->getTotalRamos() / $det_esp->cantidad, 2); $d++)
                                    <option value="{{$d}}">{{$d}}</option>
                                @endfor
                            </select>
                        </td>
                    </tr>
                    @php
                        array_push($matriz, $fila);
                        $total_ramos += $marca->getTotalRamos();
                    @endphp
                @endforeach
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        Total Ramos
                    </th>
                    @php
                        $totales_x_color = [];
                            for($c = 0; $c < count($coloraciones); $c++){
                                $total = 0;
                                for($m = 0; $m < count($marcaciones); $m++){
                                    $total += $matriz[$m][$c];
                                }
                                array_push($totales_x_color, $total);
                            }
                    @endphp
                    @for($c = 0; $c < count($coloraciones); $c++)
                        <th class="text-center"
                            style="border-color: #9d9d9d; width: 150px">
                            <input type="text" value="{{$totales_x_color[$c]}}"
                                   id="total_color_{{str_replace(' ','_',espacios($coloraciones[$c]->nombre))}}"
                                   class="text-center element_{{str_replace(' ','_',espacios($coloraciones[$c]->nombre))}} total_color" disabled
                                   style="background-color: {{$coloraciones[$c]->fondo}}; color: {{$coloraciones[$c]->texto}}">
                        </th>
                    @endfor
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white"
                        id="total_ramos">
                        {{$total_ramos}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white"
                        id="total_piezas">
                        {{round($total_ramos / $det_esp->cantidad, 2)}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        Total
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white" colspan="2"
                        rowspan="2">
                        <button type="button" class="btn btn-primary btn-xs" onclick="distribuir_marcaciones('{{$pedido->id_pedido}}')">
                            <i class="fa fa-fw fa-gift"></i> Distribuir
                        </button>
                    </th>
                </tr>

                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        <button type="button" class="btn btn-default btn-xs" onclick="update_distribucion('{{$pedido->id_pedido}}')">
                            <i class="fa fa-fw fa-pencil"></i> Guardar
                        </button>
                    </th>
                    @foreach($coloraciones as $color)
                        <th class="text-center"
                            style="border-color: #9d9d9d; width: 150px">
                            <input type="text" value="{{str_replace(' ','_',espacios($color->nombre))}}"
                                   class="text-center element_{{str_replace(' ','_',espacios($color->nombre))}}" disabled
                                   style="background-color: {{$color->fondo}}; color: {{$color->texto}}">
                        </th>
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        Ramos
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        Piezas
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d; width: 150px; background-color: #9d9d9d; color: white">
                        <button type="button" class="btn btn-default btn-xs" onclick="update_distribucion('{{$pedido->id_pedido}}')">
                            <i class="fa fa-fw fa-pencil"></i> Guardar
                        </button>
                    </th>
                </tr>
            </table>
        </div>
    </div>
</div>

<input type="hidden" id="id_pedido" value="{{$pedido->id_pedido}}">
<input type="hidden" id="id_especificacion_empaque" value="{{$esp_emp->id_especificacion_empaque}}">
<input type="hidden" id="id_detalle_especificacionempaque" value="{{$det_esp->id_detalle_especificacionempaque}}">
<script>
    arreglo_models = [];

    function cambiar_colores(color) {
        fondo = $('#fondo_coloracion_' + color).val();
        texto = $('#texto_coloracion_' + color).val();

        $('.element_' + color).css('background-color', '' + fondo);
        $('.element_' + color).css('color', '' + texto);
    }

    function editar_coloracion(color) {
        if ($('#form_coloracion_' + color).valid()) {
            datos = {
                _token: '{{csrf_token()}}',
                pedido: $('#id_pedido').val(),
                color: color,
                nombre: $('#nombre_coloracion_' + color).val(),
                fondo: $('#fondo_coloracion_' + color).val(),
                texto: $('#texto_coloracion_' + color).val(),
                tipo: 'C'   // coloracion
            };
            if ($('#checkbox_auto_editar').prop('checked')) {
                $.LoadingOverlay('show');
                $.post('{{url('pedidos/editar_coloracion')}}', datos, function (retorno) {
                    alerta(retorno.mensaje);
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                    alerta('Ha ocurrido un problema al enviar la información');
                }).always(function () {
                    $.LoadingOverlay('hide');
                });
            } else {
                exist = 0;
                for (i = 0; i < arreglo_models.length; i++) {
                    if (arreglo_models[i]['color'] == datos['color'] && arreglo_models[i]['tipo'] == 'C') {
                        arreglo_models[i] = datos;
                        exist = 1;
                    }
                }
                if (exist == 0)
                    arreglo_models.push(datos);
            }
        }
    }

    function editar_marcacion(marcacion) {
        if ($('#form_marcacion_' + marcacion).valid()) {
            datos = {
                _token: '{{csrf_token()}}',
                pedido: $('#id_pedido').val(),
                nombre: $('#nombre_marcacion_' + marcacion).val(),
                id_marcacion: marcacion,
                tipo: 'M'   // marcacion
            };
            if ($('#checkbox_auto_editar').prop('checked')) {
                $.LoadingOverlay('show');
                $.post('{{url('pedidos/editar_marcacion')}}', datos, function (retorno) {
                    alerta(retorno.mensaje);
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                    alerta('Ha ocurrido un problema al enviar la información');
                }).always(function () {
                    $.LoadingOverlay('hide');
                });
            } else {
                exist = 0;
                for (i = 0; i < arreglo_models.length; i++) {
                    if (arreglo_models[i]['id_marcacion'] == datos['id_marcacion'] && arreglo_models[i]['tipo'] == 'M') {
                        arreglo_models[i] = datos;
                        exist = 1;
                    }
                }
                if (exist == 0)
                    arreglo_models.push(datos);
            }
        }
    }

    function editar_marcacion_coloracion(marcacion, color) {
        datos = {
            pedido: $('#id_pedido').val(),
            cantidad: $('#cant_marc_' + marcacion + '_color_' + color).val(),
            id_marcacion: marcacion,
            color: color,
            nombre: $('#nombre_coloracion_' + color).val(),
            fondo: $('#fondo_coloracion_' + color).val(),
            texto: $('#texto_coloracion_' + color).val(),
            tipo: 'X'   // cantidad de ramos de marcacion x coloracion
        };
        exist = 0;
        for (i = 0; i < arreglo_models.length; i++) {
            if (arreglo_models[i]['id_marcacion'] == datos['id_marcacion'] && arreglo_models[i]['color'] == datos['color'] && arreglo_models[i]['tipo'] == 'X') {
                arreglo_models[i] = datos;
                exist = 1;
            }
        }
        if (exist == 0)
            arreglo_models.push(datos);

        /* =========== ACTUALIZR TOTALES DE LA TABLA ===============*/
        list_coloraciones = $('.input_color_' + color);
        total_color = 0;
        for (i = 0; i < list_coloraciones.length; i++) {
            if (parseInt(list_coloraciones[i].value) > 0) {
                total_color += parseInt(list_coloraciones[i].value);
            }
        }
        $('#total_color_' + color).val(total_color);

        list_marcaciones = $('.input_marca_' + marcacion);
        total_marca = 0;
        for (i = 0; i < list_marcaciones.length; i++) {
            if (parseInt(list_marcaciones[i].value) > 0) {
                total_marca += parseInt(list_marcaciones[i].value);
            }
        }
        $('#total_marca_' + marcacion).html(total_marca);
        $('#total_piezas_' + marcacion).html(Math.round((total_marca / parseInt($('#cantidad_ramos').val())) * 100) / 100);

        list_total = $('.total_color');
        total_ramos = 0;
        for (i = 0; i < list_total.length; i++) {
            if (parseInt(list_total[i].value) > 0) {
                total_ramos += parseInt(list_total[i].value);
            }
        }
        $('#total_ramos').html(total_ramos);
        $('#total_piezas').html(Math.round((total_ramos / parseInt($('#cantidad_ramos').val())) * 100) / 100);
    }

    function update_distribucion(id_pedido) {
        if (parseInt($('#total_ramos').html()) == parseInt($('#cantidad_ramos').val()) * parseInt($('#cantidad_piezas').val())) {
            if (arreglo_models.length > 0) {
                /* =========== Actualziar pedido ============*/
                if ($('#form-update_pedido').valid()) {
                    datos = {
                        _token: '{{csrf_token()}}',
                        id_pedido: $('#id_pedido').val(),
                        id_especificacion_empaque: $('#id_especificacion_empaque').val(),
                        id_detalle_especificacionempaque: $('#id_detalle_especificacionempaque').val(),
                        fecha_pedido: $('#fecha_pedido').val(),
                        cantidad_piezas: $('#cantidad_piezas').val(),
                        id_empaque: $('#id_empaque').val(),
                        cantidad_ramos: $('#cantidad_ramos').val(),
                        id_clasificacion_ramo: $('#id_clasificacion_ramo').val(),
                        id_variedad: $('#id_variedad').val(),
                        id_empaque_e: $('#id_empaque_e').val(),
                        id_empaque_p: $('#id_empaque_p').val(),
                        tallos_x_ramos: $('#tallos_x_ramo').val(),
                        longitud_ramo: $('#longitud_ramo').val(),
                        id_unidad_medida: $('#id_unidad_medida').val(),
                        id_agencia_carga: $('#id_agencia_carga').val(),
                    };

                    post_jquery('{{url('pedidos/update_pedido_orden_semanal')}}', datos, function () {
                        /* =========== Actualziar Distribucion ============*/
                        datos = {
                            _token: '{{csrf_token()}}',
                            pedido: $('#id_pedido').val(),
                            arreglo: arreglo_models
                        };
                        cerrar_modals();
                        post_jquery('{{url('pedidos/update_distribucion')}}', datos, function () {
                            cerrar_modals();
                            distribuir_orden_semanal(id_pedido);
                            buscar_listado_pedidos();
                        });
                    });
                }
            } else {
                alerta('<div class="alert alert-warning text-center">No hay ninguna modificación</div>');
            }
        } else {
            alerta('<div class="alert alert-warning text-center">' +
                'La cantidad total de ramos en la distribución no coincide con la cantidad de ramos total indicada en el pedido</div>');
        }
    }

    function update_pedido() {
        if ($('#form-update_pedido').valid()) {
            datos = {
                _token: '{{csrf_token()}}',
                id_pedido: $('#id_pedido').val(),
                id_especificacion_empaque: $('#id_especificacion_empaque').val(),
                id_detalle_especificacionempaque: $('#id_detalle_especificacionempaque').val(),
                fecha_pedido: $('#fecha_pedido').val(),
                cantidad_piezas: $('#cantidad_piezas').val(),
                id_empaque: $('#id_empaque').val(),
                cantidad_ramos: $('#cantidad_ramos').val(),
                id_clasificacion_ramo: $('#id_clasificacion_ramo').val(),
                id_variedad: $('#id_variedad').val(),
                id_empaque_e: $('#id_empaque_e').val(),
                id_empaque_p: $('#id_empaque_p').val(),
                tallos_x_ramos: $('#tallos_x_ramo').val(),
                longitud_ramo: $('#longitud_ramo').val(),
                id_unidad_medida: $('#id_unidad_medida').val(),
                id_agencia_carga: $('#id_agencia_carga').val(),
            };

            post_jquery('{{url('pedidos/update_pedido_orden_semanal')}}', datos, function () {
                cerrar_modals();
                distribuir_orden_semanal(datos['id_pedido']);
                buscar_listado_pedidos();
            });
        }
    }

    function distribuir_marcaciones(pedido) {
        ids_marcacion = $('.id_marcacion');
        arreglo = [];
        for (i = 0; i < ids_marcacion.length; i++) {
            data = {
                id_marcacion: ids_marcacion[i].value,
                distribuciones: $('#cantidad_distribuciones_' + ids_marcacion[i].value).val()
            };

            arreglo.push(data);
        }
        datos = {
            arreglo: arreglo,
            id_pedido: pedido
        };
        get_jquery('{{url('pedidos/distribuir_marcaciones')}}', datos, function (retorno) {
            modal_view('modal_view_distribuir_marcaciones', retorno, '<i class="fa fa-fw fa-gift"></i> Distribuir marcaciones', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }
</script>