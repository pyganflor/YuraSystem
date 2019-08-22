<script>
    listado_pedido_factura_generada();

    function listado_pedido_factura_generada(){
        $.LoadingOverlay('show');
        datos = {
            id_configuracion_empresa : $("#id_configuracion_empresa_orden_factura").val(),
            fecha : $("#fecha").val()
        };
        $.get('{{url('orden_factura/buscar_pedido_facturada_generada')}}', datos, function (retorno) {
            $('#div_content_orden_factura').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    listado_pedido_factura_generada

</script>
