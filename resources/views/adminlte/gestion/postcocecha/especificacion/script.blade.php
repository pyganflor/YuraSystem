<script>
    buscar_listado_especificaciones();

    $(document).on("click", "#pagination_listado_especificaciones .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=&' + $('#busqueda_especifiaciones').val().trim()+
        '&id_cliente=' + $('#id_cliente').val() +
        '&tipo=' + $('#tipo').val() +
        '&estado=' + $('#estado').val() + '&');
        $('#div_listado_especificaciones').html($('#table_especificaciones').html());
        $.get(url, function (resul) {
            $('#div_listado_especificaciones').html(resul);
            estructura_tabla('table_content_especificaciones');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function asignar_especificacicon(id_especificacion,nombre_especificacion){
        $.LoadingOverlay('show');
        datos = {
            id_especificacion : id_especificacion
        };
        $.get('{{url('especificacion/form_asignacion_especificacion')}}', datos, function (retorno) {
            modal_view('modal_asignar_especificacion', retorno, '<i class="fa fa-fw fa-plus"></i> <b>Especificación</b>:    '+nombre_especificacion+' ', true, false, '{{isPC() ? '50%' : ''}}', function () {
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_asignacion(){
       arrClientes = [];
        $.each($('input:checkbox[name=cliente]:checked'), function (i, j) {
            arrClientes.push([j.value,j.id.split("_")[1]]);
        });
        console.log(arrClientes);

        if (arrClientes.length === 0) {
            modal_view('modal_view_msg_asignacion_especificacion',
                '<div class="alert text-center  alert-warning"><p>Debe seleccionar al menos un cliente para asignar</p></div>',
                '<i class="fa fa-fw fa-table"></i> Estatus asignación', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        datos = {
            _token: '{{csrf_token()}}',
            arrClientes : arrClientes
        };
        get_jquery('{{url('especificacion/store_asignacion_especificacion')}}', datos, function (retorno) {
            modal_view('modal_view_msg_asignacion_especificacion',
                retorno,
                '<i class="fa fa-fw fa-table"></i> Estatus asignación', true, false, '{{isPC() ? '50%' : ''}}');
            //cerrar_modals();
        })

    }

    function verificar_pedido_especificacion(id_cliente,id_especificacion,input_id){
        datos = {
            id_cliente: id_cliente,
            id_especificacion: id_especificacion
        };
        if(!$("#"+input_id).is(':checked')) {
            $.get('{{url('especificacion/verificar_pedido_especificacion')}}', datos, function (retorno) {
                if(retorno > 0){
                    $("#"+input_id).prop('checked',true);
                    modal_view('modal_view_msg_asignacion_especificacion',
                        '<div class="alert text-center  alert-warning"><p>No puede ser eliminada esta especificación del cliente ya que posee pedidos realizados con la misma</p></div>',
                        '<i class="fa fa-times" aria-hidden="true"></i> Estatus asignación', true, false, '{{isPC() ? '50%' : ''}}');
                }else{
                    get_jquery('{{url('especificacion/delete_asignacion_especificacion')}}', datos, function (retorno) {
                        modal_view('modal_view_msg_delete_especificacion',
                            retorno,
                            '<i class="fa fa-check" aria-hidden="true"></i> Estatus asignación', true, false, '{{isPC() ? '50%' : ''}}');
                    });
                }
            });
        }else{
            get_jquery('{{url('especificacion/store_asignacion_especificacion')}}', datos, function (retorno) {
                modal_view('modal_view_msg_asignacion_especificacion',
                    retorno,
                    '<i class="fa fa-check" aria-hidden="true"></i> Estatus asignación', true, false, '{{isPC() ? '50%' : ''}}');
            });
        }
    }
</script>
