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
    
    function desactivar_transportista(id_transportista,estado) {
        modal_quest('modal_desactivar_transportista', 'Esta seguro que desea desctivar este transportista?', "<i class='fa fa-question-circle-o'></i> Descativar transportista",true, false, '{{isPC() ? '80%' : ''}}', function () {
            $.LoadingOverlay('show');
            datos = {
                _token    : '{{csrf_token()}}',
                id_transportista : id_transportista,
                estado: estado
            };
            post_jquery('{{url('transportista/update_estado')}}', datos, function () {
                cerrar_modals();
                buscar_listado_transportista();
            });
            $.LoadingOverlay('hide');
        });
    }

    function add_camiones_condutores(id_transportista){
        $.LoadingOverlay('show');
        datos = {
            id_transportista : id_transportista,
        };
        $.get('{{url('transportista/list_camiones_conductores')}}', datos, function (retorno) {
            modal_view('modal_camiones_condutores', retorno, '<i class="fa fa-fw fa-plus"></i> <b>Comiones y conductores</b>: ', true, false, '{{isPC() ? '60%' : ''}}', function () {});
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_camion(id_camion) {
        $.LoadingOverlay('show');
        datos = {
            id_camion : id_camion
        };
        $.get('{{url('transportista/add_camion')}}', datos, function (retorno) {
            modal_form('modal_add_camion', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir camión', true, false, '{{isPC() ? '50%' : ''}}', function () {
                store_camion(id_camion);
                $.LoadingOverlay('hide');
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_camion(id_camion) {
        if($("#form_add_camion").valid()) {
            datos = {
                _token: '{{csrf_token()}}',
                id_camion: id_camion,
                modelo: $("#modelo").val(),
                placa: $("#placa").val(),
                id_transportista: $("#id_transportista").val()
            };
            post_jquery('{{url('transportista/store_camion')}}', datos, function () {
                cerrar_modals();
                add_camiones_condutores($("#id_transportista").val());
            });
            $.LoadingOverlay('hide');
        }
    }

    function update_estado_camion(id_camion,estado) {
        modal_quest('modal_desactivar_camion', 'Esta seguro que desea desctivar este camión?', "<i class='fa fa-question-circle-o'></i> Descativar camión",true, false, '{{isPC() ? '50%' : ''}}', function () {
            $.LoadingOverlay('show');
            datos = {
                _token    : '{{csrf_token()}}',
                id_camion : id_camion,
                estado: estado
            };
            post_jquery('{{url('transportista/update_estado_camion')}}', datos, function () {
                cerrar_modals();
                add_camiones_condutores($("#id_transportista").val());
            });
            $.LoadingOverlay('hide');
        });
    }

    function add_conductor(id_conductor) {
            $.LoadingOverlay('show');
            datos = {
                id_conductor: id_conductor
            };
            $.get('{{url('transportista/add_conductor')}}', datos, function (retorno) {
                modal_form('modal_add_conductor', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir conductor', true, false, '{{isPC() ? '50%' : ''}}', function () {
                    store_conductor(id_conductor);
                    $.LoadingOverlay('hide');
                });
            }).always(function () {
                $.LoadingOverlay('hide');
            });
    }

    function store_conductor(id_conductor) {
        if($("#form_add_conductor").valid()){
            datos = {
                _token    : '{{csrf_token()}}',
                id_conductor : id_conductor,
                nombre : $("#nombre").val(),
                tipo_identificacion : $("#tipo_identificacion").val(),
                identificacion : $("#identificacion").val(),
                id_transportista : $("#id_transportista").val()
            };
            post_jquery('{{url('transportista/store_conductor')}}', datos, function () {
                cerrar_modals();
                add_camiones_condutores($("#id_transportista").val());
            });
            $.LoadingOverlay('hide');
        }
    }

    function update_estado_conductor(id_conductor,estado) {
        modal_quest('modal_desactivar_conductor', 'Esta seguro que desea desctivar este conductor?', "<i class='fa fa-question-circle-o'></i> Descativar conductor",true, false, '{{isPC() ? '50%' : ''}}', function () {
            $.LoadingOverlay('show');
            datos = {
                _token    : '{{csrf_token()}}',
                id_conductor : id_conductor,
                estado: estado
            };
            post_jquery('{{url('transportista/update_estado_conductor')}}', datos, function () {
                cerrar_modals();
                add_camiones_condutores($("#id_transportista").val());
            });
            $.LoadingOverlay('hide');
        });
    }
</script>
