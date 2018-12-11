function add_pedido(id_cliente,pedido_fijo,vista) {
    datos = {
        id_cliente : id_cliente,
        pedido_fijo: pedido_fijo,
        vista      : vista
    };
    get_jquery('/clientes/add_pedido', datos, function (retorno) {
        modal_view('modal_add_pedido', retorno, '<i class="fa fa-fw fa-plus"></i> Agregar pedido', true, false, '70%');
        id_cliente !== '' ?  add_campos(1,id_cliente) : '';
        pedido_fijo != '' ? div_opcion_pedido_fijo(1) : '';
        setTimeout(function () {
            vista == 'pedidos' ? $("#btn_add_campos").attr('disabled',true) : '';
        },500)
    });
}

function add_campos(value,id_cliente) {
    $.LoadingOverlay('show');
    var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;
    cant_tr > -1 ? $('#btn_delete_inputs').removeClass('hide') : '';
    datos ={
        cant_tr    : cant_tr,
        id_cliente : id_cliente
    };
    $.get('/clientes/inputs_pedidos', datos, function (retorno) {
        $('#tbody_inputs_pedidos').append(retorno);
        if($("#id_cliente_venta").length > 0){
           cargar_espeicificaciones_cliente(false);
        }
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function delete_campos(value) {
    $.LoadingOverlay('show');
    var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;

    if($("#tr_inputs_pedido_"+cant_tr+" input#cantidad_"+cant_tr).val().length < 1){
        var tr = $("tbody tr#tr_inputs_pedido_"+cant_tr);
        tr.remove();
        if(cant_tr == 2){
            $('#btn_delete_inputs').addClass('hide');
        }
    }else{
        $('#btn_delete_inputs').addClass('hide')
    }
    $.LoadingOverlay('hide');
}

function div_opcion_pedido_fijo(opcion){
    $.LoadingOverlay('show');
    datos ={
        opcion : opcion,
    };
    $.get('clientes/opcion_pedido_fijo', datos, function (retorno) {
        $('#div_opciones_pedido_fijo').html(retorno);
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function pushSemanas(opcion,arrSemanas) {
    if(opcion == 2 || opcion == 1 ){
        $("select#intervalo option#options_dinamics").remove();
        $.each(arrSemanas,function (i,j) {
            $("select#intervalo").append('<option id="options_dinamics" value="'+(i+1)+'">'+j+'</option>' )
        });
    }
}

function verificar_intervalo_fecha() {

    if($("#fecha_desde_pedido_fijo").val()!='' && $("#fecha_hasta_pedido_fijo").val()!=''){
        $("#intervalo").attr('disabled',false);
    }
    //if($("#fecha_desde_pedido_fijo").val().length > 1 && $("#fecha_hasta_pedido_fijo").val().length > 1){
    var fechaDesde = moment($("#fecha_desde_pedido_fijo").val());
    var fechaHasta = moment($("#fecha_hasta_pedido_fijo").val());
    var diferenciaDias = fechaHasta.diff(fechaDesde, 'days');

    var fechaFormateada = $('#fecha_desde_pedido_fijo').val().replace('/-/g', '/');
    let date = new Date(fechaFormateada);

    var p = 0;
    for (var x = 0; x < diferenciaDias + 2; x++) {
        var fechas = (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
        date.setDate(date.getDate() + 1);
        var d = new Date(fechas);
        if (d.getDay() === parseInt($("#dia_semana").val().trim())) {
            p++
        }
    }
    var arrSemanas = [];
    if (p > 0) {
        for (var i = 0; i < p; i++) {
            var plu = '';
            (i > 0) ?  plu = 's' :  plu;
            arrSemanas.push([(i + 1) + ' Semana' + plu]);
        }
    }
    pushSemanas(1, arrSemanas);
    //}
}

function add_fechas_pedido_fijo_personalizado() {

    $.LoadingOverlay('show');
    var cant_div = $('#td_fechas_pedido_fijo_personalizado div.col-md-4').length;
    if(cant_div > 0) {
        $('#btn_delete_fechas_pedido_fijo_personalizado').removeClass('hide');
    }
    datos ={
        cant_div : cant_div,
    };
    $.get('clientes/add_fechas_pedido_fijo_personalizado', datos, function (retorno) {
        $('#td_fechas_pedido_fijo_personalizado').append(retorno);
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function delete_fechas_pedido_fijo_personalizado() {

    $.LoadingOverlay('show');
    var cant_div = $('#td_fechas_pedido_fijo_personalizado div.col-md-4').length;
    var div = $("#div_"+cant_div);
    div.remove();

    if(cant_div == 2){
        $('#btn_delete_fechas_pedido_fijo_personalizado').addClass('hide');
    }
    $.LoadingOverlay('hide');
}

function habilitar_campos() {
    $("#fecha_desde_pedido_fijo").attr('disabled',false);
    $("#fecha_hasta_pedido_fijo").attr('disabled',false);
}

function store_pedido(id_cliente,pedido_fijo,csrf_token,vista) {

    if ($('#form_add_pedido').valid()){
        var result = confirm("Una vez guardado el pedido no puede ser editado");
        if(result) {
            var arrFechas = [];

            if(pedido_fijo && ($("#opcion_pedido_fijo").val() == 1) ||$("#opcion_pedido_fijo").val() == 2){
                var fechaDesde= moment($("#fecha_desde_pedido_fijo").val());
                var fechaHasta= moment($("#fecha_hasta_pedido_fijo").val());
                var diferenciaDias = fechaHasta.diff(fechaDesde, 'days');

                var fechaFormateada = $('#fecha_desde_pedido_fijo').val().replace('/-/g', '/');
                let date = new Date(fechaFormateada);
                var x = 1;

                //($("#opcion_pedido_fijo").val() == 2 || $("#opcion_pedido_fijo").val() == 1 ) ? diferenciaDias = diferenciaDias + 2 : diferenciaDias;
                for(var i=0; i<diferenciaDias+2; i++ ){

                    var fechas =(date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear();
                    date.setDate(date.getDate()+1);
                    var d = new Date(fechas);

                    if($("#opcion_pedido_fijo").val() == 1){
                        if(d.getDay() === parseInt($("#dia_semana").val().trim())){
                            if(x === parseInt($("#intervalo").val())){
                                arrFechas.push(fechas);
                                x = 0;
                            }
                            x++;
                        }
                    }else if($("#opcion_pedido_fijo").val() == 2){
                        if(d.getDate() == parseInt($("#dia_mes").val())){
                            arrFechas.push(fechas);
                        }
                    }
                }
            }else if(pedido_fijo && $("#opcion_pedido_fijo").val() == 3){
                $cant_pedidos = $("#td_fechas_pedido_fijo_personalizado div.col-md-4").length;
                for(var i=0; i<$cant_pedidos; i++){
                    arrFechas.push(
                        $("input#fecha_desde_pedido_fijo_"+(i+1)).val()
                    );
                }
            }
            $.LoadingOverlay('show');var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;
            var arrDataDetallesPedido = [];
            for (var i = 1; i <= cant_tr; i++) {
                arrDataDetallesPedido.push([
                    $("#cantidad_" + i).val(),
                    $("#id_especificacion_" + i).val(),
                    $("#id_agencia_carga_" + i).val(),
                ]);
            }

            datos = {
                _token: csrf_token,
                arrDataDetallesPedido : arrDataDetallesPedido,
                descripcion           : $('#descripcion').val(),
                fecha_de_entrega      : $('#fecha_de_entrega').length ?  $('#fecha_de_entrega').val() : '',
                id_cliente            : id_cliente == '' ? $("#id_cliente_venta").val() : id_cliente,
                id_pedido             : $('#id_pedido').val(),
                arrFechas             : arrFechas.length < 1 ? '' : arrFechas,
                pedido_fijo           : $("#opcion_pedido_fijo").length > 0 ? $("#opcion_pedido_fijo").val() : '',
                opcion                : $("#opcion_pedido_fijo").val()
            };
            post_jquery('clientes/store_pedidos', datos, function () {
                cerrar_modals();

                if(vista != 'pedidos'){
                    detalles_cliente( id_cliente == '' ? id_cliente = $("#id_cliente_venta").val() : id_cliente);
                }
            });
            $.LoadingOverlay('hide');
        }
    }
}


function cancelar_pedidos(id_pedido,id_cliente) {

    $.LoadingOverlay('show');
    datos = {
        _token: '{{csrf_token()}}',
        id_pedido: id_pedido,
    };
    get_jquery('clientes/cancelar_pedido', datos, function () {
        cerrar_modals();
        detalles_cliente(id_cliente);
    });
    $.LoadingOverlay('hide');
}

function cargar_espeicificaciones_cliente(remove) {
    $.LoadingOverlay('show');
    remove ? $("#tbody_inputs_pedidos tr").remove() : '';
    var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;
    datos = {
        id_cliente : $("#id_cliente_venta").val()
    };
   get_jquery('pedidos/cargar_especificaciones', datos, function (response) {
       remove ? add_campos(1, '') : '';
        setTimeout(function () {
            $.each(response['especificaciones'],function (i,j) {
                $("#id_especificacion_"+cant_tr).append('<option value="'+j.id_cliente_pedido_especificacion+'">'+j.nombre+'</option>');
            });
            $.each(response['agencias_carga'],function (i,j) {
                $("#id_agencia_carga_"+cant_tr).append('<option value="'+j.id_agencia_carga+'">'+j.nombre+'</option>');
            });
        },500);
       $("#btn_add_campos").attr('disabled',false);
    });
    $.LoadingOverlay('hide');
}

function detalles_cliente(id_cliente) {
    $.LoadingOverlay('show');
    datos = {
        id_cliente: id_cliente
    };
    $.get('clientes/ver_detalles_cliente', datos, function (retorno) {
        modal_view('modal_view_detalle_cliente', retorno, '<i class="fa fa-fw fa-eye"></i> Detalles de cliente', true, false, '75%');
    });
    $.LoadingOverlay('hide');
}


function add_envio(id_pedido){
    $.LoadingOverlay('show');
    datos = {
        id_pedido: id_pedido
    };
    $.get('clientes/add_envio', datos, function (retorno) {
        modal_form('modal_view_envio_pedido', retorno, '<i class="fa fa-plane" ></i> Crear envío', true, false, '75%', function () {
            store_envio();
        });
    });
    $.LoadingOverlay('hide');
}


function add_form_envio(id_form,total) {
    //var catn_row_div_inputs = $("#div_inputs_envios_"+id_form+" div#rows").length;
    //$("#div_inputs_envios_"+id_form+" #id_agencia_transporte_"+catn_row_div_inputs+",#cantidad_"+catn_row_div_inputs+",#envio_"+catn_row_div_inputs).attr('disabled',true);


    var cant_total_pedidos = $("#cantidad_detalle_form_"+id_form).val();

    //console.log(cant_total_pedidos);
    var cant_rows = $("form#form_envio_"+id_form+ " div#rows").length;

    cant_rows < 1 ? agregar_inputs(cant_rows,cant_total_pedidos,id_form,total) : '';

    if(cant_rows >= 1 ){
       var campo_at = $("#id_agencia_transporte_"+id_form+"_"+cant_rows).val();
       var campo_c  =  $("#cantidad_"+id_form+"_"+cant_rows).val();
       var campo_e  =  $("#envio_"+id_form+"_"+cant_rows).val();
       cant_rows == 0 ? total = total - campo_c : '';

       var totales_cantidad = 0;
       for(var i=1; i<=cant_rows; i++){
          totales_cantidad =  totales_cantidad + parseInt($("#cantidad_"+id_form+"_"+i).val());
       }
        total2 = total - totales_cantidad;

        if((campo_at == undefined || campo_at == null) ||( campo_c == undefined || campo_c == null) || ( campo_e == undefined || campo_e == null ) ){
            $('#msg_'+id_form).html('<b>Complete todos los campos del Envío N# '+cant_rows+'</b>');
        }else{
            agregar_inputs(cant_rows,cant_total_pedidos,id_form, total2);
            $('#msg_'+id_form).html('');
        }
    }
}

function agregar_inputs(cant_rows,cant_total_pedidos,id_form, total) {
    $.LoadingOverlay('show');
    if(total > 0){
        datos = {
            rows         : cant_rows+1,
            cant_pedidos : cant_total_pedidos,
            id_form      : id_form
        };
        $.get('clientes/add_form_envio', datos, function (retorno) {
            $("#div_inputs_envios_"+id_form).append(retorno);

            for(var i=1; i<=total; i++){
                $("#cantidad_"+id_form+"_"+(cant_rows+1)).append('<option value="'+i+'">'+i+'</option>');
            }
            $('#msg_'+id_form).html('');


            setTimeout(function () {
                var cant_forms = $('div.well').length;
                for(var j=1; j<=cant_forms; j++){

                    var cant_rows_x_form = $("#div_inputs_envios_"+j+" div#rows" ).length;

                    if($("#form_envio_"+j)[0].id !== $("#form_envio_"+id_form)[0].id){

                        console.log("diferentes");
                        if(j != parseInt(id_form)){
                           $("option#dinamic_"+j+"").remove();
                            for(var x=1; x<=cant_rows_x_form; x++){
                                $("select[name=envio_"+id_form+"]").append("<option id='dinamic_"+j+"'>Detalle N#" + j + " , Envio N#" + x + " </option>")
                            }
                        }

                    }else{
                        var cant_rows_x_form = $("#div_inputs_envios_"+j+" div#rows" ).length;
                        console.log(j , parseInt(id_form));
                        if(j == parseInt(id_form)) {
                            console.log(cant_rows_x_form); //aqu'i llega
                            for(var z=1; z<=cant_rows_x_form; z++){
                                console.log(j , parseInt(id_form));
                                if(j != parseInt(id_form)) {
                                    console.log("hola");
                                    $("option#dinamic_"+z+"").remove();
                                    $("select[name=envio_"+z+"]").append("<option id='dinamic_"+j+"'>Detalle N#" + j + " , Envio N#" + z + " </option>")
                                }
                            }
                        }
                    }
                }
            },500)


        });

    }else{
        setTimeout(function () {
            $('#msg_'+id_form).html('No se pueden realizar mas envíos en este detalle');
        },500);

    }
    $.LoadingOverlay('hide');
}

function reset_form_envio(id_form) {

}

function store_envio() {
    
}
