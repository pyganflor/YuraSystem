<script>
    buscar_listado_envios();
    calcular_precio_envio();

    function buscar_listado_envios() {
        $.LoadingOverlay('show');
        datos = {
            id_cliente : $('#id_cliente').val(),
            fecha      : $('#fecha').val(),
            estado     : $('#estado').val(),
        };
        $.get('{{url('envio/buscar')}}', datos, function (retorno) {
            $('#div_listado_envios').html(retorno);
            calcular_precio_envio();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_envios .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?anno=' + $('#anno').val() +
            '&estado=' + $('#estado').val() +
            '&desde=' + $('#desde').val() + '&'+
            '&desde=' + $('#hasta').val() + '&'+
            '&id_cliente=' + $('#id_cliente').val() + '&');
        $('#div_listado_envios').html($('#table_envios').html());
        $.get(url, function (resul) {
            //console.log(resul);
            $('#div_listado_envios').html(resul);
            estructura_tabla('table_content_envios');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function exportar_envios() {
        $.LoadingOverlay('show');
        window.open('{{url('envio/exportar')}}' + '?anno=' + $('#anno').val().trim() +
            '&id_cliente=' + $('#id_cliente').val() +
            '&desde=' + $('#desde').val() +
            '&estado=' + $('#estado').val() +
            '&hasta=' + $('#hasta').val(), '_blank');
        $.LoadingOverlay('hide');
    }

    function genera_comprobante_cliente(){
        if ($('#form_envios').valid()) {
            arrEnvios = [];
            $.each($('input:checkbox[name=check_envio]:checked'), function (i, j) {
                arrEnvios.push([
                    j.value,
                    $("#descuento_" + (j.id)).val(),
                    $("#guia_madre_" + (j.id)).val(),
                    $("#guia_hija_" + (j.id)).val(),
                    $("#codigo_pais_" + (j.id)).val(),
                    $("#destino_" + (j.id)).val(),
                    $("#codigo_pais_"+(j.id)+" option:selected").text()
                ]);
            });

            if (arrEnvios.length === 0) {
                modal_view('modal_view_msg_factura',
                    '<div class="alert text-center  alert-warning"><p>Debe seleccionar al menos un envío para facturar</p></div>',
                    '<i class="fa fa-fw fa-table"></i> Estatus facturas', true, false, '{{isPC() ? '50%' : ''}}');
                return false;
            }

            modal_quest('modal_message_facturar_envios',
                '<div class="alert alert-warning text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Esta seguro que desea generar el comprobante electrónico de los envíos seleccionados?</div>',
                '<i class="fa fa-file-code-o" aria-hidden="true"></i> Generar facturas', true, false, '{{isPC() ? '40%' : ''}}', function () {
                cerrar_modals();
                $.LoadingOverlay("show", {
                    image: "",
                    progress: true,
                    text: "Generando documento electrónico...",
                    textColor: "#fff",
                    progressColor : "#00a65a",
                    progressResizeFactor: "0.20",
                    background: "rgba(0, 0, 0, 0.75)"
                });
                var count = 0;
                var cantidad_envios = arrEnvios.length;
                var tiempo = cantidad_envios * 1500;
                var interval = setInterval(function () {
                    if(count>=15 && count<99)
                        $.LoadingOverlay("text", "Firmado documento electrónico...");

                    if (count >= 100) {
                        clearInterval(interval);
                        return;
                    }
                    count += 100 / cantidad_envios;
                    $.LoadingOverlay("progress", count);
                }, tiempo);
                datos = {
                    _token: '{{csrf_token()}}',
                    arrEnvios: arrEnvios
                };
                $.get('{{url('comprobante/generar_comprobante_factura')}}', datos, function (retorno) {
                    $.LoadingOverlay("hide");
                    modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus facturas', true, false,
                        '{{isPC() ? '50%' : ''}}');
                    buscar_listado_envios();
                });
            });
        }
    }

    function activar(input_descuento,id_check){
        var id = input_descuento.id.split("_")[1];
        $("#"+input_descuento.id,).removeAttr("readonly");
        $("#muestra_descuento_"+id).removeAttr("disabled");
    }

    function input_required(input){
        if($("input[type='checkbox']#"+input.id).is(':checked')) {
            $("#destino_" + input.id).attr('required', true);
        }else {
            $("#destino_" + input.id).attr('required', false);
        }
    }

    function calcular_precio_envio() {
        cant_forms = $("div#table_envios form").length;
        for (o=1;o<=cant_forms;o++){
            monto_total = 0.00;
            total_ramos = 0.00;
            total_piezas = 0.00;
            cant_rows = $(".input_cantidad_"+o).length;
            for (i = 1; i <= cant_rows; i++) {
                precio_especificacion = 0.00;
                ramos_totales_especificacion = 0.00;
                $.each($(".cantidad_"+o+"_"+i), function (p, q) {
                    $.each($(".td_ramos_x_caja_"+o+"_"+i), function (a, b) {
                        ramos_totales_especificacion += (q.value * b.value);
                    });
                    $.each($(".precio_"+o+"_"+i), function (y, z) {
                        precio_variedad = z.value == "" ? 0 : z.value;
                        ramos_x_caja = $(".input_ramos_x_caja_"+o+"_"+i + "_" + (y + 1)).val();
                        precio_especificacion += (parseFloat(precio_variedad) * parseFloat(ramos_x_caja) * q.value);
                    });
                });
                monto_total += parseFloat(precio_especificacion);
                $("#td_total_ramos_"+o+"_"+i).html(parseFloat(ramos_totales_especificacion));
                total_ramos += ramos_totales_especificacion;
                $("#td_precio_especificacion_"+o+"_"+i).html("$" + parseFloat(precio_especificacion).toFixed(2));
                total_piezas += parseInt($(".cantidad_"+o+"_"+i).val());
            }

            $("#total_piezas_"+o).html(total_piezas);
            $("#total_ramos_"+o).html(total_ramos);
            $("#total_monto_"+o).html(monto_total.toFixed(2));
        }

    }

    function buscar_codigo_dae(input,form){
        $.LoadingOverlay('show');
        datos = {
            codigo_pais : input.value,
            fecha_envio : $("form#"+form+" #fecha_envio").val()
        };
        $.get('{{url('envio/buscar_codigo_dae')}}', datos, function (retorno) {
            $("form#"+form+" #dae").val(retorno.codigo_dae);
            retorno === "" ? $("form#"+form+" #dae").removeAttr('disabled') : $("form#"+form+" #dae").attr('disabled','disabled');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function actualizar_envio(id_envio,form){
        if($("#"+form).valid()){
            $.LoadingOverlay('show');

           id_form = form.split("_")[2];
           cant_rows = $(".input_cantidad_"+id_form).length;
            arrDataPrecio = [];
           for (i=1;i<=cant_rows;i++){
               precio =  '';
               $.each($('.precio_'+id_form+"_"+i),function (j,k) {
                   precio +=  k.value+";"+$(".id_detalle_esp_emp_"+id_form+"_"+i)[j].value+"|";

               });
               arrDataPrecio.push({
                   precios : precio,
                   piezas : $(".cantidad_"+id_form+"_"+i).val()
               });
           }

           datos = {
               _token : '{{@csrf_token()}}',
                id_envio : id_envio,
                dae : $("form#"+form+ " #dae").val(),
                guia_madre : $("form#"+form+ " #guia_madre").val(),
                guia_hija : $("form#"+form+ " #guia_hija").val(),
                codigo_pais : $("form#"+form+ " #codigo_pais").val(),
                email : $("form#"+form+ " #email").val(),
                telefono : $("form#"+form+ " #telefono").val(),
                direccion : $("form#"+form+ " #direccion").val(),
                fecha_envio : $("form#"+form+ " #fecha_envio").val(),
                precios : arrDataPrecio
           };
            $.post('{{url('envio/actualizar_envio')}}', datos, function (retorno) {
                if (retorno.success) {
                    buscar_listado_envios();
                    modal_view('modal_editar_envio', retorno.mensaje, '<i class="fa fa-user-plus" aria-hidden="true"></i> Editar pedido', true, false,'{{isPC() ? '50%' : ''}}');
                } else {
                    alerta(retorno.mensaje);
                }
            }, 'json').fail(function (retorno) {
                alerta(retorno.responseText);
                alerta('Ha ocurrido un problema al guardar los datos del envío');
            }).always(function () {
                $.LoadingOverlay('hide');
            })
        }
    }
</script>
