<script>
    select_planta($('#filtro_predeterminado_planta').val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades' );

    listar_proyecciones_venta_semanal();

    function listar_proyecciones_venta_semanal(){
        data= {
            id_cliente: $("#id_cliente").val(),
            id_planta: $(".planta").val(),
            id_variedad: $(".variedad").val(),
            desde: $(".desde").val(),
            hasta: $(".hasta").val(),
            criterio : $("#filtro_predeterminado_criterio").val(),
            top : $("#filtro_predeterminado_top").val()
        };
        get_jquery('{{url('proy_venta_semanal/listar_proyeccion_venta_semanal')}}', data, function (retorno) {
            $('#listado_proyecciones_venta_semanal').html(retorno);
        });
    }

    function store_factor_cliente(id_cliente){
        $.LoadingOverlay('show');
        datos = {
            _token : '{{csrf_token()}}',
            id_cliente : id_cliente,
            factor : $("#factor_cliente_"+id_cliente).val()
        };
        post_jquery('{{url('proy_venta_semanal/store_factor_cliente')}}', datos, function () {

        });
        $.LoadingOverlay('hide');
    }

    function calcular_proyeccion_cliente(id_cliente,columna) {
        cajas_proyectadas = parseFloat($("#cajas_proyectadas_"+id_cliente+"_"+columna).val());
        factor_cliente = parseFloat($("#factor_cliente_"+id_cliente).val());
        ramos_x_caja_conf_empresa = parseFloat($("#ramos_x_caja_empresa").val());
        precio_promedio_variedad = parseFloat($("#precio_variedad_"+id_cliente).val());
        ramos_totales = cajas_proyectadas * factor_cliente * ramos_x_caja_conf_empresa;
        cajas_equivalentes = cajas_proyectadas * factor_cliente;
        valor = (ramos_totales*precio_promedio_variedad).toFixed(2);
        $("#cajas_equivalentes_"+id_cliente+"_"+columna).html(cajas_equivalentes);
        $("#precio_proyectado_"+id_cliente+"_"+columna).html("$"+valor);
    }

    function store_proyeccion_venta(id_cliente,columna,id_variedad){
        modal_quest('modal_update_proyeccion_venta', '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de programar esta proyección de venta? </div>', '<i class="fa fa-check"></i> Programar proyección', true, false, '<?php echo e(isPC() ? '40%' : ''); ?>', function () {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                semana : columna,
                id_cliente :id_cliente,
                id_variedad : id_variedad,
                cajas_fisicas : $("#cajas_proyectadas_"+id_cliente+"_"+columna).val(),
                cajas_equivalentes : parseFloat($("#cajas_equivalentes_"+id_cliente+"_"+columna).html()),
                valor : $("#precio_proyectado_"+id_cliente+"_"+columna).html(),
            };

            post_jquery('{{url('proy_venta_semanal/store_proyeccion_venta')}}', datos, function () {
                cerrar_modals();
                //listar_proyecciones_venta_semanal();
            });
            $.LoadingOverlay('hide');
        });
    }

    function store_proyeccion_desecho(columna,id_variedad){
        modal_quest('modal_update_proyeccion_desecho', '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de programar este desecho? </div>', '<i class="fa fa-fw fa-trash"></i> Programar desecho', true, false, '<?php echo e(isPC() ? '40%' : ''); ?>', function () {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                semana : columna,
                id_variedad : id_variedad,
                desecho : $("#desecho_semana_"+columna).val(),
            };

            post_jquery('{{url('proy_venta_semanal/store_proyeccion_desecho')}}', datos, function () {
                listar_proyecciones_venta_semanal();
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        });
    }

    function store_precio_promedio(id_cliente,id_variedad){
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_cliente :id_cliente,
            id_variedad : id_variedad,
            precio_promedio : $("#precio_variedad_"+id_cliente).val()
        };

        post_jquery('{{url('proy_venta_semanal/store_precio_promedio')}}', datos, function () {
            cerrar_modals();
            //listar_proyecciones_venta_semanal();
        });
        $.LoadingOverlay('hide');
    }

</script>
