<table class="table-striped table-responsive table-bordered" width="100%">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Fecha
        </th>
        <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            <input type="date" id="fecha_pedido" name="fecha_pedido" required style="width: 100%" class="form-control">
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Cliente
        </th>
        <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            <select name="id_cliente" id="id_cliente" required style="width: 100%" class="form-control">
                <option value="">Seleccione...</option>
                @foreach($clientes as $item)
                    <option value="{{$item->id_cliente}}">
                        {{$item->detalle()->nombre}}
                    </option>
                @endforeach
            </select>
        </td>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Descripción
        </th>
        <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            <input type="text" id="descripcion" name="descripcion" class="form-control" required style="width: 100%">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="6">
            <table class="table-striped table-bordered table-responsive" width="100%">
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        Cantidad
                    </th>
                    <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="40%">
                        <input type="number" id="cantidad_cajas" name="cantidad_cajas" class="form-control" required
                               onkeypress="return isNumber(event)">
                    </td>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        Pieza
                    </th>
                    <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="40%">
                        <select name="id_empaque" id="id_empaque" class="form-control" required>
                            <option value="">Seleccione...</option>
                            @foreach($empaques as $item)
                                <option value="{{$item->id_empaque}}">
                                    {{explode('|',$item->nombre)[0]}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>
        </th>
    </tr>
    <tr>
        <td style="border-color: #9d9d9d" class="text-center" colspan="6">
            <div class="row" style="margin-top: 10px">
                <div class="col-md-2">
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Ramos</span>
                        <input type="number" id="cantidad_ramos" name="cantidad_ramos" onkeypress="return isNumber(event)" class="form-control"
                               style="width: 100%" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Clasificación</span>
                        <select name="id_empaque" id="id_empaque" class="form-control" required>
                            <option value="">...</option>
                            @foreach(getCalibresRamo() as $item)
                                @if($item->unidad_medida->tipo == 'P')
                                    <option value="{{$item->id_clasificacion_ramo}}">
                                        {{$item->nombre}} {{$item->unidad_medida->siglas}}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Variedad</span>
                        <select name="id_empaque" id="id_empaque" class="form-control" required>
                            <option value="">...</option>
                            @foreach(getVariedades() as $item)
                                <option value="{{$item->id_variedad}}">
                                    {{$item->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Envoltura</span>
                        <select name="id_empaque" id="id_empaque" class="form-control" required>
                            <option value="">...</option>
                            @foreach($envolturas as $item)
                                <option value="{{$item->id_empaque}}">
                                    {{$item->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Presentación</span>
                        <select name="id_empaque" id="id_empaque" class="form-control" required>
                            <option value="">...</option>
                            @foreach($presentaciones as $item)
                                <option value="{{$item->id_empaque}}">
                                    {{$item->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Longitud</span>
                        <input type="number" id="longitud_ramo" name="longitud_ramo" onkeypress="return isNumber(event)" class="form-control"
                               style="width: 100%" required>
                    </div>
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Tallos</span>
                        <input type="number" id="tallos_x_ramos" name="tallos_x_ramos" onkeypress="return isNumber(event)" class="form-control"
                               style="width: 100%" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">U. medida</span>
                        <select name="id_unidad_medida" id="id_unidad_medida" class="form-control" required>
                            <option value="">...</option>
                            @foreach(getUnidadesMedida() as $item)
                                <option value="{{$item->id_unidad_medida}}">
                                    {{$item->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

@include('adminlte.gestion.postcocecha.pedidos_ventas.partials._tabla_orden_semanal')

<script>
    set_min_today($('#fecha_pedido'));

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
                    ' style="width: 150px" class="text-center input_ramos_marc_col_' + c + '" onchange="calcular_total_ramos(' + c + ')">' +
                    '</td>';
                titles_columnas += '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="titles_columnas_' + c + '" name="titles_columnas_' + c + '"' +
                    ' style="width: 150px" placeholder="C #' + c + '" class="text-center">' +
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
                    '<th class="text-center" style="border-color: #9d9d9d; width: 350px">Texto</th>' + colorpickers_tx +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 200px">Total</th>' +
                    '</tr>' +
                    '<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 350px">Fondo</th>' + colorpickers_bg +
                    '</tr>' +
                    '<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 350px">Color</th>' + titles_columnas +
                    '</tr>');
            } else {
                $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="nombre_marcacion_' + f + '" name="nombre_marcacion_' + f + '"' +
                    ' style="width: 150px" placeholder="M #' + f + '" class="text-center">' +
                    '</th>' + html_columnas +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_ramos_marcacion_' + f + '" value="0"' +
                    ' style="background-color: #357CA5; color: white">' +
                    '</td>' +
                    '</tr>');
            }
        }

        total_ramos = '';
        total_piezas = '';
        for (c = 1; c <= col; c++) {
            total_ramos += '<td class="text-center" style="border-color: #9d9d9d; background-color: #357CA5">' +
                '<input type="text" class="text-center" readonly id="input_total_ramos_' + c + '" value="0"' +
                ' style="background-color: #357CA5; color: white">' +
                '</td>';
            total_piezas += '<td class="text-center" style="border-color: #9d9d9d; background-color: #357CA5">' +
                '<input type="text" class="text-center" readonly id="input_total_piezas_' + c + '" value="0"' +
                ' style="background-color: #357CA5; color: white">' +
                '</td>';
        }
        $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
            '<th class="text-center" style="border-color: #9d9d9d">Ramos</th>' + total_ramos +
            '</tr>' +
            '<tr class="row_marcaciones_colores">' +
            '<th class="text-center" style="border-color: #9d9d9d">Piezas</th>' + total_piezas +
            '</tr>');
    }

    function seleccionar_color(c) {
        $('.input_ramos_marc_col_' + c).css('background-color', $('#color_picker_bg_' + c).val());
        $('.input_ramos_marc_col_' + c).css('color', $('#color_picker_tx_' + c).val());
    }

    function calcular_total_ramos(c) {
        fil = $('#num_marcaciones').val();
        cantidad_ramos = parseInt($('#cantidad_ramos').val());

        total = 0;
        for (i = 1; i <= fil; i++) {
            if ($('#ramos_marcacion_color_' + i + '_' + c).val() != '')
                total += parseInt($('#ramos_marcacion_color_' + i + '_' + c).val());
        }

        $('#input_total_ramos_' + c).val(total);
        if ($('#cantidad_ramos').val() != '')
            $('#input_total_piezas_' + c).val(Math.round(total / cantidad_ramos));
    }
</script>