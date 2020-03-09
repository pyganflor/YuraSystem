@if(count($recepciones)>0)
    <div class="row">
        <div class="col-md-5">
            <div class="list-group">
                @php
                    $total_tallos = 0;
                @endphp
                @foreach($variedades as $v)
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action li_variedad"
                       onclick="seleccionar_variedad('{{$v['variedad']->id_variedad}}', $(this))"
                       id="li_variedad_{{$v['variedad']->id_variedad}}">
                        {{$v['variedad']->planta->nombre.' - '.$v['variedad']->siglas}}
                        <span class="pull-right badge" title="Tallos de clasificación" style="background-color: #0b58a2; color: white"
                              id="badge_tallos_clasificados_x_variedad_{{$v['variedad']->id_variedad}}">
                            {{$clasificacion_verde != '' ? $clasificacion_verde->tallos_x_variedad($v['variedad']->id_variedad) : 0}}
                        </span>
                        <input type="hidden" id="tallos_clasificados_{{$v['variedad']->id_variedad}}" class="ids_variedad"
                               value="{{$clasificacion_verde != '' ? $clasificacion_verde->tallos_x_variedad($v['variedad']->id_variedad) : 0}}">
                        <span class="pull-right badge" title="Tallos de recepción" style="margin-right: 5px">
                            {{$v['tallos']}}
                        </span>
                    </a>
                    <input type="hidden" id="recepcion_{{$v['variedad']->id_variedad}}" value="{{$v['tallos']}}">
                    @php
                        $total_tallos += $v['tallos'];
                    @endphp
                @endforeach
                @if(count($variedades)==1)
                    <script>
                        seleccionar_variedad('{{$variedades[0]['variedad']->id_variedad}}', $('#'));
                    </script>
                @endif
            </div>
        </div>
        <div class="col-md-7" id="div_table_x_variedad">
            <div class="well text-center">
                Seleccione una variedad
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" placeholder="Listo para escanear" id="input_escanear" style="width: 100%"
                               class="text-center form-control"
                               onchange="scan()" autocomplete="off">
                    </div>
                    <div class="col-md-8 text-center">
                        <p style="display: none; margin-top: 10px" id="html_info_scan" class="text-center" onclick="$(this).html('')"></p>
                    </div>
                </div>
                @if($clasificacion_verde != '')
                    <br>
                    <small class="green">Se ha seleccionado automáticamente una clasificación existente para este día</small>
                @endif
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 10px">
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef;">Total de tallos</span>
                <span class="input-group-addon">
                    @if($clasificacion_verde != '')
                        <span class="pull-right badge" title="Tallos de clasificación" style="background-color: #0b58a2; color: white"
                              id="html_total_tallos">
                            {{$clasificacion_verde->total_tallos()}}
                        </span>
                    @endif
                    <span class="badge" title="Tallos de recepción" style="margin-right: 10px">
                        {{$total_tallos}}
                    </span>
                    <input type="hidden" id="total_tallos_recepcion" value="{{$total_tallos}}">
                </span>
            </div>
            <input type="hidden" id="total_tallos" value="{{$clasificacion_verde != '' ? $clasificacion_verde->total_tallos() : 0}}">
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Total de ramos</span>
                <span class="input-group-addon">
                    @if($clasificacion_verde != '')
                        <span class="badge" title="Tallos de clasificación" style="background-color: #0b58a2; color: white"
                              id="html_total_ramos">
                            {{$clasificacion_verde != '' ? $clasificacion_verde->total_ramos() : 0}}
                        </span>
                    @endif
                </span>
            </div>
            <input type="hidden" id="total_ramos" value="{{$clasificacion_verde != '' ? $clasificacion_verde->total_ramos() : 0}}">
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #ce8483; color: white">Desechos</span>
                <span class="input-group-addon" id="html_desechos">
                    {{$clasificacion_verde != '' ? $clasificacion_verde->desecho() : 100}}%
                </span>
            </div>
            <input type="hidden" id="desechos" value="{{$clasificacion_verde != '' ? $clasificacion_verde->desecho() : 100}}">
        </div>
    </div>

    <div class="row">
        <form id="form_store_personal_hora_inicio">
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Personal</span>
                    @if($clasificacion_verde != '')
                        <input type="number" onkeypress="return isNumber(event)" id="personal" name="personal" class="form-control text-center"
                               min="1" value="{{$clasificacion_verde->personal}}" required>
                    @else
                        <input type="number" onkeypress="return isNumber(event)" id="personal" name="personal" class="form-control text-center"
                               min="1" value="" required>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Hora inicio</span>
                    @if($clasificacion_verde != '')
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control text-center"
                               value="{{$clasificacion_verde->hora_inicio}}" required>
                        @if($clasificacion_verde->activo == 1)
                            <span class="input-group-btn" title="Guardar personal">
                                <button type="button" class="btn btn-success" onclick="store_personal()">
                                    <i class="fa fa-fw fa-save"></i>
                                </button>
                            </span>
                        @endif
                    @else
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control text-center"
                               value="08:00" required>
                        <span class="input-group-btn" title="Guardar personal">
                                <button type="button" class="btn btn-success" onclick="store_personal()">
                                    <i class="fa fa-fw fa-save"></i>
                                </button>
                            </span>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    @if($clasificacion_verde != '')
                        @if($clasificacion_verde->personal != '')
                            <span class="input-group-addon" style="background-color: #e9ecef">
                            Rendimiento
                        </span>
                            <span class="input-group-addon" title="Tallos por persona en una hora">
                            {{$clasificacion_verde->getRendimiento()}}
                        </span>
                            <span class="input-group-btn" title="Ver detalles">
                            <button type="button" class="btn btn-default"
                                    onclick="ver_rendimiento('{{$clasificacion_verde->id_clasificacion_verde}}')">
                                <i class="fa fa-fw fa-eye"></i>
                            </button>
                        </span>
                        @endif
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if($clasificacion_verde != '')
        @if($clasificacion_verde->activo == 1)
            <div class="text-center" id="btn_terminar_clasificacion">
                <button type="button" class="btn btn-danger btn-sm" onclick="terminar_clasificacion()">
                    <i class="fa fa-fw fa-times"></i> Terminar Clasificación
                </button>
            </div>
            @foreach($clasificacion_verde->variedades() as $variedad)
                <div id="div_destinar_lotes_{{$variedad->id_variedad}}" style="display: none;"></div>
                <script>
                    destinar_lotes_form('{{$variedad->id_variedad}}', '{{$clasificacion_verde->id_clasificacion_verde}}');
                </script>
            @endforeach
        @endif
    @endif

    @php
        $val_recepciones = $recepciones[0]->id_recepcion;
        for($i=1;$i<count($recepciones);$i++)
            $val_recepciones .= '|'.$recepciones[$i]->id_recepcion;
    @endphp
    <input type="hidden" id="recepciones" name="recepciones" value="{{$val_recepciones}}">
    <input type="hidden" id="id_clasificacion_verde" name="id_clasificacion_verde"
           value="{{$clasificacion_verde != '' ? $clasificacion_verde->id_clasificacion_verde : ''}}">
