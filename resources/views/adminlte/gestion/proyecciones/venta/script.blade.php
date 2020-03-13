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

    function calcular_proyeccion_cliente(id_cliente,semana) {

        if($("input#semana_"+semana).is(":checked")){

            if(id_cliente!=undefined){
                //OBTENCION DE VALORES INICIALES//
                cajas_inicial = parseFloat($("input.cajas_fisicas_inicial_"+id_cliente+"_"+semana).val());
                cajas_equivalente_inicial = parseFloat($("input.cajas_equivalente_inicial_"+id_cliente+"_"+semana).val());
                valor_inicial = parseFloat($("input.valor_inicial_"+id_cliente+"_"+semana).val());
                //FIN DE OBTENCION DE VALORES INICIALES//

                //OBTENCION DE DATOS INICIALES TOTALES//
                total_cajas_semana_inicial = parseFloat($("b.total_cajas_semana_"+semana).html().trim());
                total_cajas_equivalentes_semana = parseFloat($("b.total_cajas_equivalentes_semana_"+semana).html().trim());
                total_dinero_semana = parseFloat($("b.total_dinero_semana_"+semana).html().trim().substring(1));
                //FIN DE OBTENCION DE DATOS INICIALES TOTALES//

                //CALCULOS DE LA CELDA A EDITAR EN LA PROYECCION//
                cajas_proyectadas = parseFloat($("#cajas_proyectadas_"+id_cliente+"_"+semana).val());
                factor_cliente = parseFloat($("#factor_cliente_"+id_cliente).val());
                ramos_x_caja_conf_empresa = parseFloat($("#ramos_x_caja_empresa").val()); //40
                precio_promedio_variedad = parseFloat($("#precio_variedad_"+id_cliente).val());
                ramos_totales = cajas_proyectadas * factor_cliente * ramos_x_caja_conf_empresa;
                cajas_equivalentes = cajas_proyectadas * factor_cliente;
                valor = (ramos_totales*precio_promedio_variedad).toFixed(2);
                //FIN CALCULOS DE LA CELDA A EDITAR EN LA PROYECCION//


                //ASGINACION DE CALCULOS DE LA CELDA A EDITAR EN LA PROYECCION//
                cajas_equivalentes = isNaN(cajas_equivalentes) ? 0 : cajas_equivalentes;
                valor = isNaN(valor) ? 0 : valor;
                //desecho_semana = parseFloat($("input#desecho_semana_"+semana).val());
                $("#cajas_equivalentes_"+id_cliente+"_"+semana).html(cajas_equivalentes.toFixed(2));
                $("#precio_proyectado_"+id_cliente+"_"+semana).html("$"+valor);
                //FIN DE ASGINACION DE CALCULOS DE LA CELDA A EDITAR EN LA PROYECCION//


                //CALCULOS VALORES TOTALES//
                total_cajas_semana_inicial = isNaN(total_cajas_semana_inicial) ? 0 : total_cajas_semana_inicial;
                cajas_inicial = isNaN(cajas_inicial) ? 0 : cajas_inicial;
                cajas_proyectadas = isNaN(cajas_proyectadas ) ? 0 : cajas_proyectadas;
                total_cajas_dinamico = total_cajas_semana_inicial-cajas_inicial+cajas_proyectadas;

                total_cajas_equivalentes_dinamico = total_cajas_equivalentes_semana-cajas_equivalente_inicial+cajas_equivalentes;
                total_valor_dinamico = total_dinero_semana-valor_inicial+parseFloat(valor);
                //FIN CALCULOS VALORES TOTALES//

                //REINICIO DE VALORES INICIALES//
                $("input.cajas_fisicas_inicial_"+id_cliente+"_"+semana).val(cajas_proyectadas);
                $("input.cajas_equivalente_inicial_"+id_cliente+"_"+semana).val(cajas_equivalentes);
                $("input.valor_inicial_"+id_cliente+"_"+semana).val(valor);
                //FIN DE REINICIO DE VALORES INICIALES//


                //REINICIO DE VALORES TOTALES//
                $("b.total_cajas_semana_"+semana).html(total_cajas_dinamico.toFixed(2));
                $("b.total_cajas_equivalentes_semana_"+semana).html(total_cajas_equivalentes_dinamico.toFixed(2));
                $("b.total_dinero_semana_"+semana).html("$"+total_valor_dinamico.toFixed(2));
                //FIN REINICIO DE VALORES TOTALES//
            }

            //OBTENCION DE LOS DATOS INICIALES GENERALES PARA AFECTAR LOS SALDOS//
            saldo_inicial = parseFloat($("b.saldo_inicial_"+semana).html());
            cajas_proyectadas_semana = parseFloat($("b.cajas_proyectas_semana_"+semana).html());
            desecho =  parseFloat($("input#desecho_semana_"+semana).val());
            saldo_final_inicial = parseFloat($('b.saldo_final_'+semana).html().trim());
            cajas_equivalentes_total=parseFloat($("b.total_cajas_equivalentes_semana_"+semana).html().trim());
            //FIN DE LA OBTENCION DE LOS DATOS INICIALES GENERALES PARA AFECTAR LOS SALDOS//

            saldo_final = saldo_inicial+cajas_proyectadas_semana-desecho-cajas_equivalentes_total;

            $('b.saldo_final_'+semana).html(saldo_final.toFixed(2));

            z=parseInt(semana)+100;
            y=0;
            for(let x=(parseInt(semana)+1); x<z;x++){
                if($("b.cajas_proyectas_semana_"+x).length>0){
                    if(y==0){
                        saldo_inicial = saldo_final;
                    }else{
                        saldo_inicial= saldo_final_anterior;
                    }
                    cajas_proyectadas_semana = parseFloat($("b.cajas_proyectas_semana_"+x).html().trim());
                    desecho =  parseFloat($("input#desecho_semana_"+x).val());
                    cajas_equivalentes_total=parseFloat($("b.total_cajas_equivalentes_semana_"+x).html().trim());

                    saldo_final = saldo_inicial+cajas_proyectadas_semana-desecho-cajas_equivalentes_total;

                    $("b.saldo_inicial_"+x).html(saldo_inicial.toFixed(2));
                    $("b.saldo_final_"+x).html(saldo_final.toFixed(2));
                    saldo_final_anterior= saldo_final;
                    y++;
                }
            }

        }else{
            modal_view('modal_error_calcula_proyeccion', '<div class="alert alert-danger text-center"><p> Debe seleccionar desde que semana en adelante desea programar</p> </div>', '<i class="fa fa-times"></i> Proyeccion de venta', true, false, '50%');
        }

    }

    function store_proyeccion_venta(){
        clientes=[];
        semanas=[];
        semana_inicio="";
        saldos=[];
        semana_fin="";
        desecho=[];
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
                    $.each($('td.semana_'+j.semana),function(l,k){
                        if(!$(k).find(".input_cajas_proyectadas").prop('disabled')) {
                            clientes.push({
                                id_cliente: $(k).find('.id_cliente').val(),
                                cajas_fisicas: $(k).find('.input_cajas_proyectadas').val(),
                                semana: j.semana,
                                cajas_equivalentes: parseFloat($("#cajas_equivalentes_" + $(k).find('.id_cliente').val() + "_" + $(k).find('.input_codigo_semana').val()).html()),
                                valor: $("#precio_proyectado_" + $(k).find('.id_cliente').val() + "_" + $(k).find('.input_codigo_semana').val()).html(),
                            });
                        }
                    });
                    $.each($("td.desecho_semana_"+j.semana),function(o,p){
                        if(!$("input#desecho_semana_"+j.semana).prop('disabled')){
                            desecho.push({
                                cantidad : $(p).find('input.input_semana_'+j.semana).val(),
                                semana : j.semana
                            });
                        }
                    });
                    $.each($("b.saldo_inicial_"+j.semana),function(q,r){
                        saldos.push({
                            inicial : parseFloat($(r).html().trim()),
                            final : parseFloat($("b.saldo_final_"+j.semana).html().trim()),
                            semana: j.semana
                        });
                    });
                });
                datos = {
                    _token: '{{csrf_token()}}',
                    clientes : clientes,
                    semanas : semanas,
                    desecho : desecho,
                    saldos : saldos,
                    id_variedad : $("#filtro_predeterminado_variedad").val()
                };

                $.post('{{url('proy_venta_semanal/store_proyeccion_venta')}}', datos, function (retorno){
                    if (retorno.success) {
                        alerta_accion(retorno.mensaje, function () {
                            listar_proyecciones_venta_semanal();
                            cerrar_modals();
                        });
                    } else {
                        alerta(retorno.mensaje);
                    }
                }).always(function () {
                    $.LoadingOverlay('hide');
                });
            });
        }else{
            modal_view('modal_error_store_proyeccion', '<div class="alert alert-danger text-center"><p> Debe seleccionar desde que semana en adelante desea programar</p> </div>', '<i class="fa fa-times"></i> Proyeccion de venta', true, false, '50%');
        }

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
            if(checked){
                $("input#semana_"+x).prop('checked',true).attr('disabled',true);
            }else{
                $("input#semana_"+x).prop('checked',false).removeAttr('disabled');
            }
        }
    }

    function calcula_totales(inicio,id_cliente){
        //OBTENCION DE LOS DATOS INICIALES GENERALES PARA AFECTAR LOS SALDOS//
        saldo_inicial = parseFloat($("b.saldo_inicial_"+semana).html());
        cajas_proyectadas_semana = parseFloat($("b.cajas_proyectas_semana_"+semana).html());
        desecho =  parseFloat($("input#desecho_semana_"+semana).val());
        //FIN DE LA OBTENCION DE LOS DATOS INICIALES GENERALES PARA AFECTAR LOS SALDOS//

        cajas=0;
        cajas_equivalentes =0;
        dinero = 0;
        //z=parseInt(semana)+100;
        $.each($("b#cajas_equivalentes_"+id_cliente+"_"+semana),function (i,j) {
            cajas+= parseFloat($(j).trim());
        });

    }

</script>
