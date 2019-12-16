<script>

    listar_proyecciones_resumen_total();

    function listar_proyecciones_resumen_total(){
        data= {
            desde: $(".desde").val(),
            hasta: $(".hasta").val(),
        };
        get_jquery('{{url('proy_resumen_total/listar_resumen_total')}}', data, function (retorno) {
            $('#listado_proyecciones_resumen_total').html(retorno);
        });
    }
</script>
