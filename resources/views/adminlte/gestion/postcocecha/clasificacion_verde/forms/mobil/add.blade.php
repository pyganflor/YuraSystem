<div id="div_form_verde" class="hide" style="margin-bottom: 10px">
    <table class="table-bordered table-striped" style="border: 2px solid #9d9d9d" width="100%">
        <tr>
            <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center">
                Fecha Recepciones
            </th>
            <td style="border-color: #9d9d9d; background-color: #e9ecef">
                <input class="text-center" type="date" style="width: 100%" id="fecha_recepciones" required onchange="select_fecha_recepciones()">
            </td>
        </tr>
        <tr>
            <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center">
                Fecha Clasf. Verde
            </th>
            <td style="border-color: #9d9d9d; background-color: #e9ecef">
                <input class="text-center" type="date" style="width: 100%" id="fecha_ingreso" required onchange="select_fecha_recepciones()">
            </td>
        </tr>
        <tr>
            <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center">
                Personal
            </th>
            <td style="border-color: #9d9d9d; background-color: #e9ecef">
                <input class="text-center" type="number" style="width: 100%" id="personal" value="{{isset($verde) ? $verde->personal : ''}}">
            </td>
        </tr>
        <tr>
            <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-center">
                Hora Inicio
            </th>
            <td style="border-color: #9d9d9d; background-color: #e9ecef">
                <input class="text-center" type="time" style="width: 100%" id="hora_inicio" value="{{isset($verde) ? $verde->hora_inicio : ''}}">
            </td>
        </tr>
        <tr>
            <th colspan="2" style="border-color: #9d9d9d; background-color: #e9ecef">
                <button type="button" class="btn btn-xs btn-block btn-primary" onclick="store_form_verde()">
                    <i class="fa fa-fw fa-save"></i> Guardar
                </button>
            </th>
        </tr>
    </table>
    <input type="hidden" id="id_clasificacion_verde" value="{{isset($verde) ? $verde->id_clasificacion_verde : ''}}">
</div>

<div id="div_formulario"></div>

<script>
    set_max_today($('#fecha_recepciones'));
    set_max_today($('#fecha_ingreso'));
    select_fecha_recepciones();

    function store_form_verde() {
        datos = {
            _token: '{{csrf_token()}}',
            fecha_recepciones: $('#fecha_recepciones').val(),
            fecha: $('#fecha_ingreso').val(),
            personal: $('#personal').val(),
            hora_inicio: $('#hora_inicio').val(),
            id: $('#id_clasificacion_verde').val(),
        };
        $('#div_form_verde').LoadingOverlay('show');
        $.post('{{url('clasificacion_verde/store_form_verde')}}', datos, function (retorno) {
            if (retorno.success) {
                select_fecha_recepciones();
            } else
                alerta(retorno.mensaje);
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#div_form_verde').LoadingOverlay('hide');
        });
    }

    function select_fecha_recepciones() {
        datos = {
            fecha_recepcion: $('#fecha_recepciones').val(),
            fecha_verde: $('#fecha_ingreso').val()
        };
        get_jquery('{{url('clasificacion_verde/select_fecha_recepciones')}}', datos, function (retorno) {
            $('#div_formulario').html(retorno);
            //$('#fecha_ingreso').val($('#fecha_recepciones').val());
        }, 'div_form_verde');
    }

    function construir_tabla() {
        datos = {
            variedad: $('#variedad_form').val()
        };
        get_jquery('{{url('clasificacion_verde/construir_tabla')}}', datos, function (retorno) {
            $('#body_tabla_formulario').html(retorno);
        }, 'table_formulario')
    }

    function calcular_tabla(pos) {
        ramos = $('#ramos_' + pos).val();
        tallos_x_ramo = $('#tallos_x_ramo_' + pos).val();
        $('#total_' + pos).html(ramos * tallos_x_ramo);
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
</script>