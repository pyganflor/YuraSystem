<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
       /* datos = {
            busqueda: $('#busqueda_marcas').val().trim(),
        };*/
        $.get('{{url('precio/buscar')}}', {}/*datos*/, function (retorno) {
            $('#div_content_precio').html(retorno);
            estructura_tabla('table_content_marcas');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>
