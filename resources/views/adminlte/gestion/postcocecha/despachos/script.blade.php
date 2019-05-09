<script>
    listar_resumen_pedidos($('#fecha_pedidos_search').val());
    
    function empaquetar(fecha) {
        $.LoadingOverlay('show');
        datos = {
            fecha: fecha,
        };
        $.get('{{url('despachos/empaquetar')}}', datos, function (retorno) {
            modal_view('modal_view_empaquetar', retorno, '<i class="fa fa-fw fa-gift"></i> Empaquetar', true, false, '{{isPc() ? '35%' : ''}}');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function crear_despacho() {
        var arr_pedidos = [], arr_ordenado, pedidos =[];
        $.each($(".orden_despacho"),function (i,j) {
            if(j.value !== '')
                arr_pedidos.push(j.value);
        });
        arr_ordenado = arr_pedidos.sort(menor_mayor);
        for (x=0;x<arr_ordenado.length;x++){
            $.each($(".orden_despacho"),function (i,j) {
                if(j.value !== '' && arr_ordenado[x] === j.value )
                    pedidos.push(j.id)
            });
        }
        if (pedidos.length === 0) {
            modal_view('modal_view_msg_factura',
                '<div class="alert text-center  alert-warning"><p><i class="fa fa-fw fa-exclamation-triangle"></i> Debe seleccionar al menos un pedido para crear el despacho</p></div>',
                '<i class="fa fa-truck" aria-hidden="true"></i> Despacho', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        $.LoadingOverlay('show');
        datos = {
            _token  : '{{csrf_token()}}',
            pedidos : pedidos
        };
        $.post('{{url('despachos/crear_despacho')}}', datos, function (retorno) {
            modal_form('modal_despacho', retorno, '<i class="fa fa-truck" ></i> Crear despacho', true, false, '{{isPC() ? '80%' : ''}}', function () {
                store_despacho();
                $.LoadingOverlay('hide');
            });
        });
    }

    function store_despacho() {
        if($("#form_despacho").valid()){
            $.LoadingOverlay('show');
            arr_sellos = [];
            arr_pedidos = [];
            $.each($(".sello"),function (i,j) {
                if(j.value != "")
                    arr_sellos.push(j.value);
            });
            $.each($(".id_pedido"),function (i,j) {
                arr_pedidos.push(j.value);
            });
            datos = {
                _token: '{{csrf_token()}}',
                id_transportista : $("#id_transportista").val(),
                id_camion : $("#id_camion").val(),
                n_placa : $("#n_placa").val(),
                id_conductor : $("#id_chofer").val(),
                fecha_despacho : $("#fecha_despacho").val(),
                sello_salida : $("#sello_salida").val(),
                horario : $("#horario").val(),
                semana : $("#semana").val(),
                rango_temp : $("#rango_temp").val(),
                sello_adicional : $("#sello_adicional").val(),
                n_viaje : $("#n_viaje").val(),
                horas_salida : $("#horas_salida").val(),
                temperatura : $("#temperatura").val(),
                kilometraje : $("#kilometraje").val(),
                nombre_oficina_despacho : $("#nombre_oficina_despacho").val(),
                id_oficina_despacho : $("#id_oficina_despacho").val(),
                nombre_cuarto_frio : $("#nombre_cuarto_frio").val(),
                id_cuarto_frio : $("#id_cuarto_frio").val(),
                nombre_transportista : $("#nombre_transportista").val(),
                firma_id_transportista : $("#firma_id_transportista").val(),
                nombre_guardia_turno : $("#nombre_guardia_turno").val(),
                id_guardia_turno : $("#id_guardia_turno").val(),
                nombre_asist_comercial : $("#nombre_asist_comercial").val(),
                id_asist_comercial : $("#id_asist_comercial").val(),
                correo_oficina_despacho : $("#correo_oficina_despacho").val(),
                sellos : arr_sellos,
                pedidos : arr_pedidos
            };

            post_jquery('despachos/store_despacho', datos, function () {
                cerrar_modals();
                listar_resumen_pedidos($('#fecha_pedidos_search').val());
                $.LoadingOverlay('hide');
            });

        }
    }

    function busqueda_camiones_conductores() {
        datos = {
            id_transportista : $("#id_transportista").val()
        };
        $.get('{{url('despachos/list_camiones_conductores')}}', datos, function (retorno) {
            $.each(retorno.camiones,function (i,j) {
                $("#id_camion").append("<option id='camion_dinamic' value='"+j.id_camion+"'>"+j.modelo+"</option>")
                if(i === 0)
                    $("#n_placa").val(j.placa);
            });
            $.each(retorno.conductores,function (i,j) {
                $("#id_chofer").append("<option id='chofer_dinamic' value='"+j.id_conductor+"'>"+j.nombre+"</option>")
            });

        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function busqueda_placa_camion() {
        datos = {
            id_camion : $("#id_camion").val()
        };
        $.get('{{url('despachos/list_placa_camion')}}', datos, function (retorno) {
            $("#n_placa").val(retorno.placa)
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function duplicar_nombre(input){
        $("#nombre_transportista").val(input.value);
    }

    function ver_despachos() {
        $.LoadingOverlay('show');
        $.get('{{url('despachos/ver_despachos')}}', {}, function (retorno) {
            modal_view('modal_view_despachos', retorno, '<i class="fa fa-truck"></i> Despachos realizados', true, false, '{{isPc() ? '60%' : ''}}');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function update_estado_despacho(id_despacho,estado) {
        modal_quest('modal_despacho', "<div class='alert alert-danger text-center'>Desea cancelar este despacho?</div>", "<i class='fa fa-exclamation-triangle' ></i> Seleccione una opción",true, false, '{{isPC() ? '35%' : ''}}', function () {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_despacho: id_despacho,
                estado: estado,
            };
            post_jquery('{{url('despachos/update_estado_despachos')}}', datos, function () {
                cerrar_modals();
                listar_resumen_pedidos($('#fecha_pedidos_search').val());
                ver_despachos()
            });
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_despachos .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=&' );
        $('#div_listado_despachos').html($('#table_despachos').html());
        $.get(url, function (resul) {
            $('#div_listado_despachos').html(resul);
            estructura_tabla('table_content_despachos');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });




</script>
