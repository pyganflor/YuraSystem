<script>
    var cant_row_add_cosecha = 1;
    listar_cosechas();
    select_cama(1);

    function listar_cosechas() {
        datos = {
            fecha: $('#fecha_search').val(),
            variedad: $('#variedad_search').val(),
        };
        $.LoadingOverlay('show');
        $.get('{{url('cosecha_plantas_madres/listar_cosechas')}}', datos, function (retorno) {
            $('#div_listado_cosechas').html(retorno);
            estructura_tabla('table_cosechas', false, true);
            //$('#table_cosechas_filter').remove();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function select_cama(num_form) {
        datos = {
            _token: '{{csrf_token()}}',
            cama: $('#cama_add_cosecha_' + num_form).val()
        };
        $.LoadingOverlay('show');
        $.post('{{url('cosecha_plantas_madres/select_cama')}}', datos, function (retorno) {
            $('#td_variedad_add_cosecha_' + num_form).html(retorno.variedad['siglas']);
            $('#variedad_add_cosecha_' + num_form).val(retorno.variedad['id_variedad']);
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }

    function add_row_add_cosecha() {
        cant_row_add_cosecha++;
        $('#table_form_add_cosecha').append('<tr id="tr_add_cosecha_' + cant_row_add_cosecha + '">' +
            '<td class="text-center" style="border-color: #9d9d9d;">' +
            '<select id="cama_add_cosecha_' + cant_row_add_cosecha + '" name="cama_add_cosecha_' + cant_row_add_cosecha + '" ' +
            'style="width: 100%" onchange="select_cama(' + cant_row_add_cosecha + ')">' + $('#cama_add_cosecha_1').html() +
            '</select>' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d;" id="td_variedad_add_cosecha_' + cant_row_add_cosecha + '">' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d;">' +
            '<input type="number" onkeyup="return isNumber(event)" id="cantidad_add_cosecha_' + cant_row_add_cosecha + '" ' +
            'style="width: 100%" class="text-center">' +
            '</td>' +
            '<input type="hidden" id="variedad_add_cosecha_' + cant_row_add_cosecha + '">' +
            '</tr>');
        select_cama(cant_row_add_cosecha);
    }

    function store_cosechas() {
        data = [];
        for (i = 1; i <= cant_row_add_cosecha; i++) {
            v = {
                cama: $('#cama_add_cosecha_' + i).val(),
                variedad: $('#variedad_add_cosecha_' + i).val(),
                cantidad: $('#cantidad_add_cosecha_' + i).val(),
            };
            if (v['cantidad'] > 0)
                data.push(v);
        }
        if (data.length > 0) {
            datos = {
                _token: '{{csrf_token()}}',
                fecha: $('#fecha_search').val(),
                cantidades: data,
            };
            if (datos['fecha'] != '') {
                $.LoadingOverlay('show');
                $.post('{{url('cosecha_plantas_madres/store_cosechas')}}', datos, function (retorno) {
                    alerta(retorno.mensaje);
                    if (retorno.success) {
                        listar_cosechas();
                        for (i = 2; i <= cant_row_add_cosecha; i++)
                            $('#tr_add_cosecha_' + i).remove();
                        cant_row_add_cosecha = 1;
                        $('#cantidad_add_cosecha_1').val('');
                    }
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                }).always(function () {
                    $.LoadingOverlay('hide');
                })
            } else {
                alerta('<div class="alert alert-danger text-center">Debe indicar la fecha</div>')
            }
        } else {
            alerta('<div class="alert alert-danger text-center">Debe ingresar las cantidades</div>')
        }
    }
</script>