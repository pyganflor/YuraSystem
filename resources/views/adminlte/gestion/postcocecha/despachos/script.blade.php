<script>
    listar_resumen_pedidos($("#fecha_pedidos_search").val(),'',$("#id_configuracion_empresa_despacho").val());
    
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
                '<div class="alert text-center  alert-warning"><p><i class="fa fa-fw fa-exclamation-triangle"></i> Debe ordenar al menos un pedido para crear el despacho</p></div>',
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
        form_valid = true;
        cant_form = $("div#despachos form").length;
        for (i=1;i<=cant_form;i++)
            if(!$("#form_despacho_"+i).valid()) form_valid = false;

        if(form_valid){
            $.LoadingOverlay('show');
            arr_datos = [];
            arr_sellos = [];
            //arr_pedidos = [];
            for (i=1;i<=cant_form;i++){
                data = "";
                data_sellos = [];
                $.each($("form#form_despacho_"+i+" .sello"),function (i,j) { if(j.value != "") data_sellos.push(j.value); });

                if($("#table_despacho_2").length == 1){
                    tr_piezas = $("form#form_despacho_"+i+" tr#tr_pedido_piezas").length;
                    for (j=1; j <= tr_piezas; j++){
                        id_pedido = $("select#pedido_"+i+"_"+j).val();
                        cant_piezas_camion = 0;
                        cantidad="";
                        $.each($("input.caja_"+i+"_"+j),function (l,m) { if(m.value > 0) cant_piezas_camion += parseInt(m.value); });
                        cantidad += cant_piezas_camion+";";
                        data += id_pedido+"|"+cantidad;
                    }
                }else{
                    pedidos = $("table tr#tr_despachos").length;
                    for (j=1; j<=pedidos; j++) {
                        id_pedido = $(".id_pedido_"+j).val();
                        full = $("td.full_"+j+" input.full").val();
                        half = $("td.half_"+j+" input.half").val();
                        cuarto = $("td.cuarto_"+j+" input.cuarto").val();
                        sexto = $("td.sexto_"+j+" input.sexto").val();
                        octavo = $("td.octavo_"+j+" input.octavo").val();
                        cantidad = parseInt(full) + parseInt(half) + parseInt(cuarto) + parseInt(sexto) + parseInt(octavo);
                        data += id_pedido+"|"+cantidad+";";
                    }
                }
                arr_datos.push({
                    arr_sellos : data_sellos,
                    id_transportista : $("form#form_despacho_"+i+" #id_transportista").val(),
                    id_camion : $("form#form_despacho_"+i+" #id_camion").val(),
                    n_placa : $("form#form_despacho_"+i+" #n_placa").val(),
                    id_conductor : $("form#form_despacho_"+i+" #id_chofer").val(),
                    fecha_despacho : $("form#form_despacho_"+i+" #fecha_despacho").val(),
                    sello_salida : $("form#form_despacho_"+i+" #sello_salida").val(),
                    horario : $("form#form_despacho_"+i+" #horario").val(),
                    semana : $("form#form_despacho_"+i+" #semana").val(),
                    rango_temp : $("form#form_despacho_"+i+" #rango_temp").val(),
                    sello_adicional : $("form#form_despacho_"+i+" #sello_adicional").val(),
                    n_viaje : $("form#form_despacho_"+i+" #n_viaje").val(),
                    horas_salida : $("form#form_despacho_"+i+" #horas_salida").val(),
                    temperatura : $("form#form_despacho_"+i+" #temperatura").val(),
                    kilometraje : $("form#form_despacho_"+i+" #kilometraje").val(),
                    nombre_oficina_despacho : $("#nombre_oficina_despacho").val(),
                    id_oficina_despacho : $("#id_oficina_despacho").val(),
                    nombre_cuarto_frio : $("#nombre_cuarto_frio").val(),
                    id_cuarto_frio : $("#id_cuarto_frio").val(),
                    nombre_transportista : $("form#form_despacho_"+i+" #responsable").val(),
                    //firma_id_transportista : $("#firma_id_transportista").val(),
                    nombre_guardia_turno : $("#nombre_guardia_turno").val(),
                    id_guardia_turno : $("#id_guardia_turno").val(),
                    nombre_asist_comercial : $("#nombre_asist_comercial").val(),
                    id_asist_comercial : $("#id_asist_comercial").val(),
                    correo_oficina_despacho : $("#correo_oficina_despacho").val(),
                    distribucion : data
                });
            }

            //$.each($(".id_pedido"),function (i,j) { arr_pedidos.push(j.value); });
            datos = {
                _token: '{{csrf_token()}}',
                data_despacho : arr_datos,
                //arr_pedidos : arr_pedidos,
            };
            post_jquery('despachos/store_despacho', datos, function () {
                cerrar_modals();
                listar_resumen_pedidos($('#fecha_pedidos_search').val());
                $.LoadingOverlay('hide');
            });

        }
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

    function desbloquea_pedido() {
       /* if($("#id_configuracion_empresa_despacho").val().length < 1){

            $.each($(".orden_despacho"),function (i,j) {
                $(j).attr('disabled',true);
                $(j).val("");
            });
        }else{
            $.each($("div#table_despachos input.id_configuracion_empresa_"+$('#id_configuracion_empresa_despacho').val()),function (i,j) {
                $(j).removeAttr('disabled');
            });

        }
        $.each($("div#table_despachos input").not(".id_configuracion_empresa_"+$('#id_configuracion_empresa_despacho').val()),function (i,j) {
            $(j).attr('disabled',true);
            $(j).val("");
        });*/
        //listar_resumen_pedidos($("#fecha_pedidos_search").val(), '',$("#id_configuracion_empresa_despacho").val());
    }

</script>
