<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_clientes').val().trim(),
        };
        $.get('{{url('clientes/buscar')}}', datos, function (retorno) {
            $('#div_listado_clientes').html(retorno);
            estructura_tabla('table_content_clientes');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_clientes .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=' + $('#busqueda_clientes').val() + '&');
        $('#div_listado_clientes').html($('#table_clientes').html());
        $.get(url, function (resul) {
            $('#div_listado_clientes').html(resul);
            estructura_tabla('table_content_clientes');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function exportar_clientes() {
        $.LoadingOverlay('show');
        window.open('{{url('clientes/exportar')}}' + '?busqueda=' + $('#busqueda_clientes').val().trim(), '_blank');
        $.LoadingOverlay('hide');
    }

    function eliminar_cliente(id, estado) {
        mensaje = {
            title: estado == 'A' ? '<i class="fa fa-fw fa-trash"></i> Desactivar cliente' : '<i class="fa fa-fw fa-unlock"></i> Activar cliente',
            mensaje: estado == 'A' ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este cliente?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este cliente?</div>',
        };
        modal_quest('modal_delete_cliente', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_cliente: id
            };
            $.LoadingOverlay('show');
            $.post('{{url('clientes/eliminar')}}', datos, function (retorno) {
                if (retorno.success) {
                    if (retorno.estado) {
                        $('#row_clientes_' + id).removeClass('error');
                        $('#btn_clientes_' + id).removeClass('btn-danger');
                        $('#btn_clientes_' + id).addClass('btn-success');
                        $('#btn_clientes_' + id).prop('title', 'Desactivar');
                        $('#icon_clientes_' + id).removeClass('fa-unlock');
                        $('#icon_clientes_' + id).addClass('fa-trash');
                    } else {
                        $('#row_clientes_' + id).addClass('error');
                        $('#btn_clientes_' + id).removeClass('btn-success');
                        $('#btn_clientes_' + id).addClass('btn-danger');
                        $('#btn_clientes_' + id).prop('title', 'Activar');
                        $('#icon_clientes_' + id).removeClass('fa-trash');
                        $('#icon_clientes_' + id).addClass('fa-unlock');
                    }
                    for (i = 0; i < arreglo_modals_form.length; i++) {
                        arreglo_modals_form[i].close();
                    }
                    arreglo_modals_form = [];
                    location.reload();
                } else {
                    alerta(retorno.mensaje);
                }
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta(retorno.responseText);
                alerta('Ha ocurrido un problema al cambiar el estado del cliente');
            }).always(function () {
                $.LoadingOverlay('hide');
            })
        });
    }

    function add_cliente(id_cliente) {
        $.LoadingOverlay('show');
        datos = {
            id_cliente: id_cliente
        };
        $.get('{{url('clientes/add')}}', datos, function (retorno) {
            modal_view('modal_admin_especificaciones', retorno, '<i class="fa fa-user-plus" aria-hidden="true"></i> Añadir cliente', true, false,'{{isPC() ? '90%' : ''}}');
            //modal_view('modal_add_cliente', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir cliente', true, false, '{{isPC() ? '60%' : ''}}', function () {
                //store_cliente();
               // $.LoadingOverlay('hide');
            //});
        });
        $.LoadingOverlay('hide');
    }

    function store_cliente() {
        if ($('#form_add_cliente').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_cliente: $('#id_cliente').val(),
                nombre: $('#nombre').val(),
                identificacion: $('#identificacion').val(),
                pais: $("#pais").val(),
                provincia: $("#provincia").val(),
                correo: $("#correo").val(),
                telefono: $("#telefono").val(),
                direccion: $("#direccion").val(),
                codigo_impuesto: $("#codigo_impuesto").val(),
                tipo_identificacion : $('#tipo_identificacion').val(),
                tipo_impuesto : $('#tipo_impuesto').val(),
                almacen : $('#almacen').val(),
                puerto_entrada : $("#puerto_entrada").val(),
                tipo_credito : $("#tipo_credito").val(),
                marca : $("#marca").val(),
                factura_cliente : $("#factura_cliente").is(":checked"),
                csv_etiqueta : $("#csv_etiqueta").is(":checked"),
                packing_list : $("#packing_list").is(":checked"),
                dist_cajas : $("#dist_cajas").is(":checked"),
                factura_sri : $("#factura_sri").is(":checked")
            };
            post_jquery('{{url('clientes/store')}}', datos, function () {
                cerrar_modals();
                detalles_cliente($('#id_cliente').val());
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    /* ============= ESPECIFICACIONES =====================*/
    function admin_especificaciones(id_cliente) {
        datos = {
            id_cliente: id_cliente
        };
        get_jquery('{{url('clientes/admin_especificaciones')}}', datos, function (retorno) {
            modal_view('modal_admin_especificaciones', retorno, '<i class="fa fa-fw fa-gift"></i> Especificaciones', true, false,'{{isPC() ? '90%' : ''}}');
        });
    }

    /* ============= FUNCION PARA AÑADIR DOCUMENTO =================*/
    function add_info(codigo, id_cliente) {
        add_documento('detalle_cliente', codigo, function () {
            detalles_cliente(id_cliente);
        });
    }

    function delete_inputs(cant) {
        var tr = $("tr#tr_select_agencias_carga_" + cant);
        tr.remove(tr.cant);
    }

    function store_agencias(id_cliente) {
        if ($('#form_add_user_agencia_carga').valid()) {
            $.LoadingOverlay('show');
            var cant_inputs_agencias_carga = $("tbody#campos_agencia_carga tr").length;
            var arrAgenciasCarga = [];
            var contactos = [];
            $.each($("tbody#campos_agencia_carga tr"), function(i,j){
                var contactosAgenciaCarga = [];
                arrAgenciasCarga.push([
                    $('#select_agencia_carga_'+(i+1)).val(),
                    $('#id_select_agencia_carga_'+(i+1)).val(),
                    /*$("#contacto_cliente_agencia_carga_"+(i+1)).val(),
                    $("#correo_cliente_agencia_carga_"+(i+1)).val(),
                    $("#direccion_cliente_agencia_carga_"+(i+1)).val()*/
                ]);
                $.each($("div.contacto_agencia_carga_"+(i+1)+ " div.row"),function(l,m){
                    if($(m).find('input.contacto_cliente_agencia_carga').val()!= ""){
                        contactosAgenciaCarga.push({
                            contacto : $(m).find('input.contacto_cliente_agencia_carga').val(),
                            correo : $(m).find('input.correo_cliente_agencia_carga').val(),
                            direccion : $(m).find('input.direccion_cliente_agencia_carga').val()
                        });
                    }
                });
                contactos.push(contactosAgenciaCarga);
            });
            console.log(contactos);
           // return false;
            datos = {
                _token: '{{csrf_token()}}',
                data_agencias_carga: arrAgenciasCarga,
                id_cliente: id_cliente,
                contactos : contactos
            };
            post_jquery('{{url('clientes/store_agencia_carga')}}', datos, function () {
                cerrar_modals();
                detalles_cliente(id_cliente);
            });
            $.LoadingOverlay('hide');
        }
    }

    function deleteClienteAgenciaCarga(id_cliente_agencia_carga, estado, id_cliente) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_cliente_agencia_carga: id_cliente_agencia_carga,
            estado: estado,
        };
        post_jquery('{{url('clientes/delete_cliente_agencia_carga')}}', datos, function () {
            cerrar_modals();
            detalles_cliente(id_cliente);
        });
        $.LoadingOverlay('hide');
    }

    function store_contactos(id_cliente, id_detalle_cliente) {
        if ($('#form_add_user_contactos').valid()) {
            $.LoadingOverlay('show');
            var cant_inputs_contactos = $("tbody#campos_contactos tr").length;

            var arrContactos = [];

            for (var i = 0; i < cant_inputs_contactos; i++) {
                arrContactos.push(
                    [
                        $('#nombre_contacto_' + (parseInt(i) + parseInt(1))).val(),
                        $('#correo_' + (parseInt(i) + parseInt(1))).val(),
                        $('#telefono_' + (parseInt(i) + parseInt(1))).val(),
                        $('#direccion_' + (parseInt(i) + parseInt(1))).val(),
                        $('#id_inputs_contacto_' + (parseInt(i) + parseInt(1))).val()
                    ]);
            }
            datos = {
                _token: '{{csrf_token()}}',
                data_contactos: arrContactos,
                id_cliente: id_cliente,
                id_detalle_cliente: id_detalle_cliente
            };

            post_jquery('{{url('clientes/store_contactos')}}', datos, function () {
                cerrar_modals();
                detalles_cliente(id_cliente);
            });
            $.LoadingOverlay('hide');
        }

    }

    function actualizarContacto(id_contacto, est_contacto, id_cliente) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_contacto: id_contacto,
            estado: est_contacto,
        };
        post_jquery('{{url('clientes/actualizar_estado_contacto')}}', datos, function () {
            cerrar_modals();
            detalles_cliente(id_cliente);
        });
        $.LoadingOverlay('hide');
    }




</script>
