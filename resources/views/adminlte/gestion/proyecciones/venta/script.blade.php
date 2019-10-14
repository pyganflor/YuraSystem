<script>
    select_planta($('#filtro_predeterminado_planta').val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades' );

    function listar_proyecciones_venta_semanal(){
        data= {
            id_cliente: $("#id_cliente").val(),
            id_planta: $(".planta").val(),
            id_variedad: $(".variedad").val(),
            desde: $(".desde").val(),
            hasta: $(".hasta").val()
        };
        console.log(data);
        get_jquery('{{url('proy_venta_semanal/listar_proyeccion_venta_semanal')}}', data, function (retorno) {
            $('#listado_proyecciones_venta_semanal').html(retorno);
        });
    }
    setTimeout(function(){
        listar_proyecciones_venta_semanal();
    },1000);


</script>
