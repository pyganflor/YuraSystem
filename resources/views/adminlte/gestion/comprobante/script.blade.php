<script>
    buscar_listado_comprobante();

    function buscar_listado_comprobante() {
        $.LoadingOverlay('show');
        datos = {
            anno               : $('#anno').val(),
            id_cliente         : $('#id_cliente').val(),
            codigo_comprobante : $("#codigo_comprobante").val(),
            fecha              : $('#fecha').val(),
            estado             : $('#estado').val()
        };
        $.get('{{url('comprobante/buscar')}}', datos, function (retorno) {
            $('#div_listado_comprobante').html(retorno);
            estructura_tabla('table_content_comprobante');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
    
    function enviar_comprobante(tipo_comprobante) {
        arrComprobante = [];
        $.each($('input:checkbox[name=enviar]:checked'), function (i, j) {
            arrComprobante.push(j.value);
        });

        if (arrComprobante.length === 0) {
            modal_view('modal_view_msg_factura',
                '<div class="alert text-center  alert-warning"><p><i class="fa fa-fw fa-exclamation-triangle"></i> Debe seleccionar al menos un documento electrónico para enviar al SRI</p></div>',
                '<i class="fa fa-file-text-o" aria-hidden="true"></i> Comprobante electrónicos', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        mensaje = "";
        if(tipo_comprobante !== "06")
            mensaje= '<div class="alert alert-info text-center"> <input type="checkbox" id="envio_correo" name="envio_correo" style="position: relative;top: 3px;" checked> <label for="envio_correo">¿Enviar Correo electrónico a cliente(s) ?</label> </div>';

        modal_quest('modal_message_facturar_envios',
            '<div class="alert alert-info text-center"><label>Se enviaran los comprobantes electrónicos seleccionados al SRI</label></div>' +
            mensaje,
            '<i class="fa fa-check" aria-hidden="true"></i> Se realizaran las siguientes acciones', true, false, '{{isPC() ? '40%' : ''}}', function () {
                cerrar_modals();
                $.LoadingOverlay("show", {
                    image: "",
                    progress: true,
                    text: "Enviando comprobante al SRI...",
                    textColor: "#fff",
                    progressColor : "#00a65a",
                    progressResizeFactor: "0.20",
                    background: "rgba(0, 0, 0, 0.75)"
                });
                var count = 0;
                var cantidad_envios = arrComprobante.length;
                var tiempo = cantidad_envios * 3000;
                var interval = setInterval(function () {
                    if(count>=20 && count<=95)
                        $.LoadingOverlay("text", "Enviando correo electrónico al cliente...");
                    if (count >= 100) {
                        clearInterval(interval);
                        return;
                    }
                    count += 100 / cantidad_envios;
                    $.LoadingOverlay("progress", count);
                }, tiempo);
                datos = {
                    _token: '{{csrf_token()}}',
                    arrComprobante: arrComprobante,
                    tipo_comprobante : tipo_comprobante,
                    envio_correo : $("#envio_correo").is(":checked")
                };
                $.get('{{url('comprobante/generar_comprobante_lote')}}', datos, function (retorno) {
                    $.LoadingOverlay("hide");
                    modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus comprobantes', true, false,
                        '{{isPC() ? '50%' : ''}}');
                    buscar_listado_comprobante();
                });
            });
    }

    function firmar_comprobante(){
        arrNoFirmados = [];
        $.each($('input:checkbox[name=firmar]:checked'), function (i, j) {
            arrNoFirmados.push(j.value);
        });

        if (arrNoFirmados.length === 0) {
            modal_view('modal_view_msg_factura',
                '<div class="alert text-center  alert-warning"><p><i class="fa fa-fw fa-exclamation-triangle"></i> Debe seleccionar al menos un comprobantes electrónico para firmar</p></div>',
                '<i class="fa fa-file-text-o" aria-hidden="true"></i> Comprobantes electrónicos', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        modal_quest('modal_message_facturar_envios',
            '<div class="alert alert-warning text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Esta seguro que desea firmar los comprobantes electrónicos seleccionadas?</div>',
            '<i class="fa fa-check" aria-hidden="true"></i> Firmar comprobantes electrónicos', true, false, '{{isPC() ? '40%' : ''}}', function () {
                cerrar_modals();
                $.LoadingOverlay("show", {
                    image: "",
                    progress: true,
                    text: "Firmando comprobante...",
                    textColor: "#fff",
                    progressColor : "#00a65a",
                    progressResizeFactor: "0.20",
                    background: "rgba(0, 0, 0, 0.75)"
                });
                var count = 0;
                var cantidad_firmas = arrNoFirmados.length;
                var tiempo = cantidad_firmas * 4000;
                var interval = setInterval(function () {
                    if(count>=40 && count<=95)
                        $.LoadingOverlay("text", "Comprobante firmado...");
                    if (count >= 100) {
                        clearInterval(interval);
                        return;
                    }
                    count += 100 / cantidad_firmas;
                    $.LoadingOverlay("progress", count);
                }, tiempo);
                datos = {
                    _token: '{{csrf_token()}}',
                    arrNoFirmados: arrNoFirmados
                };
                $.get('{{url('comprobante/firmar_comprobante')}}', datos, function (retorno) {
                    $.LoadingOverlay("hide");
                    modal_view('modal_view_msg_comprobante', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus comprobante', true, false,
                        '{{isPC() ? '50%' : ''}}');
                    buscar_listado_comprobante();
                });
            });
    }

    function reenviar_correo(comprobante) {
        $.LoadingOverlay('show');
        datos = {
            comprobante : comprobante
        };
        $.get('{{url('comprobante/reenviar_correo')}}', datos, function (retorno) {
            modal_view('modal_view_email', retorno, '<i class="fa fa-envelope-o" aria-hidden="true"></i> Reenvio de mail', true, false,
                '{{isPC() ? '50%' : ''}}');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function crear_guia_remision(id_comprobante){
        html = "<div class='row'>" +
                "<div class='col-md-12'>" +
                    "<form id='form_guia_ruta' name='form_guia_ruta'>" +
                    "<p><label for='ruta'>Escriba la ruta par la guía de remisión</label></p>" +
                        "<div class='row'>" +
                            "<div class='col-md-12'>" +
                                "<input type='text' id='ruta' name='ruta' class='form-control' required> "+
                            "</div>"+
                        "</div>" +
                    "</form>" +
                    "</div>"+
                "</div>";

        modal_quest('modal_crear_guia_remision', html, "<i class='fa fa-road' ></i> Ruta",true, false, '{{isPC() ? '25%' : ''}}', function () {
            if($("#form_guia_ruta").valid()){
                $.LoadingOverlay('show');

                datos = {
                    _token: '{{csrf_token()}}',
                    id_comprobante: id_comprobante,
                    ruta : $("#ruta").val()
                };
                post_jquery('comprobante/generar_comprobante_guia_remision', datos, function () {
                    cerrar_modals();
                    buscar_listado_comprobante();
                });
                $.LoadingOverlay('hide');
            }
        });
    }
</script>
