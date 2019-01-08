<div id="table_aperturas">
    @if(count($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_aperturas">
            <tr>
                <th style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2">
                    <ul class="list-unstyled">
                        <li>
                            Semana: {{getSemanaByDate($fecha)->codigo}}
                        </li>
                        <li>
                            Día: {{getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime($fecha)))]}}
                        </li>
                    </ul>
                </th>
                <th style="border-color: #9d9d9d;" colspan="8">
                    @if(count($stock_frio) > 0)
                        <button type="button" class="btn btn-primary btn-xs pull-right" onclick="empaquetar('{{$fecha}}')">
                            <i class="fa fa-fw fa-gift"></i> Armado
                        </button>
                    @else
                        <div class="well text-center" style="margin-bottom: 0">
                            No se han sacado flores de las aperturas para los pedidos de este día
                        </div>
                    @endif
                </th>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    PEDIDO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CLIENTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FLOR
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    EMPAQUE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    PRESENTACIÓN
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    PIEZAS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CAJAS FULL
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    RAMOS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    RAMOS x CAJA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    AGENCIA CARGA
                </th>
            </tr>
            @php
                $piezas_totales = 0;
                $ramos_totales = 0;
                $cajas_totales = 0;
            @endphp
            @foreach($listado as $pedido)
                @foreach(getPedido($pedido->id_pedido)->detalles as $detalle)
                    @foreach($detalle->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                        @foreach($esp_emp->detalles as $det_esp_emp)
                            <tr onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')">
                                <td class="text-center" style="border-color: #9d9d9d">
                                    PED{{$pedido->id_pedido}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{getPedido($pedido->id_pedido)->cliente->detalle()->nombre}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->variedad->siglas}} {{$det_esp_emp->clasificacion_ramo->nombre}}
                                    {{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                    @if($det_esp_emp->tallos_x_ramos != '')
                                        {{$det_esp_emp->tallos_x_ramos}} tallos
                                    @endif
                                    @if($det_esp_emp->id_unidad_medida != '' && $det_esp_emp->longitud_ramo != '')
                                        {{$det_esp_emp->longitud_ramo}}{{$det_esp_emp->unidad_medida->siglas}}
                                    @endif
                                    {{$det_esp_emp->grosor_ramo->nombre}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->empaque_e->nombre}}, {{$det_esp_emp->empaque_p->nombre}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$esp_emp->cantidad}}
                                    @php
                                        $piezas_totales += $esp_emp->cantidad;
                                    @endphp
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$esp_emp->cantidad * explode('|',$esp_emp->empaque->nombre)[1]}}
                                    @php
                                        $cajas_totales += $esp_emp->cantidad * explode('|',$esp_emp->empaque->nombre)[1];
                                    @endphp
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->cantidad * $esp_emp->cantidad * $detalle->cantidad}}
                                    @php
                                        $ramos_totales += $det_esp_emp->cantidad * $esp_emp->cantidad * $detalle->cantidad;
                                    @endphp
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->cantidad}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$detalle->agencia_carga->nombre}}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </table>
        <div class="row">
            <div class="col-md-4">
                <table class="table table-responsive table-bordered" style="font-size: 0.7em; border: 1px solid #9d9d9d">
                    <tr>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d" colspan="{{count($variedades)+1}}">
                            Cajas Equivalentes
                        </th>
                    </tr>
                    <tr>
                        <td style="border-color: #9d9d9d"></td>
                        @foreach($variedades as $variedad)
                            <th class="text-center" style="border-color: #9d9d9d">
                                {{getVariedad($variedad->id_variedad)->nombre}}
                            </th>
                        @endforeach
                    </tr>
                    @foreach($grosores as $grosor)
                        <tr>
                            <th class="text-center" style="border-color: #9d9d9d">
                                {{getGrosor($grosor->id_grosor_ramo)->nombre}}
                            </th>
                            @foreach($variedades as $variedad)
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{getEquivalentesByGrosorVariedad($fecha, $grosor->id_grosor_ramo, $variedad->id_variedad)}}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="col-md-4">
                <table class="table table-responsive table-bordered" style="font-size: 0.7em; border: 1px solid #9d9d9d">
                    <tr>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d" colspan="2">
                            Totales ramos por Variedad
                        </th>
                    </tr>
                    @foreach($ramos_x_variedad as $item)
                        <tr>
                            <th class="text-center" style="border-color: #9d9d9d">
                                {{getVariedad($item->id_variedad)->siglas}}
                                {{getCalibreRamoById($item->id_clasificacion_ramo)->nombre}}{{getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                                @if($item->tallos_x_ramos != '')
                                    {{$item->tallos_x_ramos}} tallos
                                @endif
                                @if($item->longitud_ramo != '' && $item->id_unidad_medida != '')
                                    {{$item->longitud_ramo}} {{getUnidadMedida($item->id_unidad_medida)->siglas}}
                                @endif
                            </th>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$item->cantidad}}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="col-md-4">
                <table class="table table-responsive table-bordered" style="font-size: 0.7em; border: 1px solid #9d9d9d">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">Piezas Totales Pedidas</th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$piezas_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">Ramos Totales Pedidos</th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$ramos_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">Cajas Full Totales Pedidas</th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$cajas_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">Cajas Equivalentes Totales Pedidas</th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$ramos_totales/getConfiguracionEmpresa()->ramos_x_caja}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado pedidos para esta fecha</div>
    @endif
