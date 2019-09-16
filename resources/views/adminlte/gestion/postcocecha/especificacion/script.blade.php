<script>
    buscar_listado_especificaciones();

    $(document).on("click", "#pagination_listado_especificaciones .pagination li a", function (e) {
       // $.LoadingOverlay("show");
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
           // $.LoadingOverlay("hide");
        });
    });

    function asignar_especificacicon(id_especificacion,nombre_especificacion){
        $.LoadingOverlay('show');
        datos = {
            id_especificacion : id_especificacion
        };
        $.get('{{url('especificacion/form_asignacion_especificacion')}}', datos, function (retorno) {
            modal_view('modal_asignar_especificacion', retorno, '<i class="fa fa-fw fa-plus"></i> <b>Especificación</b>: ', true, false, '{{isPC() ? '70%' : ''}}', function () {
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

    function add_row_especificacion(){
        cant_rows = $("tbody#div_nueva_especificacion tr").length;
        $("#btn_add_row_especificacion_"+cant_rows).attr('disabled',true);
        datos = {
            cant_rows : cant_rows
        };
        $.get('especificacion/add_row_especificacion', datos, function (retorno) {
            $("#td_btn_add_store_"+cant_rows+" button").remove();
            $("tbody#div_nueva_especificacion").append(retorno);
            $("#td_btn_add_store_"+(cant_rows+1)).append(
                "<div class='btn-group' role='group' aria-label='Basic example'>"+
                "<button type='button' class='btn btn-danger btn-xs' id='"+(cant_rows+1)+"' title='Eliminar fila' onclick='delete_row_especificacion(this.id)'>"+
                "<i class='fa fa-trash' aria-hidden='true'></i>"+
                "</button>"+
                "<button type='button' class='btn btn-success btn-xs' id='btn_add_row_especificacion_"+(cant_rows+1)+"' title='Crear fila' onclick='add_row_especificacion()'>"+
                "<i class='fa fa-plus' aria-hidden='true'></i>"+
                "</button>"+
                "<button type='button' class='btn btn-primary btn-xs' id='btn_store_row_especificacion_"+(cant_rows+1)+"' title='Guardar' onclick='store_nueva_especificacion()'>"+
                "<i class='fa fa-floppy-o' aria-hidden='true'></i> Guardar"+
                "</button></div>");

        }).always(function () {
            $("#btn_add_row_especificacion_"+cant_rows).attr('disabled',false);
            $.LoadingOverlay('hide');
        });
    }

    function delete_row_especificacion(id) {
        $("tbody#div_nueva_especificacion tr#tr_nueva_especificacion_"+id).remove();
        if(id > 2){
            $("td#td_btn_add_store_"+(id-1)).append(
                "<div class='btn-group' role='group' aria-label='Basic example'>"+
                "<button type='button' class='btn btn-danger btn-xs' id='"+(id-1)+"' title='Eliminar fila' onclick='delete_row_especificacion(this.id)'>"+
                "<i class='fa fa-trash' aria-hidden='true'></i>"+
                "</button>"+
                "<button type='button' class='btn btn-success btn-xs' title='Crear fila' id='btn_add_row_especificacion_"+(id-1)+"' onclick='add_row_especificacion()'>"+
                "<i class='fa fa-plus' aria-hidden='true'></i>"+
                "</button>"+
                "<button type='button' class='btn btn-primary btn-xs' title='Guardar datos' onclick='store_nueva_especificacion()'>"+
                "<i class='fa fa-floppy-o' aria-hidden='true'></i> Guardar"+
                "</button></div>");
        }else{
            $("td#td_btn_add_store_"+(id-1)).append(
                "<div class='btn-group' role='group' aria-label='Basic example'>"+
                "<button type='button' class='btn btn-success btn-xs' id='btn_add_row_especificacion_"+(id-1)+"' title='Crear fila' onclick='add_row_especificacion()'>"+
                "<i class='fa fa-plus' aria-hidden='true'></i>"+
                "</button>"+
                "<button type='button' class='btn btn-primary btn-xs' id='btn_store_row_especificacion_"+(id-1)+"' title='Guardar datos' onclick='store_nueva_especificacion()'>"+
                "<i class='fa fa-floppy-o' aria-hidden='true'></i> Guardar"+
                "</button></div>");
        }
    }

    function store_nueva_especificacion() {
        html = "<div class='col-md-12'><p>Seleccione la forma en la que desea crear la especificación</p></div>" +
                "<div class='row'>" +
                    "<div class='col-md-12'>"+
                        "<div class='col-md-6'>" +
                                "<input type='radio' id='individual' name='radio' value='0'> "+
                                " <label>Individual</label>"+
                            "</div>"+
                            "<div class='col-md-6'>"+
                                "<input type='radio' id='agrupado' name='radio' value='1'> "+
                                " <label>Agrupado</label>"+
                        "</div>" +
                    "</div>" +
                "</div>";

        modal_quest('modal_crear_especificacion', html, "<i class='fa fa-cubes'></i> Seleccione una opción",true, false, '{{isPC() ? '25%' : ''}}', function () {

                $.LoadingOverlay('show');
                arrData = [];
                $.each($('select[name=id_variedad]'), function (i, j) {
                    arrData.push({
                        'id_variedad' : $("#id_variedad_"+(i+1)).val(),
                        'id_clasificacion_ramo_' : $("#id_clasificacion_ramo_"+(i+1)).val(),
                        'id_empaque' : $("#id_empaque_"+(i+1)).val(),
                        'ramos_x_caja' : $("#ramo_x_caja_"+(i+1)).val(),
                        'id_presentacion' : $("#id_presentacion_"+(i+1)).val(),
                        'tallos_x_ramo' : $("#tallos_x_ramo_"+(i+1)).val(),
                        'longitud' : $("#longitud_"+(i+1)).val(),
                        'id_unidad_medida' : $("#id_unidad_medida_"+(i+1)).val(),
                    });
                });
                datos = {
                    arrData : arrData,
                    modo : $('input:radio[name=radio]:checked').val(),
                    _token: '{{csrf_token()}}',
                };
                $.post('{{url('especificacion/store_row_especificacion')}}', datos, function (retorno) {
                    modal_view('modal_message_especificaciones', retorno.mensaje, '<i class="fa fa-exclamation-triangle"></i> Especificación', true, false,'{{isPC() ? '50%' : ''}}');
                    buscar_listado_especificaciones();
                    cerrar_modals();
                }, 'json').fail(function (retorno) {
                    alerta_errores(retorno.responseText);
                    alerta('Ha ocurrido un problema al enviar la información');
                }).always(function () {
                    $.LoadingOverlay('hide');
                });

        });
    }
</script>
