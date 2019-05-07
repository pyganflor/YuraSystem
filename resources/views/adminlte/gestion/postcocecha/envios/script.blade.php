<script>
    buscar_listado_envios();
    $(document).on('load',function () { calcular_precio_envio(); });

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
            $('#div_listado_envios').html(resul);
            estructura_tabla('table_content_envios');
            calcular_precio_envio();
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

    function genera_comprobante_cliente(id_envio,form,action){
        if ($('#'+form).valid()) {
            id_form = form.split("_")[2];
            modal_quest('modal_message_facturar_envios',
                '<div class="alert alert-info text-center">  <label>Se generará el comprobante electrónico para este envío</label></div>'+
                '<div class="alert alert-info text-center"> <input type="checkbox" id="envio_correo" name="envio_correo"style="position: relative;top: 3px;" checked> <label for="envio_correo">¿Enviar Correo electrónico al cliente ?</label> </div>'+
                '<div class="alert alert-info text-center"> <input type="checkbox" id="envio_correo_agencia_carga" name="envio_correo_agencia_carga"style="position: relative;top: 3px;" checked> <label for="envio_correo">¿Enviar Correo electrónico a la Agencia de carga?</label> </div>',
                '<i class="fa fa-file-code-o" aria-hidden="true"></i> Se realizaran las siguientes acciones', true, false, '{{isPC() ? '40%' : ''}}', function () {
                    datos = {
                        _token: '{{csrf_token()}}',
                        id_envio : id_envio,
                        guia_madre : $("form#"+form+ " #guia_madre").val(),
                        guia_hija : $("form#"+form+ " #guia_hija").val(),
                        codigo_pais : $("form#"+form+ " #codigo_pais").val(),
                        dae : $("form#"+form+ " #dae").val(),
                        destino : $("form#"+form+ " #direccion").val(),
                        email : $("form#"+form+ " #email").val(),
                        telefono : $("form#"+form+ " #telefono").val(),
                        pais : $("form#"+form+ " #codigo_pais option:selected").text(),
                        fecha_envio : $("form#"+form+ " #fecha_envio").val(),
                        cant_variedades : $("form#"+form+ " table tbody#tbody_inputs_pedidos tr").length,
                        update : action == 'update' ? true : false,
                        almacen : $("form#"+form+ " #almacen").val(),
                        envio_correo : $("#envio_correo").is(":checked"),
                        envio_correo_agencia_carga : $("#envio_correo_agencia_carga").is(":checked")
                    };
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
                var tiempo = 2000;
                var interval = setInterval(function () {
                    if(count>=15 && count<99)
                        $.LoadingOverlay("text", "Firmado documento electrónico...");
                    if (count >= 100) {
                        clearInterval(interval);
                        return;
                    }
                    count += 100;
                    $.LoadingOverlay("progress", count);
                }, tiempo);
                $.get('{{url('comprobante/generar_comprobante_factura')}}', datos, function (retorno) {
                    modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus facturas', true, false,
                        '{{isPC() ? '50%' : ''}}');
                    buscar_listado_envios();
                }).always(function () {
                    $.LoadingOverlay("hide");
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

    function buscar_codigo_dae(input,form,factura_cliente_tercero){
        $.LoadingOverlay('show');
        datos = {
            codigo_pais : input.value,
            fecha_envio : $("form#"+form+" #fecha_envio").val()
        };
        $.get('{{url('envio/buscar_codigo_dae')}}', datos, function (retorno) {
            factura_cliente_tercero
                ? $("form#"+form+" #dae_cliente_tercero").val(retorno.codigo_dae)
                : $("form#"+form+" #dae").val(retorno.codigo_dae);

            retorno.codigo_dae == "" ? $("form#"+form+" #dae").removeAttr('disabled') : $("form#"+form+" #dae").attr('disabled','disabled');

            if(retorno.codigo_empresa == datos.codigo_pais){
               $("form#"+form+ " #dae").removeAttr('required').val('');
               $("form#"+form+ " #dae_cliente_tercero").removeAttr('required').val('');
            }else{
               $("form#"+form+ " #dae").attr('required',true).val(retorno.codigo_dae);
               $("form#"+form+ " #dae_cliente_tercero").attr('required',true).val(retorno.codigo_dae);
            }
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function actualizar_envio(id_envio,form,tipo_pedido){
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
               aerolinea : $("form#"+form+ " #aerolinea").val(),
               precios : arrDataPrecio,
               almacen : $("form#"+form+ " #almacen").val(),
               tipo_pedido : tipo_pedido
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
            });
        }
    }

    function factura_tercero(id_envio) {
        datos = {
            id_envio : id_envio
        };
        $.get('{{url('envio/factura_cliente_tercero')}}', datos, function (retorno) {
            modal_form('modal_factura_cliente_tercero', retorno, '<i class="fa fa-user-plus" aria-hidden="true">' +
                '</i> Datos del cliente a facturar', true, false, '75%', function () {
              store_datos_factura_cliente_tercero(id_envio);
            });
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    }

    function store_datos_factura_cliente_tercero(id_envio) {
        if ($('#form_add_cliente_factura_tercero').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_factura_cliente_tercero: $('#id_factura_cliente_tercero').val(),
                id_envio: id_envio,
                nombre: $('#nombre_cliente_tercero').val(),
                identificacion: $('#identificacion').val(),
                codigo_pais: $("#pais_cliente_tercero").val(),
                provincia: $("#provincia_cliente_tercero").val(),
                correo: $("#correo_cliente_tercero").val(),
                telefono: $("#telefono_cliente_tercero").val(),
                direccion: $("#direccion_cliente_tercero").val(),
                codigo_impuesto: $("#codigo_impuesto").val(),
                tipo_identificacion : $('#tipo_identificacion').val(),
                codigo_impuesto_porcentaje : $('#tipo_impuesto').val(),
                almacen : $('#almacen_cliente_tercero').val(),
                dae : $('#dae_cliente_tercero').val()
            };
            post_jquery('{{url('envio/store_datos_factura_cliente_tercero')}}', datos, function () {
                buscar_listado_envios();
                calcular_precio_envio();
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        }
    }

    function delete_factura_tercero(id_envio){
        modal_quest('modal_message_delete_factura_tercero',
            '<div class="alert alert-warning text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Esta seguro que desea eliminar los datos del cliente a facturar?</div>',
            '<i class="fa fa-file-code-o" aria-hidden="true"></i>Datos de facturación', true, false, '{{isPC() ? '40%' : ''}}', function () {
                $.LoadingOverlay('show');
                datos = {
                    _token: '{{csrf_token()}}',
                    id_envio: id_envio,
                };
                post_jquery('{{url('envio/delete_datos_factura_cliente_tercero')}}', datos, function () {
                    buscar_listado_envios();
                    calcular_precio_envio();
                    cerrar_modals();
                });
                $.LoadingOverlay('hide');
            });
    }

    function agregar_correo(form) {


        $.get('{{url('envio/agregar_correo')}}', datos, function (retorno) {

        });

    }

</script>