</div>

<script>
    function seleccionar_apertura_sacar(apertura) {
        if ($('#sacar_' + apertura).val() != '') {
            texto = '';
            if ($('#maximo_estandar_' + apertura).prop('checked'))
                texto = 'por encima del máximo';
            if ($('#minimo_estandar_' + apertura).prop('checked'))
                texto = 'por debajo del mínimo';
            if ($('#estandar_estandar_' + apertura).prop('checked'))
                texto = 'por encima del estandar';

            if (texto != '') {
                modal_quest('modal_quest_input_sacar',
                    '<div class="alert alert-info text-center">Está seleccionando flores con días de maduración ' + texto + ' permitido</div>',
                    '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '{{isPC() ? '35%' : ''}}', function () {
                        $('#checkbox_sacar_' + apertura).prop('checked', true);
                        $('#btn_sacar').show();

                        $('#html_current_sacar').html('');

                        listado = $('.checkbox_sacar');
                        $('#btn_sacar').hide();
                        cantidad_seleccionada = 0;
                        for (i = 0; i < listado.length; i++) {
                            if (listado[i].checked) {
                                $('#btn_sacar').show();
                                cantidad_seleccionada += parseFloat($('#sacar_' + listado[i].id.substr(15)).val());
                                $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                            }
                        }
                        cerrar_modals();
                    });
            } else {
                $('#checkbox_sacar_' + apertura).prop('checked', true);
                $('#btn_sacar').show();
            }
        } else {
            $('#checkbox_sacar_' + apertura).prop('checked', false);
            $('#btn_sacar').hide();
        }
        seleccionar_checkboxs($('#checkbox_sacar_' + apertura));
    }

    function seleccionar_checkboxs(current) {
        if (current != '' && current.prop('checked')) {
            current.prop('checked', false);
            apertura = current.prop('id').substr(15);
            $('#btn_sacar').hide();

            texto = '';
            if ($('#maximo_estandar_' + apertura).prop('checked'))
                texto = 'por encima del máximo';
            if ($('#minimo_estandar_' + apertura).prop('checked'))
                texto = 'por debajo del mínimo';
            if ($('#estandar_estandar_' + apertura).prop('checked'))
                texto = 'por encima del estandar';

            if (texto != '') {
                modal_quest('modal_quest_input_sacar',
                    '<div class="alert alert-info text-center">Está seleccionando flores con días de maduración ' + texto + ' permitido</div>',
                    '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '{{isPC() ? '35%' : ''}}', function () {
                        current.prop('checked', true);
                        $('#btn_sacar').show();

                        $('#html_current_sacar').html('');

                        listado = $('.checkbox_sacar');
                        $('#btn_sacar').hide();
                        cantidad_seleccionada = 0;
                        for (i = 0; i < listado.length; i++) {
                            if (listado[i].checked) {
                                $('#btn_sacar').show();
                                cantidad_seleccionada += parseFloat($('#sacar_' + listado[i].id.substr(15)).val());
                                $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                            }
                        }
                        cerrar_modals();
                    });
            } else {
                current.prop('checked', true);
                $('#btn_sacar').show();

                $('#html_current_sacar').html('');

                listado = $('.checkbox_sacar');
                $('#btn_sacar').hide();
                cantidad_seleccionada = 0;
                for (i = 0; i < listado.length; i++) {
                    if (listado[i].checked) {
                        $('#btn_sacar').show();
                        cantidad_seleccionada += parseFloat($('#sacar_' + listado[i].id.substr(15)).val());
                        $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                    }
                }
            }
        } else {
            $('#html_current_sacar').html('');

            listado = $('.checkbox_sacar');
            $('#btn_sacar').hide();
            cantidad_seleccionada = 0;
            for (i = 0; i < listado.length; i++) {
                if (listado[i].checked) {
                    $('#btn_sacar').show();
                    cantidad_seleccionada += parseFloat($('#sacar_' + listado[i].id.substr(15)).val());
                    $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                }
            }
        }


    }
</script>