<script>

    listar_resumen_pedidos($("#fecha_pedidos_search").val(), true);

    function ver_envio(id_pedido) {
        $.LoadingOverlay('show');
        datos = {
            id_pedido: id_pedido
        };

        $.get('{{url('pedidos/ver_envio')}}', datos, function (retorno) {
            modal_view('modal_view_envios_facturas', retorno, '<i class="fa fa-plane" aria-hidden="true"></i> Desglose de los envíos del pedido', true, false, '85%');
            //estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_orden_semanal() {
        get_jquery('{{url('pedidos/add_orden_semanal')}}', {}, function (retorno) {
            modal_view('modal-view_add_orden_semanal', retorno, '<i class="fa fa-fw fa-plus"></i> Agregar Orden Semanal', true, false,
                '{{isPC() ? '95%' : ''}}');

        });
    }

    function editar_pedido(id_cliente, id_pedido,secuencial_ficticio,secuencial) {
        add_pedido('', '', 'pedidos', id_pedido);
        datos = {
            id_cliente: id_cliente,
            id_pedido: id_pedido
        };
        setTimeout(function () {
            $.get('{{url('clientes/inputs_pedidos_edit')}}', datos, function (retorno) {
                $("#table_campo_pedido").html(retorno);
                $('select#id_cliente_venta option[value='+id_cliente+']').attr('selected', true);
                $('select#id_cliente_venta').attr('disabled', true);

                if(secuencial_ficticio!=''){
                    $("#factura_ficticia").attr({'disabled':true,'checked':true});
                    $("#numero_ficticio").attr({'disabled':false}).val(secuencial_ficticio.substr(6, secuencial_ficticio.length));
                }else if(secuencial!=''){
                    $("#factura_ficticia").attr({'disabled':true,'checked':false});
                    $("#numero_ficticio").val(secuencial);
                }

                datos = {
                    id_pedido: id_pedido,
                };
                $.get('{{url('pedidos/editar_pedido')}}', datos, function (retorno) {
                    $("#fecha_de_entrega").val(retorno.pedido[0].fecha_pedido);
                    $("#iva_cliente").val(retorno.iva_cliente);
                    $(".iva_pedido").html(retorno.iva_cliente+"%");
                    calcular_precio_pedido();
                    //$("#descripcion").val(retorno[0].descripcion);
                });
            }).always(function () {
                $.LoadingOverlay('hide');
            });
        }, 300);
    }

    function add_pedido_personalizado() {
        get_jquery('{{url('pedidos/add_pedido_personalizado')}}', {}, function (retorno) {
            modal_view('modal_view_add_pedido_personalizado', retorno, '<i class="fa fa-fw fa-gift"></i> Pedidos personalzados', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }

    function distribuir_orden_semanal(id_pedido) {
        datos = {
            id_pedido: id_pedido
        };
        get_jquery('{{url('pedidos/distribuir_orden_semanal')}}', datos, function (vista) {
            modal_view('modal-view_distribuir_orden_semanal', vista, '<i class="fa fa-fw fa-gift"></i> Distribución', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }

    function ver_distribucion_orden_semanal(id_pedido) {
        datos = {
            id_pedido: id_pedido
        };
        get_jquery('{{url('pedidos/ver_distribucion_orden_semanal')}}', datos, function (vista) {
            modal_view('modal-view_ver_distribucion_orden_semanal', vista, '<i class="fa fa-fw fa-gift"></i> Distribución', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }

    function cambiar_color(c, col, esp_emp) {
        fondo = $('#fondo_color_' + c).val();
        texto = $('#texto_color_' + c).val();

        $('.elemento_color_' + col + '_' + esp_emp).css('background-color', fondo);
        $('.elemento_color_' + col + '_' + esp_emp).css('color', texto);
    }

    function crear_packing_list(id_pedido){
        modal_quest('modal_packing_list', "<div class='alert alert-info text-center'>¿Desea crear el Packing List de este pedido?</div>", "<i class='fa fa-exclamation-triangle' ></i> Seleccione una opción",true, false, '{{isPC() ? '35%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_pedido : id_pedido
            };
            post_jquery('{{url('pedidos/crear_packing_list')}}', datos, function (retorno) {
                listar_resumen_pedidos($("#fecha_pedidos_search").val(), true);
               cerrar_modals();
            });
        });
    }

    function store_especificacion_pedido(id_agencia_carga,id_pedido,vista){
        $.LoadingOverlay('show');
        var arr_especificaciones = [], arr_ordenado, arrDatosExportacion = [], cant_datos_exportacion = $(".th_datos_exportacion").length;
        $.each($("input.orden"), function (i, j) {
            if (j.value !== '')
                arr_especificaciones.push(j.value);
        });
        arr_ordenado = arr_especificaciones.sort(menor_mayor);
        for (z = 0; z < arr_ordenado.length; z++) {
            if (cant_datos_exportacion > 0) {
                $.each($('input.orden'), function (i, j) {
                    arrDatosExportacionEspecificacion = [];
                    if (arr_ordenado[z] === j.value) {
                        for (a = 1; a <= cant_datos_exportacion; a++) { //1
                            nombre_columna_dato_exportacion = $("#th_datos_exportacion_" + a).text().trim().toUpperCase();
                            if( $("#input_" + nombre_columna_dato_exportacion + "_" + (i + 1)).val() !== ""){
                                arrDatosExportacionEspecificacion.push({
                                    valor: $("#input_" + nombre_columna_dato_exportacion + "_" + (i + 1)).val(),
                                    id_dato_exportacion : $("#id_dato_exportacion_" + nombre_columna_dato_exportacion + "_" + (i + 1)).val(),
                                    id_detalle_pedido : $("#id_det_ped_"+(i+1)).val()
                                });
                            }
                        }
                        arrDatosExportacion.push(arrDatosExportacionEspecificacion);
                    }
                });
            }
        }
        datos ={
            arrDatosExportacion : arrDatosExportacion,
            id_agencia_carga : $("#id_agencia_carga_1").val(),
            id_pedido : id_pedido,
            _token : '{{csrf_token()}}'
        };
        post_jquery('clientes/store_especificacion_pedido', datos, function () {
            cerrar_modals();
            listar_resumen_pedidos($('#fecha_pedidos_search').val(), true);

        });
        $.LoadingOverlay('hide');
    }
</script>
