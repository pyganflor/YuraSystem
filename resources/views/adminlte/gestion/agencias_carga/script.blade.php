<script>

    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_agencias_carga').val().trim(),
        };
        $.get('{{route('list.agencias_carga')}}', datos, function (retorno) {
            $('#div_listado_agencia_carga').html(retorno);
            estructura_tabla('table_content_agencias_carga');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_recepciones .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=' + $('#busqueda_agencias_carga').val() + '&');
        $('#div_listado_clientes').html($('#table_agencia_carga').html());
        $.get(url, function (resul) {
            $('#div_listado_agencia_carga').html(resul);
            estructura_tabla('table_content_agencias_carga');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function actualizar_agencia_carga(id_agencia_carga, estado_agencia_carga) {
        mensaje = {
            title: estado_agencia_carga == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar agencia de carga' : '<i class="fa fa-fw fa-unlock"></i> Activar agencia de carga',
            mensaje: estado_agencia_carga == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar esta agencia de carga?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar esta agencia de carga?</div>',
        };
        modal_quest('modal_actualizar_estado_agencia_carga', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_agencia_carga: id_agencia_carga
            };
            $.LoadingOverlay('show');
            $.post('{{route('update_estado.agencias_carga')}}', datos, function (retorno) {
                if (retorno.success) {
                    if (retorno.estado) {
                        $('#row_agencia_' + id_agencia_carga).removeClass('error');
                        $('#btn_usuarios_' + id_agencia_carga).removeClass('btn-danger');
                        $('#boton_agencia_carga_' + id_agencia_carga).addClass('btn-success');
                        $('#boton_agencia_carga_' + id_agencia_carga).prop('title', 'Desactivar');
                        $('#icon_agencia_carga_' + id_agencia_carga).removeClass('fa-unlock');
                        $('#icon_agencia_carga_' + id_agencia_carga).addClass('fa-trash');
                    } else {
                        $('#row_agencia_' + id_agencia_carga).addClass('error');
                        $('#boton_agencia_carga_' + id_agencia_carga).removeClass('btn-success');
                        $('#boton_agencia_carga_' + id_agencia_carga).addClass('btn-danger');
                        $('#boton_agencia_carga_' + id_agencia_carga).prop('title', 'Activar');
                        $('#icon_agencia_carga_' + id_agencia_carga).removeClass('fa-trash');
                        $('#icon_agencia_carga_' + id_agencia_carga).addClass('fa-unlock');
                    }
                    cerrar_modals();
                    buscar_listado();
                } else {
                    alerta(retorno.mensaje);
                }
            }, 'json').fail(function (retorno) {
                alerta(retorno.responseText);
                alerta('Ha ocurrido un problema al cambiar el estado de la agencia de carga');
            }).always(function () {
                $.LoadingOverlay('hide');
            })
        });
    }

    function exportar_agencia_carga() {
        $.LoadingOverlay('show');
        window.open('{{route('excel.agencias_carga')}}' + '?busqueda=' + $('#busqueda_agencias_carga').val().trim(), '_blank');
        $.LoadingOverlay('hide');
    }

</script>
