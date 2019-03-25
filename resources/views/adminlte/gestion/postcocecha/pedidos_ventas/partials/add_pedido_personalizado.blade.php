<div class="box box-success">
    <div class="box-header with-border">
        <div class="row">
            <div class="col-md-6">
                <h3 class="box-title">
                    Añadir pedidos
                </h3>
            </div>
            <div class="col-md-3">
                <div class="form-group input-group pull-right" style="margin-bottom: 1px">
                    <span class="input-group-addon" style="background-color: #e9ecef">Fecha de pedidos</span>
                    <input type="date" id="fecha_pedido" name="fecha_pedido" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group input-group pull-right" style="margin-bottom: 1px">
                    <span class="input-group-addon" style="background-color: #e9ecef">Añadir # formularios</span>
                    <input type="number" id="cantidad_formularios" name="cantidad_formularios" min="1" max="20" class="form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" title="Añadir formulario" onclick="add_forms()" id="btn_add_forms">
                            <i class="fa fa-fw fa-plus"></i>
                        </button>
                    </span>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-danger" title="Quitar formulario" onclick="remove_forms()" style="display: none"
                                id="btn_remove_forms">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <form id="form-add_pedido_personalizado">
            <div style="overflow-x: scroll">
                <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d"
                       id="table_add_pedido_personalizado">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            Cliente
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" width="7%">
                            Cant. Cajas
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            Pieza
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" width="7%">
                            Ramos x Cajas
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            Calibre
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            Variedad
                        </th>
                        {{--<th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            Envoltura
                        </th>--}}
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
                            Agencias
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white"
                            title="Hacer especificaciones">
                            <input type="checkbox" id="check_make_especificacion_all" name="check_make_especificacion_all"
                                   onchange="select_check_all()">
                        </th>
                    </tr>
                    <tr id="row_form_1">
                        <td class="text-center" style="border-color: #9d9d9d">

                            <select name="id_cliente_1" id="id_cliente_1" required style="width: 100%" onchange="listar_agencias_carga(1)">
                                @foreach($clientes as $cliente)
                                    <option value="{{$cliente->id_cliente}}">{{$cliente->nombre}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="number" id="cantidad_piezas_1" name="cantidad_piezas_1" required style="width: 100%"
                                   onkeypress="return isNumber(event)" min="1">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <select name="id_empaque_1" id="id_empaque_1" required style="width: 100%">
                                @foreach($cajas as $caja)
                                    <option value="{{$caja->id_empaque}}">{{explode('|',$caja->nombre)[0]}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="number" id="cantidad_ramos_1" name="cantidad_ramos_1" required style="width: 100%"
                                   onkeypress="return isNumber(event)" min="1">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <select name="id_clasificacion_ramo_1" id="id_clasificacion_ramo_1" required style="width: 100%">
                                @foreach($calibres as $item)
                                    @if($item->unidad_medida->tipo == 'P')
                                        <option value="{{$item->id_clasificacion_ramo}}">
                                            {{$item->nombre}}{{$item->unidad_medida->siglas}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <select name="id_variedad_1" id="id_variedad_1" required style="width: 100%">
                                @foreach($variedades as $item)
                                    <option value="{{$item->id_variedad}}">
                                        {{$item->siglas}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        {{--<td class="text-center" style="border-color: #9d9d9d">
                            <select name="id_empaque_e_1" id="id_empaque_e_1" required style="width: 100%">
                                @foreach($envolturas as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{explode('|',$item->nombre)[0]}}
                                    </option>
                                @endforeach
                            </select>
                        </td>--}}
                        <td class="text-center" style="border-color: #9d9d9d">
                            <select name="id_empaque_p_1" id="id_empaque_p_1" required style="width: 100%">
                                @foreach($presentaciones as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{explode('|',$item->nombre)[0]}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="number" id="tallos_x_ramo_1" name="tallos_x_ramo_1" style="width: 100%"
                                   onkeypress="return isNumber(event)" min="1">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="number" id="longitud_ramo_1" name="longitud_ramo_1" style="width: 100%"
                                   onkeypress="return isNumber(event)" min="1">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <select name="id_unidad_medida_1" id="id_unidad_medida_1" style="width: 100%">
                                @foreach($unidades_medida as $item)
                                    <option value="{{$item->id_unidad_medida}}">
                                        {{$item->siglas}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" id="td_agencia_carga_1"></td>
                        <td class="text-center" style="border-color: #9d9d9d" title="Hacer especificaciones">
                            <input type="checkbox" class="check_make_especificacion" id="check_make_especificacion_1"
                                   name="check_make_especificacion_1">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="text-center" style="margin-top: 10px">
                <button type="button" class="btn btn-xs btn-success" title="Guardar" onclick="store_pedido_personalizado()">
                    <i class="fa fa-fw fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<input type="hidden" id="cant_forms" value="1">

<script>
    set_min_today($('#fecha_pedido'));

    listar_agencias_carga(1);

    function add_forms() {
        i = $('#cant_forms').val();
        i++;
        if ($('#cantidad_formularios').val() != '')
            total_formularios = $('#cantidad_formularios').val();
        else
            total_formularios = 1;
        for (y = 1; y <= total_formularios; y++, i++) {
            $('#btn_remove_forms').show();
            $('#table_add_pedido_personalizado').append('<tr id="row_form_' + i + '">' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<select name="id_cliente_' + i + '" id="id_cliente_' + i + '" required style="width: 100%" onchange="listar_agencias_carga(' + i + ')">' +
                $('#id_cliente_1').html() +
                '</select>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" id="cantidad_piezas_' + i + '" name="cantidad_piezas_' + i + '" required style="width: 100%"' +
                ' onkeypress="return isNumber(event)" min="1">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<select name="id_empaque_' + i + '" id="id_empaque_' + i + '" required style="width: 100%">' +
                $('#id_empaque_1').html() +
                '</select>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" id="cantidad_ramos_' + i + '" name="cantidad_ramos_' + i + '" required style="width: 100%"' +
                ' onkeypress="return isNumber(event)" min="1">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<select name="id_clasificacion_ramo_' + i + '" id="id_clasificacion_ramo_' + i + '" required style="width: 100%">' +
                $('#id_clasificacion_ramo_1').html() +
                '</select>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<select name="id_variedad_' + i + '" id="id_variedad_' + i + '" required style="width: 100%">' +
                $('#id_variedad_1').html() +
                '</select>' +
                '</td>' +
                /*'<td class="text-center" style="border-color: #9d9d9d">' +
                '<select name="id_empaque_e_' + i + '" id="id_empaque_e_' + i + '" required style="width: 100%">' +
                $('#id_empaque_e_1').html() +
                '</select>' +
                '</td>' +*/
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<select name="id_empaque_p_' + i + '" id="id_empaque_p_' + i + '" required style="width: 100%">' +
                $('#id_empaque_p_1').html() +
                '</select>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" id="tallos_x_ramo_' + i + '" name="tallos_x_ramo_' + i + '" style="width: 100%"' +
                ' onkeypress="return isNumber(event)" min="1">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" id="longitud_ramo_' + i + '" name="longitud_ramo_' + i + '" style="width: 100%"' +
                ' onkeypress="return isNumber(event)" min="1"></td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<select name="id_unidad_medida_' + i + '" id="id_unidad_medida_' + i + '" style="width: 100%">' +
                $('#id_unidad_medida_1').html() +
                '</select>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d" id="td_agencia_carga_' + i + '"></td>' +
                '<td class="text-center" style="border-color: #9d9d9d" title="Hacer especificaciones">' +
                '<input type="checkbox" class="check_make_especificacion" id="check_make_especificacion_' + i + '"' +
                ' name="check_make_especificacion_' + i + '">' +
                '</td>' +
                '</tr>');
            listar_agencias_carga(i);
            $('#cant_forms').val(i);
        }
    }

    function remove_forms() {
        i = $('#cant_forms').val();
        $('#row_form_' + i).remove();
        i--;
        $('#cant_forms').val(i);
        if (i == 1)
            $('#btn_remove_forms').hide();
    }

    function select_check_all() {
        if ($('#check_make_especificacion_all').prop('checked')) {
            $('.check_make_especificacion').prop('checked', true);
        } else {
            $('.check_make_especificacion').prop('checked', false);
        }
    }

    function listar_agencias_carga(i) {
        datos = {
            id_cliente: $('#id_cliente_' + i).val(),
            pos: i,
        };
        get_jquery('{{url('pedidos/listar_agencias_carga')}}', datos, function (retorno) {
            $('#td_agencia_carga_' + i).html(retorno);
        });
    }

    function store_pedido_personalizado() {
        if ($('#form-add_pedido_personalizado').valid()) {
            arreglo = [];
            for (i = 1; i <= $('#cant_forms').val(); i++) {
                data = {
                    id_cliente: $('#id_cliente_' + i).val(),
                    cantidad_piezas: $('#cantidad_piezas_' + i).val(),
                    id_empaque: $('#id_empaque_' + i).val(),
                    cantidad_ramos: $('#cantidad_ramos_' + i).val(),
                    id_clasificacion_ramo: $('#id_clasificacion_ramo_' + i).val(),
                    id_variedad: $('#id_variedad_' + i).val(),
                    /*id_empaque_e: $('#id_empaque_e_' + i).val(),*/
                    id_empaque_p: $('#id_empaque_p_' + i).val(),
                    tallos_x_ramo: $('#tallos_x_ramo_' + i).val(),
                    longitud_ramo: $('#longitud_ramo_' + i).val(),
                    id_unidad_medida: $('#id_unidad_medida_' + i).val(),
                    id_agencia_carga: $('#id_agencia_carga_' + i).val(),
                    check_make_especificacion: $('#check_make_especificacion_' + i).prop('checked'),
                };
                arreglo.push(data);
            }
            datos = {
                _token: '{{csrf_token()}}',
                fecha_pedido: $('#fecha_pedido').val(),
                arreglo: arreglo
            };
            post_jquery('{{url('pedidos/store_pedido_personalizado')}}', datos, function () {
                listar_resumen_pedidos($("#fecha_pedidos_search").val(), true);
                cerrar_modals();
                add_pedido_personalizado();
            });
        }
    }
</script>
