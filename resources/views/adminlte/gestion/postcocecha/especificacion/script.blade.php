<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda    : $('#busqueda_especifiaciones').val().trim(),
            id_cliente  : $("#id_cliente").val(),
            tipo        : $("#tipo").val(),
            estado      : $("#estado").val()
        };
        $.get('{{url('especificacion/listado')}}', datos, function (retorno) {
            $('#div_listado_especificaciones').html(retorno);
            estructura_tabla('table_content_especificaciones');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

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
            modal_form('modal_asignar_especificacion', retorno, '<i class="fa fa-fw fa-plus"></i> <b>Especificación</b>:    '+nombre_especificacion+' ', true, false, '{{isPC() ? '50%' : ''}}', function () {
                store_asignacion();
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
       // return false;
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

    function verificar_pedido_especificacion(){

    }
</script>
