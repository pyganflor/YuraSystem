<script>
    buscar_listado_transportista();

    function buscar_listado_transportista() {
        $.LoadingOverlay('show');
        datos = {
            id_transportista : $('#id_transportista').val(),
            nombre      : $('#busqueda_transportista').val(),
            estado     : $('#estado').val(),
        };
        $.get('{{url('transportista/list')}}', datos, function (retorno) {
            $('#div_listado_transportista').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_transportista .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?anno=' + $('#anno').val() +
            '&estado=' + $('#estado').val() +
            '&desde=' + $('#desde').val() + '&'+
            '&desde=' + $('#hasta').val() + '&'+
            '&id_transportista=' + $('#id_transportista').val() + '&');
        $('#div_listado_transportista').html($('#table_transportista').html());
        $.get(url, function (resul) {
            $('#div_listado_transportista').html(resul);
            estructura_tabla('table_content_transportista');
            calcular_precio_envio();
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function add_transportista(id_transportista){
        datos = {
            id_transportista : id_transportista
        };
        $.LoadingOverlay('show');
        $.get('{{url('transportista/add')}}', datos, function (retorno) {
            modal_form('modal_add_transportista', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir transportista', true, false, '{{isPC() ? '60%' : ''}}', function () {
                store_transportista();
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_transportista() {
        if ($('#form_add_transportista').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token    : '{{csrf_token()}}',
                nombre_empresa    : $('#nombre_empresa').val(),
                ruc       : $('#ruc').val(),
                encargado : $('#encargado').val(),
                ruc_encargado : $('#ruc_encargado').val(),
                telefono_encargado : $('#telefono_encargado').val(),
                direccion_empresa : $('#direccion_empresa').val()
            };
            post_jquery('{{url('transportista/store')}}', datos, function () {
                cerrar_modals();
                buscar_listado_transportista();
            });
            $.LoadingOverlay('hide');
        }
    }
</script>