@else
    <div class="well text-center">
        No se han encontrado recepciones en la fecha indicada
    </div>
@endif

<script>
    $('#input_escanear').focus();

    function calcular_totales_verde() {
        id_variedad = $('#id_variedad').val();
        total_x_variedad = $('#recepcion_' + id_variedad).val();
        ids_unitaria = $('.ids_unitaria');
        ramos_x_variedad = 0;
        tallos_x_variedad = 0;
        desecho_x_variedad = 100;

        $('#msg_error').hide();
        $('#btn_store_verde').show();

        for (u = 0; u < ids_unitaria.length; u++) {
            cantidad_ramos = parseInt($('#cantidad_ramos_' + ids_unitaria[u].value).val());
            tallos_x_ramo = parseInt($('#tallos_x_ramos_' + ids_unitaria[u].value).val());

            ramos_x_variedad += cantidad_ramos;
            total_x_unitaria = cantidad_ramos * tallos_x_ramo;
            tallos_x_variedad += total_x_unitaria;

            if ((tallos_x_variedad + parseInt($('#tallos_clasificados_' + id_variedad).val())) <= total_x_variedad) {
                desecho_x_variedad = Math.round((tallos_x_variedad * 100) / total_x_variedad);
                desecho_x_variedad = 100 - desecho_x_variedad;

                $('#total_x_unitaria_' + ids_unitaria[u].value).html(total_x_unitaria);
            }
        }

        if ((tallos_x_variedad + parseInt($('#total_tallos').val())) <= $('#total_tallos_recepcion').val() &&
            (tallos_x_variedad + parseInt($('#tallos_clasificados_' + id_variedad).val())) <= total_x_variedad) {
            $('#html_ramos_x_variedad_' + id_variedad).html(ramos_x_variedad);
            $('#html_tallos_x_variedad_' + id_variedad).html(tallos_x_variedad);
            $('#html_desecho_x_variedad_' + id_variedad).html(desecho_x_variedad + '%');

            $('#html_total_tallos').html((tallos_x_variedad + parseInt($('#total_tallos').val())));
            $('#html_total_ramos').html((ramos_x_variedad + parseInt($('#total_ramos').val())));
            $('#html_desechos').html(100 - Math.round(((tallos_x_variedad + parseInt($('#total_tallos').val())) * 100) / parseInt($('#total_tallos_recepcion').val())) + '%');
            $('#badge_tallos_clasificados_x_variedad_' + id_variedad).html((tallos_x_variedad + parseInt($('#tallos_clasificados_' + id_variedad).val())));

            $('#input_tallos_x_variedad_' + id_variedad).val(tallos_x_variedad);
        } else {
            $('#msg_error').show();
            $('#btn_store_verde').hide();
        }
    }

    function store_verde() {
        if ($('#form-add_clasificacion_verde_x_variedad_' + $('#id_variedad').val()).valid()) {
            if (parseInt($('#input_tallos_x_variedad_' + $('#id_variedad').val()).val()) <= parseInt($('#recepcion_' + $('#id_variedad').val()).val())) {
                modal_quest('modal_quest_store_clasificacion_verde', '<div class="alert alert-info text-center">' +
                    '¿Está seguro de guardar la información en el sistema?</div>',
                    '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '{{isPC() ? '35%' : ''}}', function () {
                        ids_unitaria = $('.ids_unitaria');

                        arreglo_detalles = [];
                        cant_ramos = 0;
                        for (u = 0; u < ids_unitaria.length; u++) {
                            detalle = {
                                id_clasificacion_unitaria: $('#id_unitiaria_' + ids_unitaria[u].value).val(),
                                cantidad_ramos: $('#cantidad_ramos_' + ids_unitaria[u].value).val(),
                                tallos_x_ramos: $('#tallos_x_ramos_' + ids_unitaria[u].value).val(),
                            };
                            arreglo_detalles.push(detalle);

                            cant_ramos += parseInt($('#cantidad_ramos_' + ids_unitaria[u].value).val());
                        }

                        if (cant_ramos > 0) {
                            datos = {
                                _token: '{{csrf_token()}}',
                                recepciones: $('#recepciones').val(),
                                detalles: arreglo_detalles,
                                fecha_ingreso: $('#fecha_ingreso').val(),
                                id_variedad: $('#id_variedad').val(),
                                id_clasificacion_verde: $('#id_clasificacion_verde').val()
                            };
                            if ($('#id_clasificacion_verde').val() != '')
                                urls = '{{url('clasificacion_verde/store_detalles')}}';
                            else
                                urls = '{{url('clasificacion_verde/store')}}';

                            post_jquery(urls, datos, function () {
                                cerrar_modals();
                                add_verde($('#fecha_recepciones').val());
                                buscar_listado();
                            });
                        } else {
                            alerta('<p class="text-center">Al menos ingrese un ramo de la clasificación</p>');
                        }
                    });
            } else {
                alerta('<p class="text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ' +
                    'La cantidad clasificada no puede ser mayor a la cantidad en recepción</p>');
            }
        }
    }

    function terminar_clasificacion() {
        datos = {
            _token: '{{csrf_token()}}',
            id_clasificacion_verde: $('#id_clasificacion_verde').val()
        };
        fecha = $('#fecha_recepciones').val();
        modal_quest('modal_quest_terminar_clasificacion',
            '<div class="alert alert-info text-center">Si termina esta clasificación en verde no podrá volver a clasificar más ramos en la fecha seleccionada</div>',
            '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '{{isPc() ? '35%' : ''}}', function () {
                post_jquery('{{url('clasificacion_verde/terminar')}}', datos, function () {
                    if ($('#check_mandar_apertura_auto').prop('checked')) {
                        ids_variedades = $('.id_variedad_form');
                        arreglo_master = [];

                        for (v = 0; v < ids_variedades.length; v++) {
                            arreglo_master.push(store_lote_re_from(ids_variedades[v].value));
                        }
                        datos = {
                            _token: '{{csrf_token()}}',
                            arreglo: arreglo_master
                        };
                        post_jquery('{{url('clasificacion_verde/store_lote_re_from')}}', datos, function () {
                        });
                    }
                    cerrar_modals();
                    buscar_listado();

                    add_verde(fecha);
                });
            });
    }

    function store_personal() {
        if ($('#personal').val() != '' && $('#hora_inicio').val() != '') {
            datos = {
                _token: '{{csrf_token()}}',
                fecha_ingreso: $('#fecha_ingreso').val(),
                recepciones: $('#recepciones').val(),
                personal: $('#personal').val(),
                hora_inicio: $('#hora_inicio').val(),
                id_clasificacion_verde: $('#id_clasificacion_verde').val(),
            };

            post_jquery('{{url('clasificacion_verde/store_personal')}}', datos, function () {
                cerrar_modals();
                add_verde($('#fecha_recepciones').val());
                buscar_listado();
            });
        }
    }

    /* ================= funciones Escaner ===========*/
    function select_nav(option) {
        $('.nav_header').removeClass('active');
        $('.div_nav').hide();
        $('#li_nav_' + option).addClass('active');
        $('#div_table_' + option).show();

        if (option == 'automatico')
            $('#input_escanear').focus();
    }

    function scan() {
        var scan = $('#input_escanear').val().toUpperCase();
        var tipo = scan.substr(0, 1);
        var data = scan.substr(1);
        if (tipo == 'C') {  // ejemplo C2, "C" indica calibre y "2" el id_clasificacion_unitaria
            scan_calibres(data);
        } else if (tipo == 'R') {   // ejemplo R15, "R" indica ramos y "15" la cantidad de ramos
            scan_ramos(data);
        } else if (tipo == 'G') {
            scan_guardar();
        } else if (tipo == 'X') {
            scan_reiniciar();
        } else if (tipo == 'V') {   // ejemplo V1, "V" indica variedad y "1" el id_variedad
            scan_variedad(data);
        } else {
            $('#html_info_scan').html('<span class="error">' +
                'No se reconoce la lectura, asegúrese de escanear los códigos correctos' +
                '</span>');
            beep();
        }
        $('#html_info_scan').show();

        $('#input_escanear').val('');
        $('#input_escanear').focus();
    }

    function scan_calibres(data) {
        id = $('#nombre_unitaria_' + data.toLowerCase()).val();
        if (id != '' && id != undefined) {
            unidad_medida = $('#unidad_medida_unitaria_' + id).val();
            tallos_x_ramos = $('#tallos_x_ramos_' + id).val();
            fondo = $('#fondo_unitaria_' + id).val();
            texto = $('#texto_unitaria_' + id).val();

            $('#html_info_scan').html('Se ha añadido el calibre <span class="badge" style="background-color: ' + fondo + '; color: ' + texto + '">' +
                data.toLowerCase() +
                '</span>');

            ramos = $('#ramos_x_defecto').val();
            if (ramos > 0)
                total_x_unitaria = ramos * tallos_x_ramos;
            else {
                total_x_unitaria = 0;
                ramos = 0;
            }

            cant_filas = parseInt($('#cant_filas').val()) + 1;
            $('#table_automatico').append('<tr style="background-color: ' + fondo + '; color: ' + texto + '" id="row_auto_' + cant_filas + '">' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                data.toLowerCase() +
                '<input type="hidden" class="id_unitaria_auto" id="id_unitaria_auto_' + cant_filas + '" value="' + id + '">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" id="cantidad_ramos_auto_' + cant_filas + '" name="cantidad_ramos_auto_' + cant_filas + '" required min="0"' +
                ' class="text-center cantidad_ramos_auto" style="background-color: ' + fondo + '; color: ' + texto + '" onkeypress="return isNumber(event)" ' +
                'value="' + ramos + '" onchange="calcular_total_x_variedad(1)">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" id="tallos_x_ramo_auto_' + cant_filas + '" name="tallos_x_ramo_auto_' + cant_filas + '"' +
                ' style="background-color: ' + fondo + '; color: ' + texto + '" onkeypress="return isNumber(event)" value="' + tallos_x_ramos + '"' +
                ' min="0" required class="text-center" onchange="calcular_total_x_variedad(1)">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<span class="badge total_x_unitaria_auto" id="total_x_unitaria_auto_' + cant_filas + '">' + total_x_unitaria + '</span>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<button type="button" class="btn btn-xs btn-danger" onclick="remove_fila_auto(' + cant_filas + ')">' +
                '<i class="fa fa-fw fa-times"></i>' +
                '</button>' +
                '</td>' +
                '</tr>');
            $('#cant_filas').val(cant_filas);
            calcular_total_x_variedad();
        } else {
            $('#html_info_scan').html('<span class="error">' +
                'No se reconoce la lectura, asegúrese de escanear los códigos correctos' +
                '</span>');
            beep();
        }
    }

    function calcular_total_x_variedad(edit) {
        id_variedad = $('#id_variedad').val();
        id_unitaria_auto = $('.id_unitaria_auto');
        cosecha_x_variedad = $('#recepcion_' + id_variedad).val();
        total_x_variedad = 0;
        ramos_x_variedad = 0;
        for (x = 0; x < id_unitaria_auto.length; x++) {
            num_fila = id_unitaria_auto[x].id.substr(17);
            total_x_unitaria = 0;
            if (parseInt($('#cantidad_ramos_auto_' + num_fila).val()) > 0) {
                ramos_x_variedad += parseInt($('#cantidad_ramos_auto_' + num_fila).val());
                if (parseInt($('#tallos_x_ramo_auto_' + num_fila).val()) > 0)
                    total_x_unitaria = parseInt($('#cantidad_ramos_auto_' + num_fila).val()) * parseInt($('#tallos_x_ramo_auto_' + num_fila).val());
            }
            $('#total_x_unitaria_auto_' + num_fila).html(total_x_unitaria);
            total_x_variedad += total_x_unitaria;
        }
        if ((total_x_variedad + parseInt($('#total_tallos').val())) <= $('#total_tallos_recepcion').val() &&
            (total_x_variedad + parseInt($('#tallos_clasificados_' + id_variedad).val())) <= cosecha_x_variedad) {
            $('#th_total_x_variedad').html('Total: ' + total_x_variedad);
            $('#input_tallos_x_variedad_auto_' + id_variedad).val(total_x_variedad);

            $('#badge_tallos_clasificados_x_variedad_' + id_variedad).html((total_x_variedad + parseInt($('#tallos_clasificados_' + id_variedad).val())));
            $('#html_total_ramos').html((ramos_x_variedad + parseInt($('#total_ramos').val())));
            $('#html_total_tallos').html((total_x_variedad + parseInt($('#total_tallos').val())));
            $('#html_desechos').html(100 - Math.round(((total_x_variedad + parseInt($('#total_tallos').val())) * 100) / parseInt($('#total_tallos_recepcion').val())) + '%');

            if (!!edit)
                $('#html_info_scan').html('');
        } else {
            $('#html_info_scan').html('<span class="error">' +
                'La cantidad de tallos clasificados no puede superar a la cantidad cosechada' +
                '</span>');
            beep();
        }
        $('#input_escanear').focus();
    }

    function scan_ramos(data) {
        if (parseInt(data) > 0) {
            $('#html_info_scan').html('Se ha añadido <span class="badge">' +
                data +
                '</span> ramos por defecto');
            $('#ramos_x_defecto').val(data);
            cantidad_ramos_auto = $('.cantidad_ramos_auto');
            for (i = 0; i < cantidad_ramos_auto.length; i++) {
                if (cantidad_ramos_auto[i].value == '' || cantidad_ramos_auto[i].value == 0) {
                    cantidad_ramos_auto[i].value = data;
                    num_fila = cantidad_ramos_auto[i].id.substr(20);
                    total_x_unitaria = data * parseInt($('#tallos_x_ramo_auto_' + num_fila).val());
                    $('#total_x_unitaria_auto_' + num_fila).html(total_x_unitaria);
                }
            }
            calcular_total_x_variedad();
        } else {
            $('#html_info_scan').html('<span class="error">' +
                'No se reconoce la lectura, asegúrese de escanear los códigos correctos' +
                '</span>');
            beep();
        }
    }

    function scan_guardar() {
        if ($('#form-add_clasificacion_verde_x_variedad_auto_' + $('#id_variedad').val()).valid()) {
            if (parseInt($('#input_tallos_x_variedad_' + $('#id_variedad').val()).val()) <= parseInt($('#recepcion_' + $('#id_variedad').val()).val())) {
                modal_quest('modal_quest_store_clasificacion_verde', '<div class="alert alert-info text-center">' +
                    '¿Está seguro de guardar la información en el sistema?</div>',
                    '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '{{isPC() ? '35%' : ''}}', function () {
                        ids_unitaria = $('.id_unitaria_auto');

                        arreglo_detalles = [];
                        cant_ramos = 0;
                        for (u = 0; u < ids_unitaria.length; u++) {
                            num_fila = id_unitaria_auto[u].id.substr(17);
                            detalle = {
                                id_clasificacion_unitaria: $('#id_unitaria_auto_' + num_fila).val(),
                                cantidad_ramos: $('#cantidad_ramos_auto_' + num_fila).val(),
                                tallos_x_ramos: $('#tallos_x_ramo_auto_' + num_fila).val(),
                            };
                            arreglo_detalles.push(detalle);

                            cant_ramos += parseInt($('#cantidad_ramos_auto_' + num_fila).val());
                        }

                        if (cant_ramos > 0) {
                            datos = {
                                _token: '{{csrf_token()}}',
                                recepciones: $('#recepciones').val(),
                                detalles: arreglo_detalles,
                                fecha_ingreso: $('#fecha_ingreso').val(),
                                id_variedad: $('#id_variedad').val(),
                                id_clasificacion_verde: $('#id_clasificacion_verde').val()
                            };
                            if ($('#id_clasificacion_verde').val() != '')
                                urls = '{{url('clasificacion_verde/store_detalles')}}';
                            else
                                urls = '{{url('clasificacion_verde/store')}}';

                            post_jquery(urls, datos, function () {
                                cerrar_modals();
                                add_verde($('#fecha_recepciones').val());
                                buscar_listado();
                            });
                        } else {
                            alerta('<p class="text-center">Al menos ingrese un ramo de la clasificación</p>');
                        }
                    });
            } else {
                alerta('<p class="text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ' +
                    'La cantidad clasificada no puede ser mayor a la cantidad en recepción</p>');
            }
        }
    }

    function scan_reiniciar() {
        li_variedad = $('.li_variedad');
        for (i = 0; i < li_variedad.length; i++) {
            if (li_variedad[i].id.substr(12) == $('#id_variedad').val()) {
                seleccionar_variedad($('#id_variedad').val(), $('#' + li_variedad[i].id));
            }
        }
    }

    function scan_variedad(data) {
        li_variedad = $('.li_variedad');
        li = '';
        for (i = 0; i < li_variedad.length; i++) {
            if (li_variedad[i].id.substr(12) == data) {
                li = $('#' + li_variedad[i].id);
            }
        }
        if (li != '') {
            seleccionar_variedad(data, li);
        } else {
            $('#html_info_scan').html('<span class="error">' +
                'La variedad escaneada no está disponible en este formulario' +
                '</span>');
            beep();
        }

    }

    function remove_fila_auto(num_fila) {
        $('#row_auto_' + num_fila).remove();
        calcular_total_x_variedad(1);
    }
</script>