<script>
    estructura_tabla('table_cuarto_frio', false);

    function editar_dia(pos_inv, pos_dia) {
        $('#span_editar_' + pos_inv + '_' + pos_dia).hide();
        $('#input_editar_' + pos_inv + '_' + pos_dia).show();
        $('#input_editar_' + pos_inv + '_' + pos_dia).focus();

        $('#input_accion_' + pos_inv + '_' + pos_dia).val('E');

        $('#btn_save_' + pos_inv).show();
    }

    function add_dia(pos_inv, pos_dia) {
        $('#span_editar_' + pos_inv + '_' + pos_dia).hide();
        $('#input_add_' + pos_inv + '_' + pos_dia).show();
        $('#input_add_' + pos_inv + '_' + pos_dia).focus();

        $('#input_accion_' + pos_inv + '_' + pos_dia).val('A');

        $('#btn_save_' + pos_inv).show();
    }

    function editar_inventario(pos_inv) {
        var add = [];
        var edit = [];
        for (i = 0; i <= 9; i++) {
            accion = $('#input_accion_' + pos_inv + '_' + i).val();
            valor = '';
            if (accion == 'A') {
                valor = $('#input_add_' + pos_inv + '_' + i).val();
                if (valor > 0)
                    add.push({
                        valor: valor,
                        dia: i,
                    });
            }
            if (accion == 'E') {
                valor = $('#input_editar_' + pos_inv + '_' + i).val();
                if (valor > 0)
                    edit.push({
                        valor: valor,
                        dia: i,
                    });
            }
        }
        data = {
            variedad: $('#variedad_' + pos_inv).val(),
            peso: $('#peso_' + pos_inv).val(),
            nombre_peso: $('#nombre_peso_' + pos_inv).val(),
            presentacion: $('#presentacion_' + pos_inv).val(),
            tallos_x_ramo: $('#tallos_x_ramo_' + pos_inv).val(),
            longitud_ramo: $('#longitud_ramo_' + pos_inv).val(),
            unidad_medida: $('#unidad_medida_' + pos_inv).val(),
        };
        if (add.length > 0) {
            datos = {
                _token: '{{csrf_token()}}',
                add: add,
                data: data
            };
            post_jquery('{{url('cuarto_frio/add_inventario')}}', datos, function () {
                cerrar_modals();
            });
        }
        if (edit.length > 0) {
            $('#tr_basura').show();
            for (i = 0; i < edit.length; i++) {
                $('.span_editar_' + edit[i]['dia']).hide();
                $('.input_add_' + edit[i]['dia']).show();
                $('.input_add_' + edit[i]['dia']).css('background-color', '#74e5ea');
                $('#input_add_' + pos_inv + '_' + edit[i]['dia']).hide();
                $('#input_editar_' + pos_inv + '_' + edit[i]['dia']).prop('readonly', true);

                list = $('.input_add_' + edit[i]['dia']);
                for (x = 0; x < list.length; x++) {
                    var name = list[x].name.substr(4);
                    var nombre_peso = $('#nombre_peso_' + x).val();

                    factor = data['nombre_peso'] / nombre_peso;
                    convert = $('#input_editar_' + pos_inv + '_' + edit[i]['dia']).val() * factor;
                    list[x].placeholder = parseInt(convert);
                    list[x].max = parseInt(convert);
                }

                $('#basura_dia_' + edit[i]['dia']).prop('placeholder', edit[i]['valor']);
                $('#basura_dia_' + edit[i]['dia']).prop('max', edit[i]['valor']);
                $('#btn_save_dia_' + edit[i]['dia']).show();

                $('#inventario_target_' + edit[i]['dia']).val(pos_inv);
            }
        } else {
            location.reload();
        }
    }

    function delete_dia(dia) {
        datos = {
            _token: '{{csrf_token()}}',
            dia: dia
        };
        post_jquery('{{url('cuarto_frio/delete_dia')}}', datos, function () {
            cerrar_modals();
            $.LoadingOverlay('show');
            location.reload();
        });
    }

    function save_dia(dia) {
        pos_inv = $('#inventario_target_' + dia).val();
        data = {
            variedad: $('#variedad_' + pos_inv).val(),
            peso: $('#peso_' + pos_inv).val(),
            nombre_peso: $('#nombre_peso_' + pos_inv).val(),
            presentacion: $('#presentacion_' + pos_inv).val(),
            tallos_x_ramo: $('#tallos_x_ramo_' + pos_inv).val(),
            longitud_ramo: $('#longitud_ramo_' + pos_inv).val(),
            unidad_medida: $('#unidad_medida_' + pos_inv).val(),
            editar: $('#input_editar_' + pos_inv + '_' + dia).val(),
            dia: dia
        };
        basura = $('#basura_dia_' + dia).val();
        arreglo = [];

        list = $('.input_add_' + dia);
        total_convert = 0;

        for (i = 0; i < list.length; i++) {
            if (list[i].value > 0) {
                var inv_i = list[i].name.substr(4);
                var nombre_peso = $('#nombre_peso_' + i).val();

                factor = nombre_peso / data['nombre_peso'];
                convert = list[i].value * factor;
                total_convert += convert;

                inventario = {
                    variedad: $('#variedad_' + inv_i).val(),
                    peso: $('#peso_' + inv_i).val(),
                    nombre_peso: $('#nombre_peso_' + inv_i).val(),
                    presentacion: $('#presentacion_' + inv_i).val(),
                    tallos_x_ramo: $('#tallos_x_ramo_' + inv_i).val(),
                    longitud_ramo: $('#longitud_ramo_' + inv_i).val(),
                    unidad_medida: $('#unidad_medida_' + inv_i).val(),
                    add: $('#input_add_' + inv_i + '_' + dia).val()
                };
                arreglo.push({
                    inventario: inventario
                });
            }
        }
        total_convert += parseInt(basura);

        if (total_convert <= data['editar']) {
            datos = {
                _token: '{{csrf_token()}}',
                data: data,
                arreglo: arreglo,
                basura: basura
            };
            post_jquery('{{url('cuarto_frio/save_dia')}}', datos, function () {
                cerrar_modals();
                $.LoadingOverlay('show');
                location.reload();
            });
        } else {
            alerta('<div class="alert alert-warning text-center">La cantidad de ramos total ingresada (' + total_convert + ') ' +
                'es mayor a la cantidad a editar (' + data['editar'] + ')</div>');
        }
    }
</script>