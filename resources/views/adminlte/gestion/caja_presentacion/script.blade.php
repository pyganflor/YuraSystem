<script>
    
    buscar_empaques();
    
    function buscar_empaques() {
        $.LoadingOverlay('show');
        /*datos = {
            anno               : $('#anno').val(),
            id_cliente         : $('#id_cliente').val(),
            codigo_comprobante : $("#codigo_comprobante").val(),
            desde              : $('#desde').val(),
            hasta              : $('#hasta').val(),
            estado             : $('#estado').val()
        };*/
        $.get('{{url('caja_presentacion/buscar_empaque')}}', {}/*datos*/, function (retorno) {
            $('#div_listado_empaque').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
    
    function add_empaque(id_empaque) {
        datos = {
            id_empaque : id_empaque
        };
        $.LoadingOverlay('show');
        $.get('{{url('caja_presentacion/add_empaque')}}', datos, function (retorno) {
            modal_form('modal_add_empaque', retorno, '<i class="fa fa-pencil" aria-hidden="true"></i> Editar nombre del empaque', true, false, '{{isPC() ? '40%' : ''}}', function () {
                store_empaque(id_empaque);
                cerrar_modals();
                $.LoadingOverlay('hide');
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_empaque(id_empaque) {
        datos = {
            id_empaque : id_empaque,
            nombre     : $("#nombre_empaque").val(),
            tipo       : $("#tipo").val()
        };
        $.LoadingOverlay('show');
        $.get('{{url('caja_presentacion/store_empaque')}}', datos, function (retorno) {
            modal_view('modal_empaque', retorno, '<i class="fa fa-fw fa-gift"></i> Mensaje empaque', true, false,'{{isPC() ? '40%' : ''}}');
            buscar_empaques();
            cerrar_modals();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function update_detalle_empaque(id_empaque,estado) {
        datos = {
            id_empaque : id_empaque,
            nombre     : $("#nombre_empaque").val(),
            estado     : estado
        };
        $.LoadingOverlay('show');
        $.get('{{url('caja_presentacion/update_estado_empaque')}}', datos, function (retorno) {
            modal_view('modal_empaque', retorno, '<i class="fa fa-fw fa-gift"></i> Mensaje empaque', true, false,'{{isPC() ? '40%' : ''}}');
            buscar_empaques();
            cerrar_modals();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function exportar_detalle_empaque() {
        $.LoadingOverlay('show');
        $.ajax({
            type: "POST",
            dataType: "html",
            contentType: "application/x-www-form-urlencoded",
            url: '{{url('caja_presentacion/exportar_detalle_empaque')}}',
            data: {
                _token: '{{csrf_token()}}'
            },
            success: function (data) {
                var opResult = JSON.parse(data);
                var $a = $("<a>");
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Detalles empaque.xlsx");
                $a[0].click();
                $a.remove();
                $.LoadingOverlay('hide');
            }
        });
    }
    
    function form_add_detalle_empaque() {
        $.LoadingOverlay('show');
        $.get('{{url('caja_presentacion/form_file_detalle_empaque')}}', {}, function (retorno) {
            modal_form('modal_file_detalle_empaque', retorno, '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Archivo detalles empaque', true, false, '{{isPC() ? '40%' : ''}}', function () {
                importar_excel_detalle_empque();
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function importar_excel_detalle_empque(){
        $.LoadingOverlay('show');
        if($("#form_add_detalle_empaque").valid()){
            var formData = new FormData($("#form_add_detalle_empaque")[0]);
            formData.append('_token','{{csrf_token()}}');
            $.ajax({
                url: '{{url('caja_presentacion/importar_detalle_empaque')}}',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(retorno){
                    $.LoadingOverlay('hide');
                    modal_view('modal_view_detalle_empaque', retorno, '<i class="fa fa-fw fa-table"></i> Detalles empaque', true, false,
                        '{{isPC() ? '50%' : ''}}');
                   // buscar_listado();

                }
            });
        }
    }

    function detalle_empaque(id_empaque){
        $.LoadingOverlay('show');
        datos = {
            id_empaque : id_empaque
        };
        $.get('{{url('caja_presentacion/detalle_empaque')}}', datos, function (retorno) {
            modal_view('modal_detalle_empaque', retorno, '<i class="fa fa-list" aria-hidden="true"></i> Detalles empaque <span id="span_nombre_empaque" style="font-weight:900"></span>', true, false,'{{isPC() ? '60%' : ''}}');

            /*modal_form('modal_detalle_empaque', retorno, '<i class="fa fa-list" aria-hidden="true"></i> Detalles empaque <span id="span_nombre_empaque" style="font-weight:900"></span>', true, false, '{{isPC() ? '65%' : ''}}', function () {
               store_detalle_empque();
            });*/
            setTimeout(function(){
                $("#span_nombre_empaque").html($("#nombre_empaque").val());
            },500);

        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_detalle_empque() {
        $.LoadingOverlay('show');
        arrData=[];
        var cant_inputs = $("form#form_add_detalle_empaque div.row").length;

        for(var i=1;i<=cant_inputs;i++){
            arrData.push({
                id_variedad: $("#id_variedad_"+i).val(),
                clasificacion_ramo :$("#clasificacion_ramo_" + i).val(),
                id_clasificacion_ramo :$("#id_clasificacion_ramo_" + i).val(),
                unidad_medida : $("#id_unidad_medida_" + i).val(),
                cantidad_ramos: $("#cantidad_ramo_" + i).val(),
                id_detalle_empaque: $("#id_detalle_empaque_" + i).val(),
            });
        }
        datos = {
            arrData : arrData,
        };
        $.get('{{url('caja_presentacion/store_detalle_empaque')}}', datos, function (retorno) {
            modal_view('modal_empaque', retorno, '<i class="fa fa-fw fa-gift"></i> Mensaje empaque </span>', true, false,'{{isPC() ? '40%' : ''}}');
            buscar_empaques();
            cerrar_modals();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_input_detalle_empaque(){
        $.LoadingOverlay('show');
        $.get('{{url('caja_presentacion/add_empaque')}}', datos, function (retorno) {
            modal_form('modal_add_empaque', retorno, '<i class="fa fa-pencil" aria-hidden="true"></i> Editar nombre del empaque', true, false, '{{isPC() ? '40%' : ''}}', function () {
                store_empaque(id_empaque);
                cerrar_modals();
                $.LoadingOverlay('hide');
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function delete_detalle_empaque(id_detalle_empaque,id_empaque){
        modal_quest('modal_message_facturar_envios',
            '<div class="alert alert-warning text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Esta seguro que desea eliminar este detalle empaque? , al hacerlo no podrá crear una nueva especificación con este detalle</div>',
            '<i class="fa fa-list-alt" aria-hidden="true"></i> Mensaje', true, false, '{{isPC() ? '60%' : ''}}', function () {
                datos = {
                    id_detalle_empaque: id_detalle_empaque,
                };
                $.LoadingOverlay('show');
                $.get('{{url('caja_presentacion/delete_detalle_empaque')}}', datos, function (retorno) {
                    modal_view('modal_detalle_empaque', retorno, '<i class="fa fa-fw fa-gift"></i> Mensaje detalle empaque', true, false, '{{isPC() ? '40%' : ''}}');
                    cerrar_modals();
                    detalle_empaque(id_empaque);
                }).always(function () {
                    $.LoadingOverlay('hide');
                });
            });
    }

</script>
