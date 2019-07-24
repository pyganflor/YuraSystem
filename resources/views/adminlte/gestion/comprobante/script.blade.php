<script>
    buscar_listado_comprobante();

    function buscar_listado_comprobante() {
        $.LoadingOverlay('show');
        datos = {
            //anno               : $('#anno').val(),
            id_cliente         : $('#id_cliente').val(),
            codigo_comprobante : $("#codigo_comprobante").val(),
            desde              : $('#desde').val(),
            hasta              : $('#hasta').val(),
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

    function enviar_correo(id_comprobante,tipo_pedido,tipo_comprobante){
        if(tipo_comprobante === "01"){
            if(tipo_pedido === "N"){
                check = "<div class='col-md-4'>" +
                    "<input type='checkbox' id='csv_etiqueta' name='csv_etiqueta' checked style='position:relative;top:3px'> "+
                    "<label style='font-weight:600' for='csv_etiqueta'>CSV de etiquetas</label>" +
                    "</div>";
            }else if(tipo_pedido === "T"){
                check = "<div class='col-md-4'>" +
                    "<input type='checkbox' id='dist_cajas' name='dist_cajas' style='position:relative;top:3px'> "+
                    "<label style='font-weight:600' for='dist_cajas'>Lista de distribución</label>" +
                    "</div>";
            }
            html = "<div class='row'>" +
                "<div class='col-md-12'>" +
                "<form id='form_envio_correo' name='form_envio_correo'>" +
                "<p><label for='ruta'>Seleccione las opciones para el envio del correo</label></p>" +
                "<p style='margin:10px 0px 0px'><label>Enviar a:</label></p>" +
                "<div class='row'>" +
                "<div class='col-md-4'>" +
                "<input type='checkbox' id='cliente' name='cliente' checked style='position:relative;top:3px'> "+
                "<label style='font-weight:600' for='cliente'>Cliente</label>" +
                "</div>"+
                "<div class='col-md-8'>" +
                "<input type='checkbox' id='agencia_carga' name='agencia_carga' style='position:relative;top:3px'> "+
                "<label style='font-weight:600' for='agencia_carga'>Agencia de carga</label>" +
                "</div>"+
                "</div>" +
                "<p style='margin:10px 0px 0px;'><label>Adjuntar:</label></p>" +
                "<div class='row'>" +
                "<div class='col-md-4'>" +
                "<input type='checkbox' id='factura_cliente' name='factura_cliente' checked style='position:relative;top:3px'> "+
                "<label style='font-weight:600' for='factura_cliente'>Factura del cliente</label>" +
                "</div>"+
                check
                +
                "<div class='col-md-4'>" +
                "<input type='checkbox' id='factura_sri' name='factura_sri' style='position:relative;top:3px'> "+
                "<label style='font-weight:600' for='factura_sri'>Factura del SRI</label>" +
                "</div>"+
                "</div>" +
                "</form>" +
                "</div>"+
                "</div>";

        }else if(tipo_comprobante === "06"){
            html = "<div class='row'>" +
                "<div class='col-md-12'>" +
                "<form id='form_envio_correo' name='form_envio_correo'>" +
                "<p><label for='ruta'>Seleccione las opciones para el envio del correo</label></p>" +
                "<p style='margin:10px 0px 0px'><label>Enviar a:</label></p>" +
                "<div class='row'>" +
                "<div class='col-md-4'>" +
                "<input type='checkbox' id='cliente' name='cliente' checked style='position:relative;top:3px'> "+
                "<label style='font-weight:600' for='cliente'>Cliente</label>" +
                "</div>"+
                "<div class='col-md-8'>" +
                "<input type='checkbox' id='agencia_carga' name='agencia_carga' style='position:relative;top:3px'> "+
                "<label style='font-weight:600' for='agencia_carga'>Agencia de carga</label>" +
                "</div>"+
                "</div>" +
                "<p style='margin:10px 0px 0px;'><label>Adjuntar:</label></p>" +
                "<div class='row'>" +
                "<div class='col-md-12'>" +
                "<input type='checkbox' id='guia_remision' name='guia_remision' checked style='position:relative;top:3px'> "+
                "<label style='font-weight:600' for='guía de remisión'>Guía de remisión</label>" +
                "</div>"+
                "</div>" +
                "</form>" +
                "</div>"+
                "</div>";
        }




        modal_quest('modal_enviar_correo', html, "<i class='fa fa-envelope-o' ></i> Envio de correos",true, false, '{{isPC() ? '50%' : ''}}', function () {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_comprobante : id_comprobante,
                cliente : $("#cliente").is(':checked'),
                agencia_carga : $("#agencia_carga").is(':checked'),
                factura_cliente : $("#factura_cliente").is(':checked'),
                factura_sri : $("#factura_sri").is(':checked'),
                csv_etiqueta : $("#csv_etiqueta").is(':checked'),
                dist_cajas : $("#dist_cajas").is(':checked'),
                guia_remision : $("#guia_remision").is(':checked')
            };
            post_jquery('comprobante/enviar_correo', datos, function () {

            });
            cerrar_modals();
            $.LoadingOverlay('hide');
        });
    }

    function crear_guia_remision(id_comprobante){
        html = "<div class='row'>" +
                    "<div class='col-md-12'>" +
                        "<form id='form_guia_ruta' name='form_guia_ruta'>" +
                            "<p><label for='ruta'>Escriba la ruta para la guía de remisión</label></p>" +
                                "<div class='row'>" +
                                    "<div class='col-md-12'>" +
                                        "<input type='text' id='ruta' name='ruta' class='form-control' value='TABABELA' required> "+
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

    function crear_guia_remision_factura(id_comprobante){
        html = "<div class='row'>" +
                    "<div class='col-md-12'>" +
                        "<form id='form_guia_ruta' name='form_guia_ruta'>" +
                            "<p><label for='ruta'>Escriba la ruta para la guía de remisión</label></p>" +
                            "<div class='row'>" +
                                "<div class='col-md-12'>" +
                                    "<input type='text' id='ruta' name='ruta' class='form-control' value='TABABELA' required> "+
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
                post_jquery('comprobante/generar_comprobante_guia_remision_factura', datos, function () {
                    cerrar_modals();
                    buscar_listado_comprobante();
                });
                $.LoadingOverlay('hide');
            }
        });
    }

    $(document).on("click", "#pagination_listado_comprobante li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?codigo_comprobante=' + $('#codigo_comprobante').val().trim()+
            '&anno=' + $('#anno').val().trim()+
            '&id_cliente=' + $('#id_cliente').val().trim()+
            '&estado=' + $('#estado').val().trim()+
            '&fecha=' + $('#fecha').val().trim() + '&');
        $('#div_listado_comprobante').html($('#table_comprobante').html());
        $.get(url, function (resul) {
            console.log(resul);
            $('#div_listado_comprobante').html(resul);
            estructura_tabla('table_content_comprobante');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function integrar_factura_venture(id_comprobante) {

        html = "<div class='row'>" +
                "<div class='col-md-12'>" +
                    "<form id='form_carga_xml' name='form_carga_xml'>" +
                        "<p><label class='alert alert-info' style='width: 100%; margin: 0;'>Escoja la fecha con la que desea integrar la factura</label></p>" +
                        "<div class='row'>" +
                            "<div class='col-md-12'>" +
                                "<input type='date' id='fecha_integrado' name='fecha_integrado' class='form-control' value='{{now()->format('Y-m-d')}}' required> "+
                            "</div>"+
                        "</div>" +
                    "</form>" +
                "</div>"+
               "</div>";
        datos = {
            id_comprobante : id_comprobante,
            fecha_integrado : $("#fecha_integrado").val(),
            _token : '{{csrf_token()}}'
        };
        console.log(datos);
        modal_form('modal_fecha_integrado', html, '<i class="fa fa-calendar"></i> Fecha de integración', true, false, '30%', function () {
            post_jquery('comprobante/integrar_factura_venture', datos, function () {
                buscar_listado_comprobante();
            });
        });
    }

    function txt_venture(){

        modal_quest('modal_crear_especificacion',
            '<div class="alert alert-warning text-center"><p>Al integrar estas facturas no podran ser modificado el pedido ni la facturación.!</p></div>',
            "<i class='fa fa-exclamation-triangle' ></i> Alerta",true, false, '{{isPC() ? '50%' : ''}}', function () {
                $.LoadingOverlay("show");
                $.ajax({
                type: "POST",
                dataType: "html",
                contentType: "application/x-www-form-urlencoded",
                url: '{{url('comprobante/descargar_txt')}}',
                data: {
                    desde: $("#desde").val(),
                    hasta : $("#hasta").val(),
                    tipo_comprobante : $("#codigo_comprobante").val(),
                    _token: '{{csrf_token()}}',
                },
                success: function (data) {
                    var opResult = JSON.parse(data);
                    var $a = $("<a>");
                    $a.attr("href", opResult.data);
                    $("body").append($a);
                    $a.attr("download", "text_integrador_" + opResult.fecha + ".txt");
                    $a[0].click();
                    $a.remove();
                }
            }).always(function () {
                buscar_listado_comprobante();
                cerrar_modals();
                $.LoadingOverlay('hide');
            });
        });
    }

    function update_integrado(id_comprobante) {
        datos = {
            id_comprobante : id_comprobante,
            _token : '{{csrf_token()}}'
        };
        modal_quest('modal_update_integrado',
            '<div class="alert alert-warning text-center"><label>Al realizar esta acción los datos de este pedido y de esta factura podrán ser modificados</label></div>',
            '<i class="fa fa-file-text-o" aria-hidden="true"></i> Actualizar estado de la factura', true, false, '{{isPC() ? '40%' : ''}}', function () {
                post_jquery('comprobante/desvincular_factura_venture', datos, function () {

                    buscar_listado_comprobante();
                    cerrar_modals();
                });
            });
    }

    function actualizar_comprobante(){
        datos = {
            fecha: $("#fecha").val(),
            _token: '{{csrf_token()}}',
            tipo_comprobante : $("#codigo_comprobante").val()
        };
        modal_quest('modal_update_integrado',
            '<div class="alert alert-info text-center"><label>Ha cargado los archivos XML correspondientes a los comprobantes generados el día seleccionado.? </label></div>',
            '<i class="fa fa-file-text-o" aria-hidden="true"></i> Actualizar comprobantes integrados', true, false, '{{isPC() ? '50%' : ''}}', function () {
                post_jquery('comprobante/actualizar_comprobante_venture', datos, function () {
                    buscar_listado_comprobante();
                    cerrar_modals();
                });
            });
    }

    function anular_factura(id_comprobante){
        datos = {
            id_comprobante : id_comprobante,
            _token : '{{csrf_token()}}'
        };
        modal_quest('modal_update_integrado',
            '<div class="alert alert-warning text-center"><label>Esta seguro que desea anular esta factura?</label></div>',
            '<i class="fa fa-file-text-o" aria-hidden="true"></i> Actualizar estado de la factura', true, false, '{{isPC() ? '40%' : ''}}', function () {
                post_jquery('comprobante/anular_factura', datos, function () {
                    buscar_listado_comprobante();
                    cerrar_modals();
                });
            });
    }

    function subir_archivos_xml(){
        if($("#codigo_comprobante").val() === "01")
            carpeta = "facturas";
        if($("#codigo_comprobante").val() === "06")
            carpeta = "guias";

        html = "<div class='row'>" +
                    "<div class='col-md-12'>" +
                        "<form id='form_carga_xml' name='form_carga_xml'>" +
                            "<p><label class='alert alert-warning' style='width: 100%; margin: 0;'>Estos archivos se cargaran en la carpeta de "+carpeta+", es correcto?</label></p>" +
                                "<div class='row'>" +
                                    "<div class='col-md-12'>" +
                                        "<input type='file' id='archivos' accept='text/xml'name='archivos[]' class='form-control' multiple required> "+
                                    "</div>"+
                            "</div>" +
                        "</form>" +
                    "</div>"+
                "</div>";

        modal_quest('modal_update_integrado',
            html,
            '<i class="fa fa-cloud-upload"></i> Carga de archivos', true, false, '{{isPC() ? '50%' : ''}}', function () {
                $.LoadingOverlay('show');
                var formData = new FormData($("#form_carga_xml")[0]);
                formData.append('tipo_comprobante', $("#codigo_comprobante").val());
                formData.append('_token', '{{csrf_token()}}');
                $.ajax({
                    url: '{{url('comprobante/carga_xml')}}',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (retorno) {
                        console.log(retorno);
                        modal_view('modal_status_carga_xml', retorno.mensaje, '<i class="fa fa-exclamation-triangle"></i> Estado de la carga de archivos', true, false, '60%');
                    },
                    error: function (retorno) {
                        alerta_errores(retorno.responseText);
                        alerta('Hubo un problema en la envío de la información');
                    }
                }).always(function () {
                    cerrar_modals();
                    $.LoadingOverlay('hide');
                });
            });

    }
</script>
