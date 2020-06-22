<form id="form_add_orden_semanal">
    <table class="table-striped table-responsive table-bordered" width="100%">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Fecha Pedido
            </th>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="date" id="fecha_pedido" name="fecha_pedido" required style="width: 100%" class="form-control"
                value="{{now()->toDateString()}}">
            </td>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Cliente
            </th>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <select name="id_cliente_orden_semanal" id="id_cliente_orden_semanal" required style="width: 100%" class="form-control"
                        onchange="buscar_agencia_carga(); listar_especificaciones_x_cliente()">
                    <option value="">Seleccione...</option>
                    @foreach($clientes as $item)
                        <option value="{{$item->id_cliente}}">
                            {{$item->detalle()->nombre}}
                        </option>
                    @endforeach
                </select>
            </td>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Facturar pedido con:
            </th>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <select class="form-control" id="id_configuracion_empresa" name="id_configuracion_empresa" title="Seleccione un empresa para facturar los pedidos">
                    @foreach(getConfiguracionEmpresa(null,true) as $empresa)
                        @php $lastPedido = getLastPedido(); @endphp
                        <option {{isset($lastPedido) ? (($lastPedido->id_configuracion_empresa === $empresa->id_configuracion_empresa) ? "selected" : "") : ""}}
                                style=" color: black" value="{{$empresa->id_configuracion_empresa}}">{{$empresa->nombre}}</option>
                    @endforeach
                </select>
            </td>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Agencia de Carga
            </th>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" id="div_agenia_carga"></td>
        </tr>
        {{--<tr>
            <td colspan="8">
                <table class="table-responsive table-bordered" style="width: 100%; border: 1px solid #9d9d9d; font-size: 0.9em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            CANTIDAD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            VARIEDAD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="150px">
                            CALIBRE
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            CAJA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            RAMOS X CAJA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            PRESENTACIÓN
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            TALLOS X RAMO
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            LONGITUD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            U. MEDIDA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="85px">
                            PRECIO
                        </th>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <input type="number" id="cantidad_cajas" name="cantidad_cajas" class="form-control" required
                                   onkeypress="return isNumber(event)" min="1">
                        </td>
                        <td class="text-center">
                            <select name="id_variedad" id="id_variedad" class="form-control" required>
                                @foreach(getVariedades() as $item)
                                    <option value="{{$item->id_variedad}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <select name="id_clasificacion_ramo" id="id_clasificacion_ramo" class="form-control" required>
                                @foreach(getCalibresRamo() as $item)
                                    @if($item->unidad_medida->tipo == 'P')
                                        <option value="{{$item->id_clasificacion_ramo}}">
                                            {{$item->nombre}} {{$item->unidad_medida->siglas}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <select name="id_empaque" id="id_empaque" class="form-control" required>
                                @foreach($empaques as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{explode('|',$item->nombre)[0]}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="number" id="cantidad_ramos" name="cantidad_ramos" onkeypress="return isNumber(event)"
                                   class="form-control" style="width: 100%" required min="1">
                        </td>
                        <td class="text-center">
                            <select name="id_empaque_p" id="id_empaque_p" class="form-control" required>
                                @foreach($presentaciones as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="number" id="tallos_x_ramos" name="tallos_x_ramos" onkeypress="return isNumber(event)"
                                   class="form-control" style="width: 100%" min="1">
                        </td>
                        <td class="text-center">
                            <input type="number" id="longitud_ramo" name="longitud_ramo" onkeypress="return isNumber(event)" class="form-control"
                                   style="width: 100%" min="1">
                        </td>
                        <td class="text-center">
                            <select name="id_unidad_medida" id="id_unidad_medida" class="form-control">
                                @foreach(getUnidadesMedida() as $item)
                                    @if($item->tipo == 'L')
                                        <option value="{{$item->id_unidad_medida}}">
                                            {{$item->siglas}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="number" id="precio" name="precio" class="form-control" required>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>--}}
    </table>
</form>

{{-- COLORES --}}
{{--<select name="select_colores" id="select_colores" style="display: none">
    @foreach($colores as $c)
        <option value="{{$c->id_color}}" style="background-color: {{$c->fondo}}; color: {{$c->texto}}">{{$c->nombre}}</option>
    @endforeach
</select>

@foreach($colores as $c)
    <input type="hidden" id="fondo_color_{{$c->id_color}}" value="{{$c->fondo}}">
    <input type="hidden" id="texto_color_{{$c->id_color}}" value="{{$c->texto}}">
    <input type="hidden" id="nombre_color_{{$c->id_color}}" value="{{$c->nombre}}">
@endforeach--}}
{{-- COLORES --}}

@include('adminlte.gestion.postcocecha.pedidos_ventas.partials._tabla_orden_semanal')

<script>
   // set_min_today($('#fecha_pedido'));
   // set_min_today($('#fecha_envio'));

    function construir_tabla() {
        col = $('#num_colores').val();
        fil = $('#num_marcaciones').val();

        $('.row_marcaciones_colores').remove();
        $('#div_tabla_distribucion_pedido').show();

        for (f = 0; f <= fil; f++) {
            html_columnas = '';
            titles_columnas = '';
            colorpickers_bg = '';
            colorpickers_tx = '';
            for (c = 1; c <= col; c++) {
                html_columnas += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" id="ramos_marcacion_color_' + f + '_' + c + '" name="ramos_marcacion_color_' + f + '_' + c + '" onkeypress="return isNumber(event)"' +
                    ' style="width: 60px" class="text-center input_ramos_marc_col_' + c + '" onchange="calcular_total_ramos(' + c + ',' + f + ')">' +
                    '</td>';
                titles_columnas += '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<select id="titles_columnas_' + c + '" name="titles_columnas_' + c + '"' +
                    ' style="width: 75px;font-size:11px" class="text-center" onchange="seleccionar_color(' + c + ')">' +
                    '<option value="">C # ' + c + '</option>' + $('#select_colores').html() +
                    '</select>' +
                    '</th>';
                colorpickers_bg += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="color" id="color_picker_bg_' + c + '" name="color_picker_bg_' + c + '"' +
                    ' style="width: 150px" placeholder="Color" value="#ffffff" onchange="seleccionar_color(' + c + ')">' +
                    '</td>';
                colorpickers_tx += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="color" id="color_picker_tx_' + c + '" name="color_picker_tx_' + c + '"' +
                    ' style="width: 150px" placeholder="Color" value="#000000" onchange="seleccionar_color(' + c + ')">' +
                    '</td>';
            }
            if (f == 0) {
                $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 85px">Color</th>' + titles_columnas +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Total</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Piezas</th>' +
                    '</tr>');
            } else {
                $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="nombre_marcacion_' + f + '" name="nombre_marcacion_' + f + '"' +
                    ' style="width: 150px" placeholder="M #' + f + '" class="text-center">' +
                    '</th>' + html_columnas +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_ramos_marcacion_' + f + '" value="0"' +
                    ' style="background-color: #357CA5; color: white; width: 60px">' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_piezas_' + f + '" value="0"' +
                    ' style="background-color: #357CA5; color: white; width: 60px">' +
                    '</td>' +
                    '</tr>');
            }
        }

        total_ramos = '';
        for (c = 1; c <= col; c++) {
            total_ramos += '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="text" class="text-center" readonly id="input_total_ramos_' + c + '" value="0"' +
                ' style="background-color: #357CA5; color: white; width: 60px">' +
                '</td>';
        }
        $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
            '<th class="text-center" style="border-color: #9d9d9d">Ramos</th>' + total_ramos +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_ramos_' + fil + '_' + col + '" name="total_ramos_' + fil + '_' + col + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_piezas_' + fil + '_' + col + '" name="total_piezas_' + fil + '_' + col + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
            '</th>' +
            '</tr>');
    }

    function seleccionar_color(c) {
        fondo = $('#fondo_color_' + $('#titles_columnas_' + c).val()).val();
        texto = $('#texto_color_' + $('#titles_columnas_' + c).val()).val();
        $('.input_ramos_marc_col_' + c).css('background-color', fondo);
        $('.input_ramos_marc_col_' + c).css('color', texto);
    }

    function calcular_total_ramos(c, f) {
        fil = $('#num_marcaciones').val();
        col = $('#num_colores').val();
        cantidad_ramos = parseInt($('#cantidad_ramos').val());

        total_col = 0;
        for (i = 1; i <= fil; i++) {
            if ($('#ramos_marcacion_color_' + i + '_' + c).val() != '')
                total_col += parseInt($('#ramos_marcacion_color_' + i + '_' + c).val());
        }

        $('#input_total_ramos_' + c).val(total_col);

        total_fila = 0;
        for (i = 1; i <= col; i++) {
            if ($('#ramos_marcacion_color_' + f + '_' + i).val() != '')
                total_fila += parseInt($('#ramos_marcacion_color_' + f + '_' + i).val());
        }

        $('#input_total_ramos_marcacion_' + f).val(total_fila);
        if ($('#cantidad_ramos').val() != '')
            $('#input_total_piezas_' + f).val(Math.round((total_fila / cantidad_ramos) * 100) / 100);

        total_ramos = 0;
        total_piezas = 0;
        for (i = 1; i <= fil; i++) {
            total_ramos += parseInt($('#input_total_ramos_marcacion_' + i).val());
            total_piezas += parseFloat($('#input_total_piezas_' + i).val());
        }

        $('#total_ramos_' + fil + '_' + col).val(total_ramos);
        $('#total_piezas_' + fil + '_' + col).val(total_piezas);
    }

    function buscar_agencia_carga() {
        datos = {
            id_cliente: $('#id_cliente_orden_semanal').val()
        };
        get_jquery('{{url('pedidos/buscar_agencia_carga')}}', datos, function (retorno) {
            $('#div_agenia_carga').html(retorno);
        });
    }

    function listar_especificaciones_x_cliente() {
        datos = {
            id_cliente: $('#id_cliente_orden_semanal').val()
        };
        get_jquery('{{url('pedidos/orden_semanal/listar_especificaciones_x_cliente')}}', datos, function (retorno) {
            $('#div_especificaciones_orden_semanal').html(retorno);
            if($($(retorno)[0]).hasClass('well_1')){
                $("#div_tabla_distribucion_pedido").css('display','none');

            }else{
                $("#div_tabla_distribucion_pedido").css('display','block');
                $("#msj_busqueda_especificacion").html("El cliente no posee especificaciones");
            }


        });
    }

    function store_orden_semanal() {
        if ($('#id_cliente_orden_semanal').val() != '') {
            nueva_esp = '';
            arreglo_esp = [];
            if ($('#cantidad_cajas').val() > 0)
                if ($('#form_add_orden_semanal').valid() && $('#form_marcas_colores').valid()) {
                    col = $('#num_colores').val();
                    fil = $('#num_marcaciones').val();

                    if (col > 0 && fil > 0) {
                        if ($('#total_piezas_' + fil + '_' + col).val() == $('#cantidad_cajas').val()) {
                            if ($('#total_ramos_' + fil + '_' + col).val() == parseInt($('#cantidad_ramos').val()) * parseInt($('#cantidad_cajas').val()) &&
                                $('#total_piezas_' + fil + '_' + col).val() == $('#cantidad_cajas').val()) {
                                marcaciones = [];
                                coloraciones = [];

                                for (f = 1; f <= fil; f++) {
                                    if ($('#nombre_marcacion_' + f).val() != '') {
                                        cantidades = [];
                                        for (c = 1; c <= col; c++) {
                                            if ($('#titles_columnas_' + c).val() != '') {
                                                cantidades.push({
                                                    cantidad: $('#ramos_marcacion_color_' + f + '_' + c).val()
                                                });
                                            } else {
                                                alert('Faltan datos (nombre de colores) por ingresar en la tabla.');
                                                return false;
                                            }
                                            if (f == 1) {
                                                coloraciones.push($('#titles_columnas_' + c).val());
                                            }
                                        }
                                        marcaciones.push({
                                            nombre: $('#nombre_marcacion_' + f).val(),
                                            ramos: $('#input_total_ramos_marcacion_' + f).val(),
                                            piezas: $('#input_total_piezas_' + f).val(),
                                            coloraciones: cantidades
                                        });
                                    } else {
                                        alert('Faltan datos (nombres de marcaciones) por ingresar en la tabla.');
                                        return false;
                                    }
                                }

                                nueva_esp = {
                                    cantidad_cajas: $('#cantidad_cajas').val(),
                                    id_empaque: $('#id_empaque').val(),
                                    cantidad_ramos: $('#cantidad_ramos').val(),
                                    id_clasificacion_ramo: $('#id_clasificacion_ramo').val(),
                                    id_variedad: $('#id_variedad').val(),
                                    /*id_empaque_e: $('#id_empaque_e').val(),*/
                                    id_empaque_p: $('#id_empaque_p').val(),
                                    longitud_ramo: $('#longitud_ramo').val(),
                                    tallos_x_ramos: $('#tallos_x_ramos').val(),
                                    id_unidad_medida: $('#id_unidad_medida').val(),
                                    precio: $('#precio').val(),
                                    num_marcaciones: $('#num_marcaciones').val(),
                                    num_colores: $('#num_colores').val(),
                                    marcaciones: marcaciones,
                                    coloraciones: coloraciones,
                                };
                            } else {
                                alerta('<div class="alert alert-info text-center">' +
                                    'La cantidad de ramos totales no coinciden con la cantidad de ramos especificados en el pedido</div>');
                            }
                        } else {
                            alerta('<div class="alert alert-warning text-center">Las cantidades de piezas distribuidas no coinciden con las pedidas</div>');
                            $('#cantidad_cajas').addClass('error');
                            return false;
                        }
                    } else {
                        alerta('<div class="alert alert-warning text-center">Faltan los datos de las marcaciones/colores</div>');
                        return false;
                    }
                }   // NUEVA ESPECIFICACION
            ids_especificacion = $('.id_especificacion');
            for (esp = 0; esp < ids_especificacion.length; esp++) { // ESPECIFICACIONES
                id_esp = ids_especificacion[esp].value;
                if (parseInt($('#cant_piezas_esp_' + id_esp).val()) > 0) {
                    if ($('#form_especificacion_' + id_esp).valid()) {
                        arreglo_esp_emp = [];
                        ids_esp_emp = $('.id_especificacion_empaque_' + id_esp);
                        for (esp_emp = 0; esp_emp < ids_esp_emp.length; esp_emp++) {    // ESPECIFICACIONES_EMPAQUE
                            id_esp_emp = ids_esp_emp[esp_emp].value;
                            ids_det_esp = $('.id_det_esp_' + id_esp_emp);

                            num_marcaciones = $('#num_marcaciones_' + id_esp_emp).val();
                            num_colores = $('#num_colores_' + id_esp_emp).val();

                            if (ids_det_esp.length > 1) {   // mixta
                                tipo = 1;
                                if ($('#cant_piezas_esp_' + id_esp).val() != $('#total_piezas_' + id_esp_emp).val()) {
                                    alerta('<div class="alert alert-warning text-center">Las cantidades de piezas distribuidas no coinciden con las pedidas</div>');
                                    $('#cant_piezas_esp_' + id_esp).addClass('error');
                                    return false;
                                }
                            } else {    // sencilla
                                tipo = 0;
                                if ($('#cant_piezas_esp_' + id_esp).val() != $('#total_piezas_' + num_marcaciones + '_' + num_colores +
                                    '_' + id_esp_emp).val()) {
                                    alerta('<div class="alert alert-warning text-center">Las cantidades de piezas distribuidas no coinciden con las pedidas</div>');
                                    $('#cant_piezas_esp_' + id_esp).addClass('error');
                                    return false;
                                }
                            }
                            arreglo_det_esp = [];
                            for (det_esp = 0; det_esp < ids_det_esp.length; det_esp++) {    //DETALLES_ESPECIFICACION_EMPAQUE
                                id_det_esp = ids_det_esp[det_esp].value;
                                arreglo_det_esp.push({
                                    id_det_esp: id_det_esp,
                                    precio: $('#precio_det_' + id_det_esp + '_esp_' + id_esp_emp).val(),
                                    ramos_modificados : $("input#add_esp_ramos_x_caja_det_esp_"+ id_det_esp + '_' + id_esp_emp).val()
                                });
                            }

                            marcaciones = [];
                            coloraciones = [];
                            for (f = 1; f <= num_marcaciones; f++) {
                                if ($('#nombre_marcacion_' + f + '_' + id_esp_emp).val() != '') {
                                    arreglo_colores = [];
                                    for (c = 1; c <= num_colores; c++) {
                                        if ($('#titles_columnas_' + c + '_' + id_esp_emp).val() != '') {
                                            cant_x_det_esp = [];
                                            if (tipo == 1) {    // mixta
                                                for (det_esp = 0; det_esp < ids_det_esp.length; det_esp++) {    //DETALLES_ESPECIFICACION_EMPAQUE
                                                    id_det_esp = ids_det_esp[det_esp].value;
                                                    if (parseInt($('#ramos_marcacion_color_' + f + '_' + c + '_' + id_esp_emp + '_det_' + id_det_esp).val()) > 0)
                                                        cant_x_det_esp.push({
                                                            id_det_esp: id_det_esp,
                                                            cantidad: $('#ramos_marcacion_color_' + f + '_' + c + '_' + id_esp_emp + '_det_' + id_det_esp).val()
                                                        });
                                                    else
                                                        cant_x_det_esp.push({
                                                            id_det_esp: id_det_esp,
                                                            cantidad: 0
                                                        });
                                                }
                                            } else {    // sencilla
                                                id_det_esp = ids_det_esp[0].value;
                                                cant_x_det_esp.push({
                                                    id_det_esp: id_det_esp,
                                                    cantidad: $('#ramos_marcacion_color_' + f + '_' + c + '_' + id_esp_emp).val()
                                                });
                                            }
                                            arreglo_colores.push({
                                                cant_x_det_esp: cant_x_det_esp
                                            });
                                            if (f == 1)
                                                coloraciones.push($('#titles_columnas_' + c + '_' + id_esp_emp).val());
                                        } else {
                                            alert('Faltan datos (nombres de colores) por ingresar en la tabla.');
                                            return false;
                                        }
                                    }
                                    marcaciones.push({
                                        nombre: $('#nombre_marcacion_' + f + '_' + id_esp_emp).val(),
                                        ramos: $('#input_total_ramos_marcacion_' + f + '_' + id_esp_emp).val(),
                                        piezas: $('#input_total_piezas_' + f + '_' + id_esp_emp).val(),
                                        arreglo_colores: arreglo_colores
                                    });
                                } else {
                                    alert('Faltan datos (nombres de marcaciones) por ingresar en la tabla.');
                                    return false;
                                }
                            }

                            arreglo_esp_emp.push({
                                id_esp_emp: id_esp_emp,
                                tipo: tipo,
                                num_marcaciones: num_marcaciones,
                                num_colores: num_colores,
                                marcaciones: marcaciones,
                                coloraciones: coloraciones,
                                arreglo_det_esp: arreglo_det_esp
                            });
                        }
                        arreglo_esp.push({
                            id_esp: id_esp,
                            cant_piezas: $('#cant_piezas_esp_' + id_esp).val(),
                            arreglo_esp_emp: arreglo_esp_emp
                        });
                    }
                }
            }

            if (nueva_esp != '' || arreglo_esp.length > 0) {
                datos = {
                    _token: '{{csrf_token()}}',
                    fecha_pedido: $('#fecha_pedido').val(),
                    fecha_envio: $('#fecha_pedido').val(),
                    id_cliente: $('#id_cliente_orden_semanal').val(),
                    id_agencia_carga: $('#id_agencia_carga').val(),
                    nueva_esp: nueva_esp,
                    arreglo_esp: arreglo_esp,
                    id_configuracion_empresa : $("select#id_configuracion_empresa").val()
                };
                empresa = $("select#id_configuracion_empresa option:selected").text();
                modal_quest('modal_edit_pedido',
                    '<div class="alert alert-info text-center"><p>El pedido sera facturado con la empresa '+empresa+'</p></div>', 'Guarda Pedido', true, false, '40%', function () {
                        $.LoadingOverlay('show');
                        $.post('{{url('pedidos/store_orden_semanal')}}', datos, function (retorno) {
                            if (retorno.success) {
                                alerta_accion(retorno.mensaje, function () {
                                    cerrar_modals();
                                    editar_pedido_tinturado(retorno.id_pedido, 0);
                                    listar_resumen_pedidos($("#fecha_pedidos_search").val(), true);
                                });
                            } else {
                                alerta(retorno.mensaje);
                            }

                        }, 'json').fail(function (retorno) {
                            console.log(retorno);
                            alerta_errores(retorno.responseText);
                            alerta('Ha ocurrido un problema');
                        }).always(function () {
                            $.LoadingOverlay('hide');
                        });
                });
            }
        }
    }
</script>
