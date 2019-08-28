<script>
    buscar_listado();
    
    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_consignatarios').val().trim(),
            estado: $('#estado').val(),
        };
        $.get('{{url('consignatario/buscar')}}', datos, function (retorno) {
            $('#div_listado_consignatarios').html(retorno);
            estructura_tabla('table_content_consignatarios');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_clientes .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=' + $('#busqueda_consignatarios').val() + '&');
        $('#div_listado_consignatarios').html($('#table_consignatarios').html());
        $.get(url, function (resul) {
            $('#div_listado_consignatarios').html(resul);
            estructura_tabla('pagination_listado_consignatarios');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function add_consignatario(id_consignatario){
        $.LoadingOverlay('show');
        datos = {
            id_consignatario : id_consignatario
        };
        $.get('{{url('consignatario/add')}}', datos, function (retorno) {
            modal_view('modal_admin_consignatarios', retorno, '<i class="fa fa-user-plus" aria-hidden="true"></i> Añadir consignatario', true, false,'{{isPC() ? '80%' : ''}}');
        });
        $.LoadingOverlay('hide');
    }


    function store_consignatario(id_consignatario){
        if($("#form_add_consignatario").valid()){
            datos = {
                _token : '{{csrf_token()}}',
                id_consignatario : id_consignatario,
                contacto : !$("#div_datos_consignatario").hasClass('hide'),
                nombre : $("#nombre").val(),
                identificacion : $("#identificacion").val(),
                telefono : $("#telefono").val(),
                pais : $("#pais").val(),
                ciudad : $("#ciudad").val(),
                correo : $("#correo").val(),
                direccion : $("#direccion").val(),
                id_contacto_consignatario : $("#id_contacto_consignatario").val(),
                nombre_contacto : $("#nombre_contacto").val(),
                identificacion_contacto : $("#identificacion_contacto").val(),
                telefono_contacto : $("#telefono_contacto").val(),
                pais_contacto : $("#pais_contacto").val(),
                ciudad_contacto : $("#ciudad_contacto").val(),
                correo_contacto : $("#correo_contacto").val(),
                direccion_contacto : $("#direccion_contacto").val(),
            };
            post_jquery('consignatario/store', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function contacto_consignatario(){
        objDOM = $("#div_datos_consignatario");
        if(objDOM.hasClass('hide')){
            objDOM.removeClass('hide')
        }else{
            objDOM.addClass('hide')
        }
    }
    
    function update_consignatario(id_consignatario, estado) {
        $.LoadingOverlay('show');
        datos ={
            id_consignatario : id_consignatario,
            estado : estado,
            _token : '{{csrf_token()}}',
        };
        post_jquery('consignatario/update_estado', datos, function () {
            cerrar_modals();
            buscar_listado();
        });
        $.LoadingOverlay('hide');

    }
</script>
