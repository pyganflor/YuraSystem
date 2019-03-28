<script>

    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_agencias_transporte').val().trim(),
        };
        $.get('{{url('agencias_transporte/list')}}', datos, function (retorno) {
            $('#div_listado_agencia_transporte').html(retorno);
            estructura_tabla('table_content_agencias_transporte');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_agencia_transporte .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=' + $('#busqueda_agencias_transporte').val() + '&');
        $('#div_listado_agencia_transporte').html($('#table_agencia_transporte').html());
        $.get(url, function (resul) {
            $('#div_listado_agencia_transporte').html(resul);
            estructura_tabla('table_content_agencias_transporte');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function create_agencia_transporte(id_agencia_transporte) {
        $.LoadingOverlay('show');
        datos={
            id_agencia_transporte: id_agencia_transporte
        };
        $.get('{{url('agencias_transporte/create')}}',datos ,function (retorno) {
            modal_form('modal_add_agencia_transporte', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Agencia de transporte', true, false, '{{isPC() ? '50%' : ''}}', function () {
                store_agencia_transporte();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_agencia_transporte() {
        if ($('#form_add_agencia_transporte').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token             : '{{csrf_token()}}',
                nombre             : $("#nombre_agencia").val(),
                agencia_transporte : $("#agencia_transporte").val(),
                id_agencia_transporte: $("#id_agencia_transporte").val(),
                codigo              : $("#codigo").val()
            };

            post_jquery('{{url('agencias_transporte/store')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function actualizar_agencia_transporte(id_agencia_transporte, estado_agencia_transporte) {
        mensaje = {
            title: estado_agencia_transporte == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar agencia de transporte' : '<i class="fa fa-fw fa-unlock"></i> Activar agencia de transporte',
            mensaje: estado_agencia_transporte == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar esta agencia de transporte?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar esta agencia de transporte?</div>',
        };
        modal_quest('modal_actualizar_estado_agencia_transporte', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_agencia_transporte: id_agencia_transporte
            };
            $.LoadingOverlay('show');
            $.post('{{url('agencias_transporte/update')}}', datos, function (retorno) {
                if (retorno.success) {
                    if (retorno.estado) {
                        $('#row_agencia_' + id_agencia_transporte).removeClass('error');
                        $('#btn_usuarios_' + id_agencia_transporte).removeClass('btn-danger');
                        $('#boton_agencia_transporte_' + id_agencia_transporte).addClass('btn-success');
                        $('#boton_agencia_transporte_' + id_agencia_transporte).prop('title', 'Desactivar');
                        $('#icon_agencia_transporte_' + id_agencia_transporte).removeClass('fa-unlock');
                        $('#icon_agencia_transporte_' + id_agencia_transporte).addClass('fa-trash');
                    } else {
                        $('#row_agencia_' + id_agencia_transporte).addClass('error');
                        $('#boton_agencia_transporte_' + id_agencia_transporte).removeClass('btn-success');
                        $('#boton_agencia_transporte_' + id_agencia_transporte).addClass('btn-danger');
                        $('#boton_agencia_transporte_' + id_agencia_transporte).prop('title', 'Activar');
                        $('#icon_agencia_transporte_' + id_agencia_transporte).removeClass('fa-trash');
                        $('#icon_agencia_transporte_' + id_agencia_transporte).addClass('fa-unlock');
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

    function exportar_agencia_transporte() {
        $.LoadingOverlay('show');
        window.open('{{url('agencias_transporte/excel')}}' + '?busqueda=' + $('#busqueda_agencias_transporte').val().trim(), '_blank');
        $.LoadingOverlay('hide');
    }

</script>
