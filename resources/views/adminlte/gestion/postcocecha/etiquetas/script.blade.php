<script>
    listado_etiquetas();

    function listado_etiquetas(){
        $.LoadingOverlay('show');
        datos = {
            desde: $('#desde').val(),
            hasta: $('#hasta').val(),
            id_configuracion_empresa: $("#id_configuracion_empresa").val()
        };
        $.get('{{url('etiqueta/listado')}}', datos, function (retorno) {
            $('#div_listado_etiquetas').html(retorno);
            //estructura_tabla('table_content_etiqueta');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function select_doble(input) {
        $.each($(".doble"),function(i,j){
            $(input).is(":checked")
                ? $(j).prop('checked',true)
                : $(j).prop('checked',false);
        });
    }

    function select_exportar(input) {
        $.each($(".exportar"),function(i,j){
            $(input).is(":checked")
                ? $(j).prop('checked',true)
                : $(j).prop('checked',false);
        });
    }

    function generar_excel(){
        arr_facturas = [];
        cant_facturas = $("#tbody_etiquetas_facturas tr").length;

        for (let x=1;x<=cant_facturas;x++){
            $.each($("#tr_exportables_"+x+" .exportar"), function (i, j) {
                if ($(j).is(":checked")) {
                    arr_facturas.push({
                        caja: j.value,
                        id_pedido : $("#tr_exportables_"+x+" .id_pedido").val(),
                        doble: $("#doble_" + j.name.split("_")[1]).is(":checked")
                    });
                }
            });
        }

        if (arr_facturas.length === 0) {
            modal_view('modal_view_msg_factura',
                '<div class="alert text-center  alert-warning"><p><i class="fa fa-fw fa-exclamation-triangle"></i> Debe seleccionar al menos una factura para generar la(s) etiqueta(s)</p></div>',
                '<i class="fa fa-clone"></i> Etiquetas', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }

        modal_quest('modal_exportar_etiquetas', "Desea exportar el excel con las facturas seleccionadas?", "<i class='fa fa-cubes'></i> Seleccione una opción",true, false, '{{isPC() ? '35%' : ''}}', function () {
            $.LoadingOverlay('show');
            $.ajax({
                type: "POST",
                dataType: "html",
                contentType: "application/x-www-form-urlencoded",
                url: '{{url('etiqueta/exportar_excel')}}',
                data: {
                    arr_facturas : arr_facturas,
                    _token: '{{csrf_token()}}'
                },
                success: function (data) {
                    var opResult = JSON.parse(data);
                    var $a = $("<a>");
                    $a.attr("href", opResult.data);
                    $("body").append($a);
                    $a.attr("download", "Etiquestas Cajas.xlsx");
                    $a[0].click();
                    $a.remove();
                    cerrar_modals();
                    $.LoadingOverlay('hide');
                }
            });
        });

    }

</script>
