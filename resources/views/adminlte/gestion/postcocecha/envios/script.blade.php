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
</script>
