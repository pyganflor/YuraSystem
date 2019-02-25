<div id="table_aperturas">
    @if(sizeof($listado)>0)
        <div style="overflow-x: scroll">
            <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
                   id="table_content_aperturas">
                <thead>
                <tr>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        Semana
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        Calibre
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d" width="7%">
                        Días Maduración
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d" title="Entrantes">
                        Tallos
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d" title="Entrantes">
                        Saldo
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d" title="Convertidos">
                        Ramos
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        Estandar
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        Real
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d" width="5%">
                        Sacar
                    </th>
                </tr>
                </thead>
                @php
                    $current_fecha = substr($listado[0]->fecha_inicio,0,10);

                    $tallos_entrantes = 0;
                    $tallos_restantes = 0;
                    $total_disponibles = 0;
                    $total_ramos = 0;
                    $ids_apertura = '';
                    $i=0;
                @endphp
                @foreach($listado as $apertura)
                    <tr onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')"
                        style="color: {{getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->unidad_medida->tipo == 'L' ? 'blue' : ''}}">
                        <td class="text-center" style="border-color: #9d9d9d;">
                            <input type="checkbox" id="checkbox_sacar_{{$apertura->id_stock_apertura}}" class="checkbox_sacar"
                                   onchange="seleccionar_checkboxs($(this))">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;">
                            <input type="hidden" class="ids_apertura" id="id_apertura_{{$apertura->id_stock_apertura}}"
                                   value="{{$apertura->id_stock_apertura}}">
                            <input type="hidden" id="cantidad_disponible_{{$apertura->id_stock_apertura}}"
                                   value="{{getStockById($apertura->id_stock_apertura)->cantidad_disponible}}">
                            <input type="hidden" id="ramos_convertidos_{{$apertura->id_stock_apertura}}"
                                   name="ramos_convertidos_{{$apertura->id_stock_apertura}}" class="ramos_convertidos"
                                   value="{{getStockById($apertura->id_stock_apertura)->calcularDisponibles()['estandar']}}">
                            {{getSemanaByDateVariedad($apertura->fecha_inicio, getStockById($apertura->id_stock_apertura)->id_variedad)->codigo}}
                        </td>
                        <td class="text-center"
                            style="border-color: #9d9d9d;
                                    background-color: {{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->color)[0]}};
                                    color: {{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->color)[1]}};">
                            {{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[0]}}
                            {{getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->unidad_medida->siglas}}

                            <input type="hidden" id="calibre_unitario_{{$apertura->id_stock_apertura}}"
                                   value="{{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[0]}}">
                            <input type="hidden" id="factor_calibre_unitario_{{$apertura->id_stock_apertura}}"
                                   value="{{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[1]}}">
                            <input type="hidden" id="tipo_calibre_unitatio_{{$apertura->id_stock_apertura}}"
                                   value="{{getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->unidad_medida->tipo}}">
                        </td>
                        @php
                            $color = '';
                            $maximo_estandar = true;
                            $minimo_estandar = true;
                            $estandar_estandar = true;
                            if(difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days > getStockById($apertura->id_stock_apertura)->variedad->maximo_apertura){
                                $color = '#ce8483';
                                $maximo_estandar = false;
                            }
                            if(difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days < getStockById($apertura->id_stock_apertura)->variedad->minimo_apertura){
                                $color = '#ce8483';
                                $minimo_estandar = false;
                            }
                            if(difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days > getStockById($apertura->id_stock_apertura)->variedad->estandar_apertura &&
                                difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days <= getStockById($apertura->id_stock_apertura)->variedad->maximo_apertura){
                                $color = '#ffef92';
                                $estandar_estandar = false;
                            }
                        @endphp
                        <td class="text-center"
                            style="border-color: #9d9d9d; background-color: {{$color}}">
                            {{difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days}}
                            <input type="hidden" id="dias_maduracion_{{$apertura->id_stock_apertura}}"
                                   value="{{difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days}}">
                            <input type="checkbox" class="hidden" id="maximo_estandar_{{$apertura->id_stock_apertura}}"
                                    {{!$maximo_estandar ? 'checked' : ''}}>
                            <input type="checkbox" class="hidden" id="minimo_estandar_{{$apertura->id_stock_apertura}}"
                                    {{!$minimo_estandar ? 'checked' : ''}}>
                            <input type="checkbox" class="hidden" id="estandar_estandar_{{$apertura->id_stock_apertura}}"
                                    {{!$estandar_estandar ? 'checked' : ''}}>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" title="Entrantes"
                            id="celda_tallos_entrantes_{{$apertura->id_stock_apertura}}">
                            {{getStockById($apertura->id_stock_apertura)->cantidad_tallos}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" title="Restantes"
                            id="celda_tallos_restantes_{{$apertura->id_stock_apertura}}">
                            {{getStockById($apertura->id_stock_apertura)->cantidad_disponible}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" title="Convertidos"
                            id="celda_ramos_convertidos_{{$apertura->id_stock_apertura}}">
                            {{getStockById($apertura->id_stock_apertura)->calcularDisponibles()['estandar']}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                                <span class="badge" title="Estandar">
                                    {{getStockById($apertura->id_stock_apertura)->getDisponibles('estandar')}}
                                </span>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                                <span class="badge" style="background-color: #357ca5" title="Real">
                                    {{getStockById($apertura->id_stock_apertura)->getDisponibles('real')}}
                                </span>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            <input type="number" class="text-center input_sacar" {{--onkeypress="return isNumber(event)"--}}
                            id="sacar_{{$apertura->id_stock_apertura}}" min="1" width="100%"
                                   max="{{getStockById($apertura->id_stock_apertura)->cantidad_disponible}}"
                                   onchange="seleccionar_apertura_sacar('{{$apertura->id_stock_apertura}}')"
                                   value="{{getStockById($apertura->id_stock_apertura)->cantidad_disponible}}">
                            <input type="hidden" class="input_sacar_ini"
                                   id="sacar_ini_{{$apertura->id_stock_apertura}}"
                                   value="{{getStockById($apertura->id_stock_apertura)->cantidad_disponible}}">
                        </td>
                    </tr>
                    @php
                        $tallos_entrantes += getStockById($apertura->id_stock_apertura)->cantidad_tallos;
                        $tallos_restantes += getStockById($apertura->id_stock_apertura)->cantidad_disponible;
                        $total_disponibles += getStockById($apertura->id_stock_apertura)->getDisponibles('estandar');
                        $total_ramos += getStockById($apertura->id_stock_apertura)->calcularDisponibles()['estandar'];
                        $ids_apertura .= $apertura->id_stock_apertura.'|';
                    @endphp
                    @if(count($listado) > ($i+1) && substr($listado[$i+1]->fecha_inicio,0,10) != substr($apertura->fecha_inicio,0,10) ||
                        count($listado) == ($i+1))
                        <tr style="background-color: #e9ecef;">
                            <td style="border-bottom-color: #9d9d9d; border-left-color: #9d9d9d">
                                <input type="hidden" id="fecha_{{substr($apertura->fecha_inicio,0,10)}}" class="fechas_aperturas"
                                       value="{{substr($apertura->fecha_inicio,0,10)}}">
                                <input type="hidden" id="fecha_ids_aperturas_{{substr($apertura->fecha_inicio,0,10)}}"
                                       value="{{$ids_apertura}}">
                                <input type="hidden" id="total_ramos_{{substr($apertura->fecha_inicio,0,10)}}"
                                       value="{{$total_ramos}}">
                            </td>
                            <td style="border-bottom-color: #9d9d9d; border-color: #9d9d9d" colspan="2">
                                {{substr($apertura->fecha_inicio,0,10)}}
                            </td>
                            <th class="text-center" style="border-color: #9d9d9d">Total</th>
                            <th class="text-center" style=" border-color: #9d9d9d">
                                {{$tallos_entrantes}}
                            </th>
                            <th class="text-center" style=" border-color: #9d9d9d">
                                {{$tallos_restantes}}
                            </th>
                            <th class="text-center" style=" border-color: #9d9d9d"
                                id="celda_total_ramos_{{substr($apertura->fecha_inicio,0,10)}}">
                                {{$total_ramos}}
                            </th>
                            <th class="text-center" style=" border-color: #9d9d9d">{{$total_disponibles}}</th>
                            <td style="border-bottom-color: #9d9d9d; border-right-color: #9d9d9d"></td>
                            <td style="border-bottom-color: #9d9d9d; border-right-color: #9d9d9d"></td>
                        </tr>
                        @php
                            $total_ramos = 0;
                            $total_disponibles = 0;
                            $ids_apertura = '';
                        @endphp
                    @endif
                    @php
                        $i++;
                    @endphp
                @endforeach
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>

<script>
    function seleccionar_apertura_sacar(apertura) {
        if ($('#sacar_' + apertura).val() != '' && $('#sacar_' + apertura).val() > 0) {
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
                        tallos_x_coche = $('#tallos_x_coche').val();
                        if (tallos_x_coche == '' || tallos_x_coche <= 0)
                            tallos_x_coche = 1;

                        $('#checkbox_sacar_' + apertura).prop('checked', true);
                        $('#btn_sacar').show();

                        $('#html_current_sacar').html('');

                        listado = $('.checkbox_sacar');
                        $('#btn_sacar').hide();
                        cantidad_seleccionada = 0;
                        for (i = 0; i < listado.length; i++) {
                            if (listado[i].checked) {
                                $('#btn_sacar').show();
                                factor = $('#factor_calibre_unitario_' + listado[i].id.substr(15)).val();
                                seleccionados = parseFloat($('#sacar_' + listado[i].id.substr(15)).val()) * tallos_x_coche;
                                cantidad_seleccionada += (Math.round((seleccionados / factor) * 100) / 100);
                                $('#html_current_sacar').html('Seleccionados: ' + (Math.round(cantidad_seleccionada * 100) / 100));
                            }
                        }

                        cerrar_modals();
                    });
            } else {
                $('#checkbox_sacar_' + apertura).prop('checked', true);
                $('#btn_sacar').show();
            }
        } else {
            $('#sacar_' + apertura).val(0);
            $('#checkbox_sacar_' + apertura).prop('checked', false);
            $('#btn_sacar').hide();
        }
        seleccionar_checkboxs($('#checkbox_sacar_' + apertura));
    }

    function seleccionar_checkboxs(current) {
        tallos_x_coche = $('#tallos_x_coche').val();
        if (tallos_x_coche == '' || tallos_x_coche <= 0)
            tallos_x_coche = 1;

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
                if ($('#sacar_' + current.prop('id').substr(15)).val() > 0) {
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
                                    factor = $('#factor_calibre_unitario_' + listado[i].id.substr(15)).val();
                                    seleccionados = parseFloat($('#sacar_' + listado[i].id.substr(15)).val()) * tallos_x_coche;
                                    cantidad_seleccionada += (Math.round((seleccionados / factor) * 100) / 100);
                                    $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                                }
                            }

                            cerrar_modals();
                        });
                } else {
                    current.prop('checked', false);
                }
            } else {
                if ($('#sacar_' + current.prop('id').substr(15)).val() > 0) {

                    current.prop('checked', true);
                    $('#btn_sacar').show();

                    $('#html_current_sacar').html('');

                    listado = $('.checkbox_sacar');
                    $('#btn_sacar').hide();
                    cantidad_seleccionada = 0;
                    for (i = 0; i < listado.length; i++) {
                        if (listado[i].checked) {
                            $('#btn_sacar').show();
                            factor = $('#factor_calibre_unitario_' + listado[i].id.substr(15)).val();
                            seleccionados = parseFloat($('#sacar_' + listado[i].id.substr(15)).val()) * tallos_x_coche;
                            cantidad_seleccionada += (Math.round((seleccionados / factor) * 100) / 100);
                            $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                        }
                    }
                } else {
                    current.prop('checked', false);
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
                    factor = $('#factor_calibre_unitario_' + listado[i].id.substr(15)).val();
                    seleccionados = parseFloat($('#sacar_' + listado[i].id.substr(15)).val()) * tallos_x_coche;
                    cantidad_seleccionada += (Math.round((seleccionados / factor) * 100) / 100);
                    $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                }
            }
        }
    }

    function sacar_aperturas() {
        listado = $('.checkbox_sacar');
        arreglo = [];

        tallos_x_coche = $('#tallos_x_coche').val();
        if (tallos_x_coche == '' || tallos_x_coche <= 0)
            tallos_x_coche = 1;

        for (i = 0; i < listado.length; i++) {
            if (listado[i].checked) {
                id = listado[i].id.substr(15);
                pos = $('#pos_pedido').val();

                factor = $('#factor_calibre_unitario_' + id).val();
                seleccionados = parseFloat($('#sacar_' + id).val()) * tallos_x_coche;
                cantidad_seleccionada = (Math.round((seleccionados / factor) * 100) / 100);

                data = {
                    id_stock_apertura: id,
                    dias_maduracion: $('#dias_maduracion_' + id).val(),
                    cantidad_ramos_estandar: cantidad_seleccionada,
                };
                arreglo.push(data);
            }
        }

        datos = {
            _token: '{{csrf_token()}}',
            arreglo: arreglo
        };
        post_jquery('{{url('apertura/sacar')}}', datos, function () {
            buscar_listado();
            $('#tallos_x_coche').val('');
            $('#html_current_sacar').html('');
        });
    }

    set_min_today($('#fecha_pedidos'));
    buscar_pedidos();
</script>