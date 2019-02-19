<script>
    buscar_listado_comprobante();

    function buscar_listado_comprobante() {
        $.LoadingOverlay('show');
        datos = {
            anno               : $('#anno').val(),
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
    
    function facturar_comprobante() {
        arrPreFacturas = [];
        $.each($('input:checkbox[name=facturar]:checked'), function (i, j) {
            arrPreFacturas.push(j.value);
        });

        if (arrPreFacturas.length === 0) {
            modal_view('modal_view_msg_factura',
                '<div class="alert text-center  alert-warning"><p><i class="fa fa-fw fa-exclamation-triangle"></i> Debe seleccionar al menos un documento electrónico para facturar</p></div>',
                '<i class="fa fa-file-text-o" aria-hidden="true"></i> Comprobante electrónicos', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        modal_quest('modal_message_facturar_envios',
            '<div class="alert alert-warning text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Esta seguro que desea facturar los comprobantes electrónicos seleccionadas?</div>',
            '<i class="fa fa-check" aria-hidden="true"></i> Facturar comprobantes electrónicos', true, false, '{{isPC() ? '40%' : ''}}', function () {
                cerrar_modals();
                $.LoadingOverlay("show", {
                    image: "",
                    progress: true,
                    text: "Facturando...",
                    textColor: "#fff",
                    progressColor : "#00a65a",
                    progressResizeFactor: "0.20",
                    background: "rgba(0, 0, 0, 0.75)"
                });
                var count = 0;
                var cantidad_envios = arrPreFacturas.length;
                var tiempo = cantidad_envios * 4000;
                var interval = setInterval(function () {
                    if(count>=15 && count<25)
                        $.LoadingOverlay("text", "Enviando datos al SRI...");
                    if(count>=40 && count<=95)
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
                    arrPreFacturas: arrPreFacturas
                };
                $.get('{{url('comprobante/generar_comprobante_lote')}}', datos, function (retorno) {
                    $.LoadingOverlay("hide");
                    modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus facturas', true, false,
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
                    modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus facturas', true, false,
                        '{{isPC() ? '50%' : ''}}');
                    buscar_listado_comprobante();
                });
            });
    }
</script>
