@if(count($fechas) > 0 && count($combinaciones)>0)
    <form id="form-update_stock_empaquetado" class="pull-left">
        <input type="hidden" id="id_variedad" value="{{$variedad->id_variedad}}">
        <span class="badge">{{$stock_apertura->cantidad_ingresada}}</span> ramos de <span
                class="badge">{{$variedad->nombre}}</span> sacados de apertura -

        <input type="number" id="ramos_restantes_aperturas" placeholder="ramos restantes" min="0" max="{{$stock_apertura->cantidad_ingresada}}"
               required>
        <button type="button" class="btn btn-xs btn-success" onclick="update_stock_empaquetado()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
        <input type="hidden" id="id_stock_empaquetado" value="{{$stock_apertura->id_stock_empaquetado}}">
    </form>

    <label for="check_dias_maduracion" class="pull-right" style="margin-left: 5px">Seleccionar flores con mayor días de maduración</label>
    <input type="checkbox" class="pull-right" id="check_dias_maduracion" checked>
    <table class="table table-bordered table-striped table-responsive" width="100%" id="table_clasificacion_blanco"
           style="border: 1px solid #9d9d9d; font-size: 0.8em; margin-top: 10px; margin-bottom: 0">
        <tr>
            <td style="border-color: #9d9d9d"></td>
            @php
                $pos_fecha = 1;
            @endphp
            @foreach($fechas as $fecha)
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d; border-width: {{$pos_fecha == 1 ? '3px' : ''}}">
                    {{getDias(TP_ABREVIADO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime($fecha->fecha_pedido)))]}}<br>
                    <small>{{$fecha->fecha_pedido}}</small>
                    <input type="hidden" id="fecha_{{$pos_fecha}}" value="{{$fecha->fecha_pedido}}">
                </th>
                @php
                    $pos_fecha++;
                @endphp
            @endforeach
            <th class="text-center" style="background-color: #357CA5; border-color: #9d9d9d; color: white">
                Cuarto Frío
            </th>
            <th class="text-center" style="background-color: #357CA5; border-color: #9d9d9d; color: white">
                Armado
            </th>
        </tr>
        @php
            $pos_comb = 1;
        @endphp
        @foreach($combinaciones as $item)
            @php
                if($item->tallos_x_ramos != '')
                    $tallos_x_ramo = $item->tallos_x_ramos.' tallos ';
                else
                    $tallos_x_ramo = '';
                if($item->longitud_ramo != '' && $item->id_unidad_medida != '')
                    $longitud_ramo = $item->longitud_ramo.' '.getUnidadMedida($item->id_unidad_medida)->siglas.' ';
                else
                    $longitud_ramo = '';
                $texto = getCalibreRamoById($item->id_clasificacion_ramo)->nombre.' '.getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas.' '.
                    $tallos_x_ramo.''.$longitud_ramo.''.getEmpaque($item->id_empaque_e)->nombre.' '.getEmpaque($item->id_empaque_p)->nombre;
            @endphp
            <tr>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" id="th_pedidos_{{$pos_comb}}">
                    <strong class="pull-left">
                        {{$texto}}
                    </strong>
                    <span class="badge pull-right" title="Total">
                        {{$item->cantidad}}
                    </span>
                    <input type="hidden" id="texto_{{$pos_comb}}" value="{{$texto}}">
                    <input type="hidden" id="clasificacion_ramo_{{$pos_comb}}" value="{{$item->id_clasificacion_ramo}}">
                    <input type="hidden" id="tallos_x_ramo_{{$pos_comb}}" value="{{$item->tallos_x_ramos}}">
                    <input type="hidden" id="longitud_ramo_{{$pos_comb}}" value="{{$item->longitud_ramo}}">
                    <input type="hidden" id="id_empaque_e_{{$pos_comb}}" value="{{$item->id_empaque_e}}">
                    <input type="hidden" id="id_empaque_p_{{$pos_comb}}" value="{{$item->id_empaque_p}}">
                    <input type="hidden" id="id_unidad_medida_{{$pos_comb}}" value="{{$item->id_unidad_medida}}">
                </th>
                @php
                    $pos_fecha = 1;
                @endphp
                @foreach($fechas as $fecha)
                    <td class="text-center"
                        style="border-color: #9d9d9d; border-right-width: {{$pos_fecha == 1 ? '3px' : ''}}; border-left-width: {{$pos_fecha == 1 ? '3px' : ''}};"
                        onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')">
                        {{getCantidadRamosPedidosForCB($fecha->fecha_pedido,$item->id_variedad,$item->id_clasificacion_ramo,$item->id_empaque_e,$item->id_empaque_p,
                        $item->tallos_x_ramos,$item->longitud_ramo,$item->id_unidad_medida)}}
                        <input type="hidden" id="pedido_{{$pos_comb}}_{{$pos_fecha}}" value="{{getCantidadRamosPedidosForCB($fecha->fecha_pedido,$item->id_variedad,$item->id_clasificacion_ramo,$item->id_empaque_e,$item->id_empaque_p,
                        $item->tallos_x_ramos,$item->longitud_ramo,$item->id_unidad_medida)}}">
                    </td>
                    @php
                        $pos_fecha++;
                    @endphp
                @endforeach
                <td class="text-center" style=" border-color: #9d9d9d;" width="7%">
                    <input type="hidden" id="inventario_frio_{{$pos_comb}}" value="{{getDisponibleInventarioFrio($item->id_variedad,$item->id_clasificacion_ramo,$item->id_empaque_e,$item->id_empaque_p,
                        $item->tallos_x_ramos,$item->longitud_ramo,$item->id_unidad_medida)}}">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" id="btn_inventario_{{$pos_comb}}" onclick="maduracion('{{$pos_comb}}')">
                        {{getDisponibleInventarioFrio($item->id_variedad,$item->id_clasificacion_ramo,$item->id_empaque_e,$item->id_empaque_p,
                    $item->tallos_x_ramos,$item->longitud_ramo,$item->id_unidad_medida)}}
                    </button>
                </td>
                <td class="text-center" style=" border-color: #9d9d9d;" width="7%">
                    <input type="number" style="width: 100%" id="armar_{{$pos_comb}}" min="0"
                           onchange="calcular_inventario_i('{{$pos_comb}}', '{{$pos_comb-1}}')"
                           class="text-center" value="0">
                </td>
            </tr>
            @php
                $pos_comb++;
            @endphp
        @endforeach
        <tr>
            <td style="border-color: #9d9d9d"></td>
            @php
                $pos_fecha = 1;
            @endphp
            @foreach($fechas as $fecha)
                <td style="border-color: #9d9d9d; border-bottom-width: {{$pos_fecha == 1 ? '3px' : ''}}; background-color: #e9ecef;
                        border-right-width: {{$pos_fecha == 1 ? '3px' : ''}}; border-left-width: {{$pos_fecha == 1 ? '3px' : ''}};"
                    class="text-center">
                    <button type="button" class="btn btn-xs btn-primary" title="Mandar a armar">
                        <i class="fa fa-fw fa-gift"></i>
                    </button>
                    @if($pos_fecha == 1)
                        <button type="button" class="btn btn-xs btn-success" title="Confirmar pedidos"
                                onclick="confirmar_pedidos('{{$pos_comb-1}}')">
                            <i class="fa fa-fw fa-check"></i>
                        </button>
                    @endif
                </td>
                @php
                    $pos_fecha++;
                @endphp
            @endforeach
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2">
                <button type="button" class="btn btn-xs btn-success" title="Guardar armados"
                        onclick="store_armar('{{$pos_comb-1}}')">
                    <i class="fa fa-fw fa-save"></i> Guardar
                </button>
            </td>
        </tr>
    </table>

    <input type="hidden" id="pos_comb_total" value="{{$pos_comb-1}}">

    <div style="border-top: 1px dotted #9d9d9d; padding: 5px; margin-top: 2px" class="well" title="Opciones">
        <input type="checkbox" id="estrechar_tabla" onchange="estrechar_tabla('table_clasificacion_blanco', $(this).prop('checked'))">
        <label for="estrechar_tabla" class="mouse-hand">Estrechar tabla</label>
    </div>

    <script>
        function calcular_inventario_i(pos_comb) {
            armar = 0;
            if ($('#armar_' + pos_comb).val() != '')
                armar = parseFloat($('#armar_' + pos_comb).val());
            inv = armar + parseFloat($('#inventario_frio_' + pos_comb).val());
            $('#btn_inventario_' + pos_comb).html(inv);
        }

        function confirmar_pedidos(pos_comb) {
            arreglo = [];
            for (i = 1; i <= pos_comb; i++) {
                data = {
                    pedido: parseFloat($('#pedido_' + i + '_' + 1).val()),
                    inventario: parseFloat($('#inventario_frio_' + i).val()),
                    armar: $('#armar_' + i).val() != '' ? parseFloat($('#armar_' + i).val()) : 0,
                    clasificacion_ramo: $('#clasificacion_ramo_' + i).val(),
                    tallos_x_ramo: $('#tallos_x_ramo_' + i).val(),
                    longitud_ramo: $('#longitud_ramo_' + i).val(),
                    id_empaque_e: $('#id_empaque_e_' + i).val(),
                    id_empaque_p: $('#id_empaque_p_' + i).val(),
                    id_unidad_medida: $('#id_unidad_medida_' + i).val(),
                    texto: $('#texto_' + i).val()
                };
                if (data['pedido'] <= (data['inventario'] + data['armar'])) {
                    $('#th_pedidos_' + i).removeClass('error');

                    arreglo.push(data);
                } else {
                    alert('Faltan ramos por armar para los pedidos de "' + $('#texto_' + i).val() + '"');
                    $('#th_pedidos_' + i).addClass('error');
                    return;
                }
            }
            datos = {
                _token: '{{csrf_token()}}',
                id_variedad: $('#id_variedad').val(),
                arreglo: arreglo,
                fecha_pedidos: $('#fecha_' + 1).val(),
                check_maduracion: $('#check_dias_maduracion').prop('checked'),
            };
            post_jquery('{{url('clasificacion_blanco/confirmar_pedidos')}}', datos, function () {
                cerrar_modals();
                listar_clasificacion_blanco($('#id_variedad').val());
            });
        }

        function store_armar(pos_comb) {
            arreglo = [];
            armar = 0;
            for (i = 1; i <= pos_comb; i++) {
                data = {
                    pedido: parseFloat($('#pedido_' + i + '_' + 1).val()),
                    inventario: parseFloat($('#inventario_frio_' + i).val()),
                    armar: $('#armar_' + i).val() != '' ? parseFloat($('#armar_' + i).val()) : 0,
                    clasificacion_ramo: $('#clasificacion_ramo_' + i).val(),
                    tallos_x_ramo: $('#tallos_x_ramo_' + i).val(),
                    longitud_ramo: $('#longitud_ramo_' + i).val(),
                    id_empaque_e: $('#id_empaque_e_' + i).val(),
                    id_empaque_p: $('#id_empaque_p_' + i).val(),
                    id_unidad_medida: $('#id_unidad_medida_' + i).val(),
                    texto: $('#texto_' + i).val()
                };
                armar += data['armar'];
                arreglo.push(data);
            }
            if (armar > 0) {
                datos = {
                    _token: '{{csrf_token()}}',
                    id_variedad: $('#id_variedad').val(),
                    arreglo: arreglo,
                };
                post_jquery('{{url('clasificacion_blanco/store_armar')}}', datos, function () {
                    cerrar_modals();
                    listar_clasificacion_blanco($('#id_variedad').val());
                });
            }
        }

        function maduracion(pos_comb) {
            arreglo = [];
            for (i = 1; i <= $('#pos_comb_total').val(); i++) {
                if (i != pos_comb) {
                    data = {
                        inventario: parseFloat($('#inventario_frio_' + i).val()),
                        clasificacion_ramo: $('#clasificacion_ramo_' + i).val(),
                        tallos_x_ramo: $('#tallos_x_ramo_' + i).val(),
                        longitud_ramo: $('#longitud_ramo_' + i).val(),
                        id_empaque_e: $('#id_empaque_e_' + i).val(),
                        id_empaque_p: $('#id_empaque_p_' + i).val(),
                        id_unidad_medida: $('#id_unidad_medida_' + i).val(),
                        texto: $('#texto_' + i).val()
                    };
                    arreglo.push(data);
                }
            }
            datos = {
                id_variedad: $('#id_variedad').val(),
                clasificacion_ramo: $('#clasificacion_ramo_' + pos_comb).val(),
                tallos_x_ramo: $('#tallos_x_ramo_' + pos_comb).val(),
                longitud_ramo: $('#longitud_ramo_' + pos_comb).val(),
                id_empaque_e: $('#id_empaque_e_' + pos_comb).val(),
                id_empaque_p: $('#id_empaque_p_' + pos_comb).val(),
                id_unidad_medida: $('#id_unidad_medida_' + pos_comb).val(),
                texto: $('#texto_' + pos_comb).val(),
                arreglo: arreglo
            };
            get_jquery('{{url('clasificacion_blanco/maduracion')}}', datos, function (retorno) {
                modal_view('modal_view', retorno, '<i class="fa fa-fw fa-gift"></i> Días de maduración', true, false, '{{isPC() ? '35%' : ''}}');
            });
        }

        function update_stock_empaquetado() {
            if ($('#form-update_stock_empaquetado').valid()) {
                datos = {
                    _token: '{{csrf_token()}}',
                    id_stock_empaquetado: $('#id_stock_empaquetado').val(),
                    cantidad: $('#ramos_restantes_aperturas').val()
                };
                post_jquery('{{url('clasificacion_blanco/update_stock_empaquetado')}}', datos, function () {
                    listar_clasificacion_blanco($('#id_variedad').val());
                });
            }
        }
    </script>
@else
    <div class="well text-center">
        No se han encontrado datos que mostrar
    </div>
@endif