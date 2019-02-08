<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_marcas').val().trim(),
        };
        $.get('{{url('marcas/buscar')}}', datos, function (retorno) {
            $('#div_listado_marcas').html(retorno);
            estructura_tabla('table_content_marcas');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_marca(id_marca) {
        datos = {
            id_marca : id_marca
        };
        $.LoadingOverlay('show');
        $.get('{{url('marcas/add')}}', datos, function (retorno) {
            modal_form('modal_add_marca', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir marca', true, false, '{{isPC() ? '60%' : ''}}', function () {
                store_marca();
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_marca() {

        if ($('#form_add_cliente').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token     : '{{csrf_token()}}',
                marca      : $('#marca').val(),
                descripcion: $('#descripcion').val(),
                id_marca   : $('#id_marca').val()
            };
            post_jquery('{{url('marcas/store')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    $(document).on("click", "#pagination_listado_marcas .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=&' + $('#busqueda_marcas').val().trim());
        $('#div_listado_marcas').html($('#table_marcas').html());
        $.get(url, function (resul) {
            console.log(resul);
            $('#div_listado_marcas').html(resul);
            estructura_tabla('table_content_marcas');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });
</script>
