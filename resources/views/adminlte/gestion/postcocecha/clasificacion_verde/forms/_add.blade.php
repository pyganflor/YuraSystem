@if(count($recepciones)>0)
    <div class="row">
        <div class="col-md-5">
            <div class="list-group">
                @php
                    $total_tallos = 0;
                @endphp
                @foreach($variedades as $v)
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                       onclick="seleccionar_variedad('{{$v['variedad']->id_variedad}}', $(this))">
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
    function seleccionar_variedad(id_variedad, li) {
        datos = {
            id_variedad: id_variedad,
            id_clasificacion_verde: $('#id_clasificacion_verde').val()
        };
        get_jquery('{{url('clasificacion_verde/add/cargar_tabla_variedad')}}', datos, function (retorno) {
            $('#div_table_x_variedad').html(retorno);
            $('.list-group-item').removeClass('active');
            li.addClass('active');

            $('#html_total_tallos').html($('#total_tallos').val());
            $('#html_total_ramos').html($('#total_ramos').val());
            $('#html_desechos').html($('#desechos').val() + '%');

            variedades = $('.ids_variedad');
            for (i = 0; i < variedades.length; i++) {
                $('#badge_tallos_clasificados_x_variedad_' + variedades[i].id.substr(20)).html($('#tallos_clasificados_' + variedades[i].id.substr(20)).val());
            }
        });
    }

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
        if ($('#form-add_clasificacion_verde_x_variedad_' + $('#id_variedad').valid())) {
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

                    add_verde($('#fecha_recepciones').val());
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
</script>