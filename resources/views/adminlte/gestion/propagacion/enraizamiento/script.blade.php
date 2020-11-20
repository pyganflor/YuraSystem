<script>
    var cant_form_add = 1;

    //listar_contenedores();

    function listar_enraizamientos() {
        datos = {
            fecha: $('#fecha_search').val(),
        };
        $.LoadingOverlay('show');
        $.get('{{url('propag_config/listar_contenedores')}}', datos, function (retorno) {
            $('#listado_contenedores').html(retorno);
            estructura_tabla('table_contenedores', false, true);
            //$('#table_contenedores_filter').remove();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }


    function add_row_form_add() {
        cant_form_add++;
        var parametros_select_planta = ["'form_add_variedad_" + cant_form_add + "'", "'div_cargar_variedades_" + cant_form_add + "'", "'<option value=>Seleccione</option>'"];
        $('#table_form_add').append('<tr id="row_form_add_' + cant_form_add + '">' +
            '<td class="text-center">' +
            '<select name="form_add_planta_' + cant_form_add + '" id="form_add_planta_' + cant_form_add + '" style="width: 100%"' +
            ' onchange="select_planta($(this).val(), ' + parametros_select_planta[0] + ', ' + parametros_select_planta[1] + ', ' + parametros_select_planta[2] + ', 0)"> ' +
            $('#form_add_planta_1').html() +
            '</select>' +
            '</td>' +
            '<td class="text-center" id="div_cargar_variedades_' + cant_form_add + '">' +
            '<select name="form_add_variedad_' + cant_form_add + '" id="form_add_variedad_' + cant_form_add + '" style="width: 100%" ' +
            'onchange="buscar_enraizamiento_semanal(' + cant_form_add + ')"></select>' +
            '</td>' +
            '<td class="text-center">' +
            '<input type="number" style="width: 100%" id="form_add_cantidad_' + cant_form_add + '" class="text-center" min="0">' +
            '</td>' +
            '<td class="text-center" id="div_cargar_semanas_' + cant_form_add + '">' +
            '<input type="number" style="width: 100%" id="form_add_semanas_' + cant_form_add + '" class="text-center" min="0">' +
            '</td>' +
            '</tr>');
    }

    function buscar_enraizamiento_semanal(num_row) {
        datos = {
            _token: '{{csrf_token()}}',
            fecha: $('#fecha_search').val(),
            variedad: $('#form_add_variedad_' + num_row).val()
        };
        if (datos['variedad'] > 0) {
            $('#div_cargar_semanas_' + num_row).LoadingOverlay('show');
            $.post('{{url('enraizamiento/buscar_enraizamiento_semanal')}}', datos, function (retorno) {
                if (retorno.cantidad_semanas > 0) {
                    $('#form_add_semanas_' + num_row).val(retorno.cantidad_semanas);
                    $('#form_add_semanas_' + num_row).prop('readonly', true);
                } else {
                    $('#form_add_semanas_' + num_row).val('');
                    $('#form_add_semanas_' + num_row).prop('readonly', false);
                }
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
            }).always(function () {
                $('#div_cargar_semanas_' + num_row).LoadingOverlay('hide');
            })
        }
    }

    function store_enraizamiento() {
        data = [];
        for (i = 1; i <= cant_form_add; i++) {
            var d = {
                variedad: $('#form_add_variedad_' + i).val(),
                cantidad: $('#form_add_cantidad_' + i).val(),
                semanas: $('#form_add_semanas_' + i).val(),
            };
            if (d['variedad'] > 0 && d['cantidad'] > 0 && d['semanas'] > 0)
                data.push(d);
        }

        datos = {
            _token: '{{csrf_token()}}',
            fecha: $('#fecha_search').val(),
            data: data,
        };
        post_jquery('{{url('enraizamiento/store_enraizamiento')}}', datos, function () {
            for (i = 2; i <= cant_form_add; i++)
                $('#row_form_add_' + i).remove();
            cant_form_add = 1;
            $('#form_add_cantidad_1').val('');
            $('#form_add_semanas_1').val('');
        });
    }
</script>