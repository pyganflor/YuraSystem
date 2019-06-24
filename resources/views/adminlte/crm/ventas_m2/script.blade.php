<script>
    filtrar_m2();
    filtrar_m2_anno();

    function filtrar_m2() {
        datos = {
            variedad: $('#filtro_predeterminado_variedad_m2').val()
        };
        get_jquery('{{url('ventas_m2/chart_m2')}}', datos, function (retorno) {
            $('#div_chart_ventas_m2').html(retorno);
        });
    }

    function filtrar_m2_anno() {
        datos = {
            variedad: $('#filtro_predeterminado_variedad_m2_anno').val()
        };
        get_jquery('{{url('ventas_m2/chart_m2_anno')}}', datos, function (retorno) {
            $('#div_chart_ventas_m2_anno').html(retorno);
        });
    }
</script>