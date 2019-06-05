<script>
    listado_etiquetas();

    function listado_etiquetas(){
        $.LoadingOverlay('show');
        datos = {
            desde: $('#desde').val(),
            hasta: $('#hasta').val(),
        };
        $.get('{{url('etiqueta/listado')}}', datos, function (retorno) {
            $('#div_listado_etiquetas').html(retorno);
            //estructura_tabla('table_content_etiquetas');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

</script>
