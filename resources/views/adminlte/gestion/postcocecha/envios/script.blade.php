<script>
    buscar_listado_envios();

    function buscar_listado_envios() {
        $.LoadingOverlay('show');
        datos = {
            anno       : $('#anno').val(),
            id_cliente : $('#id_cliente').val(),
            desde      : $('#desde').val(),
            hasta      : $('#hasta').val(),
            estado     : $('#estado').val()
        };
        $.get('{{url('envio/buscar')}}', datos, function (retorno) {
            $('#div_listado_envios').html(retorno);
            estructura_tabla('table_content_envios');
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
        arrEnvios = [];
        $.each($('input:checkbox[name=check_envio]:checked'), function (i, j) {
            arrEnvios.push([
                j.value,
                $("#descuento_"+(j.id)).val(),
                //$("#muestra_descuento_"+(i+1)).is(":checked")
            ]);
        });

        if(arrEnvios.length === 0) {
            modal_view('modal_view_msg_factura',
                '<div class="alert text-center  alert-warning"><p>Debe seleccionar al menos un envío para facturar</p></div>',
                '<i class="fa fa-fw fa-table"></i> Estatus facturas', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        var result = confirm("¿Esta seguro que facturar los envíos seleccionados?");
        if (result) {
            $.LoadingOverlay("show", {
                image       : "",
                progress    : true,
                text        : "Generando factura",
                textColor   : "#fff"
            });

            var count     = 0;
            var cantidad_envios = arrEnvios.length;
            var tiempo = cantidad_envios*2300;
            var interval  = setInterval(function(){
                if (count >= 100) {
                    clearInterval(interval);
                    return;
                }
                count += 100/cantidad_envios;
                $.LoadingOverlay("progress", count);
            }, tiempo);
            datos = {
                _token: '{{csrf_token()}}',
                arrEnvios : arrEnvios
            };
            $.get('{{url('comprobante/generar_factura_cliente')}}', datos, function (retorno) {
                $.LoadingOverlay("hide");
                modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus facturas', true, false,
                    '{{isPC() ? '50%' : ''}}');
            });
        }
    }

    function activar(input_descuento,id_check){
        var id = input_descuento.id.split("_")[1];
        $("#"+input_descuento.id,).removeAttr("readonly");
        $("#muestra_descuento_"+id).removeAttr("disabled");
    }
</script>
