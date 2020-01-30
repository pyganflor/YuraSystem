<script>
   // select_planta($('#filtro_predeterminado_planta').val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades' );

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
        ramos_x_caja_conf_empresa = parseFloat($("#ramos_x_caja_empresa").val()); //40
        precio_promedio_variedad = parseFloat($("#precio_variedad_"+id_cliente).val());
        ramos_totales = cajas_proyectadas * factor_cliente * ramos_x_caja_conf_empresa;
        cajas_equivalentes = cajas_proyectadas * factor_cliente;
        valor = (ramos_totales*precio_promedio_variedad).toFixed(2);
        $("#cajas_equivalentes_"+id_cliente+"_"+columna).html(cajas_equivalentes);
        $("#precio_proyectado_"+id_cliente+"_"+columna).html("$"+valor);
    }

    function store_proyeccion_venta(){
        clientes=[];
        semanas=[];
        semana_inicio="";
        semana_fin="";
        x=0;

        $.each($(".check_programacion_semana"),function (i,j) {
            if($(j).is(":checked")){
                if(x==0)
                    semana_inicio=$(j).val();

                semana_fin= $(j).val();
                semanas.push({
                    semana : $(j).val()
                });
                x++;
            }
        });



        if(semanas.length>0) {

            text='¿Está seguro de programar esta proyección entre las semanas '+semana_inicio+' y la '+semana_fin+'?';

            modal_quest('modal_update_proyeccion_venta', '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> '+text+' </div>', '<i class="fa fa-check"></i> Programar proyección', true, false, '<?php echo e(isPC() ? '40%' : ''); ?>', function () {
                $.LoadingOverlay('show');
                $.each(semanas,function(i,j){
                    $.each($('td.semana_'+j),function(l,k){
                        if(!$(k).find(".input_cajas_proyectadas").prop('disabled')) {
                            clientes.push({
                                id_cliente: $(j).find('.id_cliente').val(),
                                cajas_fisicas: $(j).find('.input_cajas_proyectadas').val(),
                                semana: $(j).find('.input_codigo_semana').val(),
                                cajas_equivalentes: parseFloat($("#cajas_equivalentes_" + $(j).find('.id_cliente').val() + "_" + $(j).find('.input_codigo_semana').val()).html()),
                                valor: $("#precio_proyectado_" + $(j).find('.id_cliente').val() + "_" + $(j).find('.input_codigo_semana').val()).html(),
                            });
                        }
                    });
                });
                datos = {
                    _token: '{{csrf_token()}}',
                    clientes : clientes,
                    semanas : semanas,
                    id_variedad : $("#filtro_predeterminado_variedad").val()
                };
                console.log(clientes);
                return false;
                post_jquery('{{url('proy_venta_semanal/store_proyeccion_venta')}}', datos, function () {
                    listar_proyecciones_venta_semanal();
                    cerrar_modals();
                });
                $.LoadingOverlay('hide');
            });
        }else{
            modal_view('modal_error_store_proyeccion', '<div class="alert alert-danger text-center"><p> Debe seleccionar desde que semana en adelante desea programar</p> </div>', '<i class="fa fa-times"></i> Proyeccion de venta', true, false, '50%');
            return false;
        }


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
                //listar_proyecciones_venta_semanal();
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


    function selecciona_check(check){
        $(check).is(":checked")
            ? checked = true
            : checked = false;

        semana = check.id.split("_")[1];
        z=parseInt(semana)+100;
        for(let x=(parseInt(semana)+1); x<z;x++){
<<<<<<< HEAD
=======
            console.log(x);
>>>>>>> 0fca28f7d6a4b9a6f65bdf779e66f68a7ca7447c
            if(checked){
                $("input#semana_"+x).prop('checked',true).attr('disabled',true);
            }else{
                $("input#semana_"+x).prop('checked',false).removeAttr('disabled');
            }
        }
    }

</script>
