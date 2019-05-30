<script>
    buscar_facturas();

    function buscar_facturas() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_facturas').val().trim(),
        };
        $.get('{{url('fue/buscar')}}', datos, function (retorno) {
            $('#div_listado_facturas').html(retorno);
            estructura_tabla('table_content_facturas');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function actualizar_fue() {
        if($("#form_actualiar_fue").valid()){
            arr_datos = [];
            modal_quest('modal_crear_especificacion','<div class="alert alert-info text-center">' +
                '¿Esta seguro que desea actualizar estos datos?</div>',
                '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta',true, false, '{{isPC() ? '35%' : ''}}', function () {
                    $.LoadingOverlay('show');

                    $.each($("input.factura_selected"), function (i,j) {
                        arr_datos.push({
                            id_comprobante : j.value,
                            codigo_dae : $("tr#tr_"+j.value+ " td input#codigo_dae").val(),
                            guia_madre : $("tr#tr_"+j.value+ " td input#guia_madre").val(),
                            guia_hija : $("tr#tr_"+j.value+ " td input#guia_hija").val(),
                            manifiesto : $("tr#tr_"+j.value+ " td input#manifiesto").val(),
                            dae : $("tr#tr_"+j.value+ " td input#dae_completa").val(),
                            peso : $("tr#tr_"+j.value+ " td input#peso").val(),
                        });
                    });
                    datos = {
                        _token : '{{csrf_token()}}',
                        arr_datos: arr_datos
                    };
                    post_jquery('fue/actualizar_fue', datos, function () {
                        buscar_facturas();
                        cerrar_modals();
                    });
                    $.LoadingOverlay('hide');
                });
        }
    }
    
    function reporte_fue() {
        $.LoadingOverlay('show');
        $.get('{{url('fue/reporte_fue')}}',{}, function (retorno) {
            modal_view('modal_reporte_fue', retorno, '<i class="fa fa-fw fa-plus"></i> <b>Reporte de Facturas por DAE</b>: ', true, false, '{{isPC() ? '90%' : ''}}', function () {
                $.LoadingOverlay('hide');
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }


</script>
