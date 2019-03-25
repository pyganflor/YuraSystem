<script>

    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_datos_exportacion').val().trim(),
            estado : $("#estado").val()
        };
        $.get('{{url('datos_exportacion/buscar')}}', datos, function (retorno) {
            $('#div_listado_datos_exportacion').html(retorno);
            estructura_tabla('table_content_dato_exportacion');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_dato_exportacion(id_dato_exportacion) {
        $.LoadingOverlay('show');
        datos = {
            id_dato_exportacion: id_dato_exportacion
        };
        $.get('{{url('datos_exportacion/add_dato_exportacion')}}', datos, function (retorno) {
            modal_form('modal_dato_exportacion', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir datos de exportación', true, false, '{{isPC() ? '30%' : ''}}', function () {
            store_dato_exportacion();
            });
            add_input_dato_exportacion(id_dato_exportacion);
        });
        $.LoadingOverlay('hide');
    }

    function add_input_dato_exportacion(id_dato_exportacion) {
        $.LoadingOverlay('show');
        cant_rows = $("#tbody_dato_exportacion tr").length;
        datos = {
            id_dato_exportacion: id_dato_exportacion,
            cant_rows : cant_rows
        };
        $.get('{{url('datos_exportacion/add_input_dato_exportacion')}}', datos, function (retorno) {
            $("#tbody_dato_exportacion").append(retorno);
        });
        $.LoadingOverlay('hide');
    }

    function store_dato_exportacion(){
        arrDatosExportacion = [];
        $.each($(".nombre_dato_exportacion"),function(i,j){
            arrDatosExportacion.push({
                nombre : j.value,
                id_dato_exportacion : $("#id_dato_exportacion_"+(i+1)).val()
            });
        });
        if ($('#form_add_dato_exportacion').valid()) {
            datos={
                _token:'{{ csrf_token() }}',
                arrDatosExportacion : arrDatosExportacion
            };
            post_jquery('{{url('datos_exportacion/store_datos_exportacion')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
        }
    }

    function update_estado_dato_exportacion(id_dato_exportacion, estado) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_dato_exportacion: id_dato_exportacion,
            estado: estado,
        };
        post_jquery('{{url('datos_exportacion/update_estado_datos_exportacion')}}', datos, function () {
            cerrar_modals();
            buscar_listado();
        });
        $.LoadingOverlay('hide');
    }

    function form_asignacion_dato_exportacion(id_dato_exportacion) {

        $.LoadingOverlay('show');
        datos = {
            id_dato_exportacion: id_dato_exportacion,
        };
        $.get('{{url('datos_exportacion/form_asignacion_dato_exportacion')}}', datos, function (retorno) {
            modal_view('modal_form_asignacion_dato_exportacion', retorno, '<i class="fa fa-user-plus" aria-hidden="true"></i> Asignar dato exportación', true, false,'{{isPC() ? '60%' : ''}}');
        });
        $.LoadingOverlay('hide');
    }

    function asignar_dato_exportacion(id_dato_exportacion,id_cliente,input){
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_dato_exportacion: id_dato_exportacion,
            id_cliente: id_cliente,
            check : $("#"+input.id).is(":checked")
        };
        post_jquery('{{url('datos_exportacion/asignar_dato_exportacion')}}', datos, function () {
        });
        $.LoadingOverlay('hide');
    }



</script>
