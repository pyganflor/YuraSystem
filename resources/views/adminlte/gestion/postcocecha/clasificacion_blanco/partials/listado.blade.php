@if(count($fechas) > 0 && count($combinaciones) > 0 && $stock_apertura != '')
    <form id="form-update_stock_empaquetado" class="pull-left">
        <input type="hidden" id="id_variedad" value="{{$variedad->id_variedad}}">
        <span class="badge">{{$stock_apertura->cantidad_ingresada}}</span> ramos de <span
                class="badge">{{$variedad->nombre}}</span> sacados de apertura -
        @if($stock_apertura->empaquetado == 0)
            <input type="number" title="Ramos armados" id="ramos_armados" style="width: 75px" placeholder="armados" min="0" required
                   class="text-center"
                   value="{{$stock_apertura->cantidad_armada}}">
            <input type="time" title="Hora inicial" id="hora_inicio" placeholder="07:00" required class="text-center"
                   value="{{isset($blanco) ? $blanco->hora_inicio : '07:00'}}">
            <input type="number" title="Personal" id="personal" style="width: 75px" placeholder="personal" required class="text-center"
                   value="{{isset($blanco) ?  $blanco->personal : ''}}" min="1">

            <input type="hidden" id="id_blanco" value="{{isset($blanco) ? $blanco->id_clasificacion_blanco : ''}}">

            <div class="btn-group">
                @if(isset($blanco))
                    <button class="btn btn-sm btn-default" type="button" title="Rendimiento"
                            onclick="ver_rendimiento({{$blanco->id_clasificacion_blanco}})">
                        <strong>{{$blanco->getRendimiento()}}</strong> ramos/hr
                    </button>
                @endif
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Opciones <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0)" onclick="update_stock_empaquetado(0)">Guardar</a></li>
                    <li><a href="javascript:void(0)" onclick="update_stock_empaquetado(1)">Guardar y terminar</a></li>
                </ul>
            </div>
        @else
            <span class="badge">{{$stock_apertura->cantidad_armada}}</span> ramos armados
        @endif
        <input type="hidden" id="id_stock_empaquetado" value="{{$stock_apertura->id_stock_empaquetado}}">
    </form>

    <label for="check_dias_maduracion" class="pull-right" style="margin-left: 5px">Seleccionar flores con mayor días de maduración</label>
    <button class="btn btn-xs btn-default pull-left" onclick="$('.celdas_pc').toggleClass('hide'); $('.celdas_mobile').toggleClass('hide')"
            style="margin-right: 5px" id="btn_ver_detalles">
        <i class="fa fa-fw fa-eye"></i> Ver detalles
    </button>
    <input type="checkbox" class="pull-right" id="check_dias_maduracion" checked>
    <table class="table-bordered table-striped table-responsive" width="100%" id="table_clasificacion_blanco"
           style="border: 1px solid #9d9d9d; font-size: 0.8em; margin-top: 10px; margin-bottom: 0">
        <tr>
            <th style="border-color: #9d9d9d" width="5%" class="text-center">Calibre</th>
            <th style="border-color: #9d9d9d" width="10%" class="text-center">Presentación</th>
            <th style="border-color: #9d9d9d" width="5%" class="text-center">Tallos</th>
            <th style="border-color: #9d9d9d" width="5%" class="text-center">Longitud</th>
            @php
                $pos_fecha = 1;
            @endphp
            @foreach($fechas as $fecha)
                <th class="text-center celdas_pc"
                    style="background-color: #e9ecef; border-color: #9d9d9d; border-width: {{$pos_fecha == 1 ? '3px' : ''}};">
                    {{getDias(TP_ABREVIADO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime($fecha->fecha_pedido)))]}}<br>
                    <small>{{$fecha->fecha_pedido}}</small>
                    <input type="hidden" id="fecha_{{$pos_fecha}}" value="{{$fecha->fecha_pedido}}">
                </th>
                @php
                    $pos_fecha++;
                @endphp
            @endforeach
            <th class="text-center celdas_mobile" style="background-color: #357CA5; border-color: #9d9d9d; color: white">
                Cuarto Frío
            </th>
            <th class="text-center celdas_mobile" style="background-color: #357CA5; border-color: #9d9d9d; color: white">
                Armado
            </th>
            <th class="text-center celdas_mobile" style="background-color: #357CA5; border-color: #9d9d9d; color: white">
                Mesa
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
                    getEmpaque($item->id_empaque_p)->nombre.' '.$tallos_x_ramo.''.$longitud_ramo;
            @endphp
            <tr>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" id="th_pedidos_{{$pos_comb}}">
                    {{getCalibreRamoById($item->id_clasificacion_ramo)->nombre.' '.getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                    <input type="hidden" id="texto_{{$pos_comb}}" value="{{$texto}}">
                    <input type="hidden" id="clasificacion_ramo_{{$pos_comb}}" value="{{$item->id_clasificacion_ramo}}">
                    <input type="hidden" id="tallos_x_ramo_{{$pos_comb}}" value="{{$item->tallos_x_ramos}}">
                    <input type="hidden" id="longitud_ramo_{{$pos_comb}}" value="{{$item->longitud_ramo}}">
                    {{--<input type="hidden" id="id_empaque_e_{{$pos_comb}}" value="{{$item->id_empaque_e}}">--}}
                    <input type="hidden" id="id_empaque_p_{{$pos_comb}}" value="{{$item->id_empaque_p}}">
                    <input type="hidden" id="id_unidad_medida_{{$pos_comb}}" value="{{$item->id_unidad_medida}}">
                </th>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{getEmpaque($item->id_empaque_p)->nombre}}
                </th>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$item->tallos_x_ramos}}
                </th>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$longitud_ramo}}
                </th>
                @php
                    $total_inventario = getDisponibleInventarioFrio($item->id_variedad,$item->id_clasificacion_ramo,/*$item->id_empaque_e,*/$item->id_empaque_p,
                            $item->tallos_x_ramos,$item->longitud_ramo,$item->id_unidad_medida);

                    $pos_fecha = 1;
                    $acumulado_pedido = 0;
                @endphp
                @foreach($fechas as $fecha)
                    @php
                        $cant_pedido = getCantidadRamosPedidosForCB($fecha->fecha_pedido,$item->id_variedad,$item->id_clasificacion_ramo,/*$item->id_empaque_e,*/$item->id_empaque_p,
                            $item->tallos_x_ramos,$item->longitud_ramo,$item->id_unidad_medida);
                        $acumulado_pedido += $cant_pedido;
                        $saldo = $total_inventario - $acumulado_pedido;
                    @endphp
                    <td class="text-center celdas_pc"
                        style="border-color: #9d9d9d; border-right-width: {{$pos_fecha == 1 ? '3px' : ''}}; border-left-width: {{$pos_fecha == 1 ? '3px' : ''}};"
                        onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')">
                        <span class="badge" title="Pedidos">{{number_format($cant_pedido,0)}}</span>
                        <input type="hidden" id="pedido_{{$pos_comb}}_{{$pos_fecha}}" value="{{$cant_pedido}}">

                        @if($saldo >= 0)
                            <span class="badge bg-green" title="Armados">{{$saldo}}</span>
                        @else
                            <span class="badge bg-red" title="Por armar">{{number_format(substr($saldo,1),0)}}</span>
                        @endif
                    </td>
                    @php
                        $pos_fecha++;
                    @endphp
                @endforeach
                <td class="text-center celdas_mobile" style=" border-color: #9d9d9d;" width="7%">
                    <input type="hidden" id="inventario_frio_{{$pos_comb}}" value="{{$total_inventario}}">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" id="btn_inventario_{{$pos_comb}}" onclick="maduracion('{{$pos_comb}}')">
                        {{$total_inventario}}
                    </button>
                </td>
                <td class="text-center celdas_mobile" style=" border-color: #9d9d9d;" width="7%">
                    <input type="number" style="width: 100%" id="armar_{{$pos_comb}}" min="0"
                           onchange="calcular_inventario_i('{{$pos_comb}}', '{{$pos_comb-1}}')"
                           class="text-center" value="0">
                </td>
                <td class="text-center celdas_mobile" style=" border-color: #9d9d9d;" width="7%">
                    <input type="number" style="width: 100%" id="mesa_{{$pos_comb}}" min="0" class="text-center" onkeypress="isNumer(event)">
                </td>
            </tr>
            @php
                $pos_comb++;
            @endphp
        @endforeach
        <tr>
            <td style="border-color: #9d9d9d" colspan="4"></td>
            @php
                $pos_fecha = 1;
            @endphp
            @foreach($fechas as $fecha)
                <td style="border-color: #9d9d9d; border-bottom-width: {{$pos_fecha == 1 ? '3px' : ''}}; background-color: #e9ecef;
                        border-right-width: {{$pos_fecha == 1 ? '3px' : ''}}; border-left-width: {{$pos_fecha == 1 ? '3px' : ''}};"
                    class="text-center celdas_pc">
                    <button type="button" class="btn btn-xs btn-primary" title="Mandar a armar"
                            onclick="mostrar_despacho('{{$fecha->fecha_pedido}}')">
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
            <td class="text-center celdas_mobile" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="3">
                @if($stock_apertura->empaquetado == 0)
                    <button type="button" class="btn btn-xs btn-success" title="Guardar armados"
                            onclick="store_armar('{{$pos_comb-1}}')">
                        <i class="fa fa-fw fa-save"></i> Guardar
                    </button>
                @endif
            </td>
        </tr>
    </table>

    <input type="hidden" id="pos_comb_total" value="{{$pos_comb-1}}">

    <div style="border-top: 1px dotted #9d9d9d; padding: 5px; margin-top: 2px" class="well" title="Opciones">
        <input type="checkbox" id="estrechar_tabla" onchange="estrechar_tabla('table_clasificacion_blanco', $(this).prop('checked'))" checked>
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
                    /*id_empaque_e: $('#id_empaque_e_' + i).val(),*/
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
                id_stock_empaquetado: $('#id_stock_empaquetado').val(),
                ramos_armados: $('#ramos_armados').val(),
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
                    mesa: $('#mesa_' + i).val(),
                    tallos_x_ramo: $('#tallos_x_ramo_' + i).val(),
                    longitud_ramo: $('#longitud_ramo_' + i).val(),
                    /*id_empaque_e: $('#id_empaque_e_' + i).val(),*/
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
                    blanco: $('#id_blanco').val(),
                    id_variedad: $('#id_variedad').val(),
                    id_stock_empaquetado: $('#id_stock_empaquetado').val(),
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
                        /*id_empaque_e: $('#id_empaque_e_' + i).val(),*/
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
                /*id_empaque_e: $('#id_empaque_e_' + pos_comb).val(),*/
                id_empaque_p: $('#id_empaque_p_' + pos_comb).val(),
                id_unidad_medida: $('#id_unidad_medida_' + pos_comb).val(),
                texto: $('#texto_' + pos_comb).val(),
                arreglo: arreglo
            };
            get_jquery('{{url('clasificacion_blanco/maduracion')}}', datos, function (retorno) {
                modal_view('modal_view', retorno, '<i class="fa fa-fw fa-gift"></i> Días de maduración', true, false, '{{isPC() ? '65%' : ''}}');
            });
        }

        function update_stock_empaquetado(terminar) {
            if ($('#form-update_stock_empaquetado').valid()) {
                datos = {
                    _token: '{{csrf_token()}}',
                    id_stock_empaquetado: $('#id_stock_empaquetado').val(),
                    cantidad: $('#ramos_armados').val(),
                    hora_inicio: $('#hora_inicio').val(),
                    personal: $('#personal').val(),
                    blanco: $('#id_blanco').val(),
                    terminar: terminar
                };
                if (terminar == 1) {
                    modal_quest('modal-quest_update_stock_empaquetado',
                        '<div class="alert alert-info text-center">¿Desea terminar la clasificación en blanco para esta variedad?</div>',
                        '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '{{isPC() ? '35%' : ''}}', function () {
                            post_jquery('{{url('clasificacion_blanco/update_stock_empaquetado')}}', datos, function () {
                                cerrar_modals();
                                listar_clasificacion_blanco($('#id_variedad').val());
                            });
                        })
                } else {
                    post_jquery('{{url('clasificacion_blanco/update_stock_empaquetado')}}', datos, function () {
                        listar_clasificacion_blanco($('#id_variedad').val());
                    });
                }
            }
        }

        function mostrar_despacho(fecha) {
            datos = {
                fecha: fecha
            };
            get_jquery('{{url('despachos/listar_resumen_pedidos')}}', datos, function (retorno) {
                modal_view('modal_view_listar_resumen_pedidos', retorno, '<i class="fa fa-fw fa-list-alt"></i> Despachos', true, false, '{{isPC() ? '95%' : ''}}');
            });
        }

        if ($(document).width() >= 1024) { // mostrar
            $('.celdas_pc').removeClass('hide');
            $('#btn_ver_detalles').addClass('hide');
        } else {    // ocultar arbol
            $('.celdas_pc').addClass('hide');
            $('#btn_ver_detalles').removeClass('hide');
        }
    </script>

@else
    <div class="well text-center">
        No se han encontrado datos que mostrar
    </div>
@endif
