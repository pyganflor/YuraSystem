<script>
    function estructura_tabla() {
        $('#table_content_semanas').DataTable({
            order: [],
            responsive: true,
            paging: false,
            info: false,
            search: false,
            columnDefs: [
                {
//                    targets: [9],
                    searchable: false,
                    orderable: false
                }
            ],
            language: {
                sSearch: "Filtrar en este listado: "
            }
        });
    }

    function select_accion(a) {
        $.LoadingOverlay('show');
        datos = {
            accion: a
        };
        get_jquery('{{url('semanas/get_accion')}}', datos, function (retorno) {
            $('#div_content_semanas').html('');
            $('#div_content_form_accions').html(retorno);
        });
        $.LoadingOverlay('hide');
    }

    function procesar() {
        if ($('#form-accions').valid()) {
            modal_quest('modal_quest_procesar',
                '<div class="alert alert-info text-center">Está a punto de generar las semanas para el año indicado<br>¿Desea continuar?</div>',
                '<i class="fa fa-fw fa-gears"></i> Procesar semanas', true, false, '35%', function () {
                    $.LoadingOverlay('show');
                    datos = {
                        _token: '{{csrf_token()}}',
                        id_variedad: $('#id_variedad').val(),
                        anno: $('#anno').val(),
                        fecha_inicial: $('#fecha_inicial').val(),
                        fecha_final: $('#fecha_final').val(),
                    };
                    post_jquery('{{url('semanas/procesar')}}', datos, function () {
                        cerrar_modals();
                        listar();
                    });
                    $.LoadingOverlay('hide');
                });
        }
    }

    function listar() {
        if ($('#form-accions').valid()) {
            datos = {
                id_variedad: $('#id_variedad').val(),
                anno: $('#anno').val(),
            };
            get_jquery('{{url('semanas/listar_semanas')}}', datos, function (retorno) {
                $('#div_content_semanas').html(retorno);
            });
        }
    }

    function save_semana(id) {
        if ($('#form-semana_curva-' + id).valid() && $('#form-semana_desecho-' + id).valid() &&
            $('#form-semana_poda-' + id).valid() && $('#form-semana_siembra-' + id).valid()) {
            datos = {
                _token: '{{csrf_token()}}',
                id_semana: id,
                curva: $('#curva_' + id).val(),
                desecho: $('#desecho_' + id).val(),
                semana_poda: $('#semana_poda_' + id).val(),
                semana_siembra: $('#semana_siembra_' + id).val(),
            };
            modal_quest('modal_quest_update_semana', '<div class="alert alert-info text-center">¿Desea actualizar los datos de la semana?</div>',
                '<i class="fa fa-fw fa-save"></i> Actualizar semana', true, false, '35%', function () {
                    $.LoadingOverlay('show');
                    post_jquery('{{url('semanas/update_semana')}}', datos, function () {
                        cerrar_modals();
                        listar();
                    });
                    $.LoadingOverlay('hide');
                });
        }
    }

    select_accion(1);

    /* ============================================================== */

    function select_all() {
        list = $('.check_week');
        cants = {
            total: list.length,
            activos: 0,
        };
        for (i = 0; i < cants['total']; i++) {
            if (list[i].checked) {
                cants['activos']++;
            }
        }
        if (cants['activos'] > 0) {
            for (i = 0; i < cants['total']; i++) {
                $('#' + list[i].id).prop('checked', false);
            }
        } else {
            for (i = 0; i < cants['total']; i++) {
                $('#' + list[i].id).prop('checked', true);
            }
        }
    }

    function select_all_options(option) {
        cant = 0;
        for (i = 0; i < $('.check_week').length; i++) {
            if ($('.check_week')[i].checked) {
                cant++;
            }
        }
        if (cant > 1) {
            if (option == 1) {
                igualar_datos(true, true, true, true);  // todos
            }
            if (option == 2) {
                igualar_datos(true, false, false, false);   // curva
            }
            if (option == 3) {
                igualar_datos(false, true, false, false);   // desecho
            }
            if (option == 4) {
                igualar_datos(false, false, true, false);   // semana_poda
            }
            if (option == 5) {
                igualar_datos(false, false, false, true);   // semana_poda
            }
        } else {
            alerta('<p class="text-center">Selecciona al menos dos semanas</p>');
        }
        $('#all_options').val('');
    }

    function igualar_datos(curva, desecho, semana_poda, semana_siembra) {
        datos = {
            curva: curva,
            desecho: desecho,
            semana_poda: semana_poda,
            semana_siembra: semana_siembra,
        };
        get_jquery('{{url('semanas/igualar_datos')}}', datos, function (retorno) {
            modal_form('modal_igualar_datos', retorno, '<i class="fa fa-fw fa-exchange"></i> Igualar todos los datos', false, true, '35%', function () {
                if ($('#form-igualar_datos').valid()) {
                    arreglo = [];
                    list = $('.check_week');
                    for (i = 0; i < list.length; i++) {
                        if (list[i].checked) {
                            arreglo.push(list[i].id.substr(6));
                        }
                    }
                    datos = {
                        _token: '{{csrf_token()}}',
                        curva: $('#curva').val(),
                        desecho: $('#desecho').val(),
                        semana_siembra: $('#semana_siembra').val(),
                        semana_poda: $('#semana_poda').val(),
                        ids: arreglo
                    };
                    post_jquery('{{url('semanas/store_igualar_datos')}}', datos, function () {
                        listar();
                        cerrar_modals();
                    });
                }
            });
        });
    }
</script>