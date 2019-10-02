<script>
    listado_pedido_factura_generada();

    function listado_pedido_factura_generada(){
        $.LoadingOverlay('show');
        datos = {
            id_configuracion_empresa : $("#id_configuracion_empresa_orden_factura").val(),
            fecha_desde : $("#fecha_desde").val(),
            fecha_hasta : $("#fecha_hasta").val()
        };
        $.get('{{url('orden_factura/buscar_pedido_facturada_generada')}}', datos, function (retorno) {
            $('#div_content_orden_factura').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

   function update_orden_factura(){
       arr_comprobante = [];
       $.each($("td.orden_factura"), function (i, j) {
           $.each($("td#" + j.id + " div"), function (k, l) {
               secuencial = $("td#" + j.id + " div input#n_factura").val();
               id_comprobante = $("input#id_comprobante_" + j.id.split("_")[2]).val();
               arr_comprobante.push({
                   secuencial: secuencial,
                   id_comprobante: id_comprobante
               });
           });
       });
       if(arr_comprobante.length  === 0){
           modal_view('modal_view_msg_factura',
               '<div class="alert text-center  alert-warning"><p><i class="fa fa-fw fa-exclamation-triangle"></i> Debes ordenar al menos una factura para poder actualizar</p></div>',
               '<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i> Orden facturas', true, false, '{{isPC() ? '40%' : ''}}');
           return false;
       }

       modal_quest('modal_quest_actualizar_secuencial', '<div class="alert alert-info text-center">' +
           '¿Esta seguro que desea actualizar los número de estas facturas?</div>',
           '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '40%', function () {
               datos ={
                   arr_comprobante : arr_comprobante,
                   _token : '{{csrf_token()}}'
               };

               post_jquery('orden_factura/update_secuencial_factura', datos, function () {
                   listado_pedido_factura_generada();
                   cerrar_modals();
               });
           })
    }

</script>
