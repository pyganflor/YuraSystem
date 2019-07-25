<script>

    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_aerolinea').val().trim(),
        };
        $.get('{{url('aerolinea/list')}}', datos, function (retorno) {
            $('#div_listado_aerolinea').html(retorno);
            estructura_tabla('table_content_aerolinea');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_aerolinea .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=' + $('#busqueda_aerolinea').val() + '&');
        $('#div_listado_aerolinea').html($('#table_aerolinea').html());
        $.get(url, function (resul) {
            $('#div_listado_aerolinea').html(resul);
            estructura_tabla('table_content_aerolinea');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function create_aerolinea(id_aerolinea) {
        $.LoadingOverlay('show');
        datos={
            id_aerolinea: id_aerolinea
        };
        $.get('{{url('aerolinea/create')}}',datos ,function (retorno) {
            modal_form('modal_add_aerolinea', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Agencia de transporte', true, false, '{{isPC() ? '50%' : ''}}', function () {
                store_aerolinea();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_aerolinea() {
        if ($('#form_add_aerolinea').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token             : '{{csrf_token()}}',
                nombre             : $("#nombre").val(),
                agencia_transporte : $("#agencia_transporte").val(),
                id_aerolinea       : $("#id_aerolinea").val(),
                codigo             : $("#codigo").val()
            };

            post_jquery('{{url('aerolinea/store')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function actualizar_aerolinea(id_aerolinea, estado) {
        mensaje = {
            title: estado == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar aerolínea' : '<i class="fa fa-fw fa-unlock"></i> Activar aerolínea',
            mensaje: estado == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar esta aerolínea?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar esta aerolínea?</div>',
        };
        modal_quest('modal_actualizar_estado_aerolinea', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_aerolinea: id_aerolinea
            };
            $.LoadingOverlay('show');
            $.post('{{url('aerolinea/update')}}', datos, function (retorno) {
                if (retorno.success) {
                    if (retorno.estado) {
                        $('#row_agencia_' + id_aerolinea).removeClass('error');
                        $('#btn_usuarios_' + id_aerolinea).removeClass('btn-danger');
                        $('#boton_aerolinea_' + id_aerolinea).addClass('btn-success');
                        $('#boton_aerolinea_' + id_aerolinea).prop('title', 'Desactivar');
                        $('#icon_aerolinea_' + id_aerolinea).removeClass('fa-unlock');
                        $('#icon_aerolinea_' + id_aerolinea).addClass('fa-trash');
                    } else {
                        $('#row_agencia_' + id_aerolinea).addClass('error');
                        $('#boton_aerolinea_' + id_aerolinea).removeClass('btn-success');
                        $('#boton_aerolinea_' + id_aerolinea).addClass('btn-danger');
                        $('#boton_aerolinea_' + id_aerolinea).prop('title', 'Activar');
                        $('#icon_aerolinea_' + id_aerolinea).removeClass('fa-trash');
                        $('#icon_aerolinea_' + id_aerolinea).addClass('fa-unlock');
                    }
                    cerrar_modals();
                    buscar_listado();
                } else {
                    alerta(retorno.mensaje);
                }
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta(retorno.responseText);
                alerta('Ha ocurrido un problema al cambiar el estado de la agencia de carga');
            }).always(function () {
                $.LoadingOverlay('hide');
            })
        });
    }

    function exportar_aerolinea() {
        $.LoadingOverlay('show');
        window.open('{{url('aerolinea/excel')}}' + '?busqueda=' + $('#busqueda_aerolinea').val().trim(), '_blank');
        $.LoadingOverlay('hide');
    }

</script>
