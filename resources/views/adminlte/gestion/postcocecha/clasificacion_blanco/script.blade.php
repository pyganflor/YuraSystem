<script>
    function buscar_stock() {
        $.LoadingOverlay('show');
        datos = {
            variedad: $('#variedad_search').val(),
            unitaria: $('#unitaria_search').val(),
        };
        $.get('{{url('clasificacion_blanco/buscar_stock')}}', datos, function (retorno) {
            $('#div_listado_aperturas').html(retorno);
            //estructura_tabla('table_content_aperturas');
            buscar_pedidos();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function buscar_pedidos() {
        datos = {
            fecha_pedidos: $('#fecha_pedidos').val(),
        };
        get_jquery('{{url('clasificacion_blanco/buscar_pedidos')}}', datos, function (retorno) {
            $('#div_listado_pedidos').html(retorno);
            $('#btn_sacar').hide();
        });
    }
</script>