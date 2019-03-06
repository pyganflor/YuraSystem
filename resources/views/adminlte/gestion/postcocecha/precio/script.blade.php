<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
       /* datos = {
            busqueda: $('#busqueda_marcas').val().trim(),
        };*/
        $.get('{{url('precio/buscar')}}', {}/*datos*/, function (retorno) {
            $('#div_content_precio').html(retorno);
            estructura_tabla('table_content_marcas');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function precio_especificacion(id_especificacion){
        datos= {
            id_especificacion : id_especificacion
        };
        $.get('{{url('precio/form_asignar_precio')}}', datos, function (retorno) {
            modal_form('modal_precio_especificaciones', retorno, '<i class="fa fa-user-plus" aria-hidden="true"></i> Asignar precio a la especificación', true, false, '{{isPC() ? '40%' : ''}}', function () {
                store_precio_especificacion(id_especificacion);
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_precio_especificacion(id_especificacion) {
        if ($('#form_add_precio').valid()) {
            arrPrecios= [];
            $.each($('select[name=id_cliente]'), function (i, j) {
                arrPrecios.push({
                    'id_cliente' : $("#id_cliente_"+(i+1)).val(),
                    'precio' : $("#precio_"+(i+1)).val(),
                    'id_cliente_pedido_especificacion' : $("#id_cliente_pedido_especificacion_"+(i+1)).val()
                });
            });
            datos = {
                arrPrecios : arrPrecios,
                id_especificacion :  id_especificacion
            };
            post_jquery('{{url('clientes/store')}}', datos, function () {

            });
            $.LoadingOverlay('hide');
        }
    }

    function add_input() {
        $("#btn_add_input").attr('disabled',true);
        cant_rows = $('select[name=id_cliente]').length;
        console.log(cant_rows);
        $.get('{{url('precio/add_input')}}', datos, function (retorno) {
            $("#form_add_precio").append(
                " <div class='row'  id='row_"+(cant_rows+1)+"'>"+
                "    <input type='hidden' id='id_cliente_pedido_especificacion_"+(cant_rows+1)+"'>" +
                "    <div class='col-md-8'>" +
                "        <div class='form-group'>" +
                "             <label for='nombre_marca'>Cliente</label>" +
                "             <select id='id_cliente_"+(cant_rows+1)+"' name='id_cliente' class='form-control' required>" +
                "                 <option disabled selected> Seleccione </option>" +
                "             </select>" +
                "        </div>" +
                "    </div>" +
                "    <div class='col-md-4'>" +
                "        <div class='form-group'>" +
                "            <label for='identificacion'>Precio</label>" +
                "            <input type='number' id='precio_"+(cant_rows+1)+"' name='precio' class='form-control' min='1' value='' required>" +
                "        </div>" +
                "    </div>" +
                "</div>");
                for(var x=0;x<retorno.length;x++){
                    $("#id_cliente_"+(cant_rows+1)).append("<option value='"+retorno[x].id_cliente+"'> "+retorno[x].nombre+" </option>");
                }
        }).always(function () {
            $.LoadingOverlay('hide');
            $("#btn_add_input").attr('disabled',false);
        });
    }

    function delete_input(cant_exist) {
        cant_rows = $('select[name=id_cliente]').length;
        if(cant_rows > cant_exist){
            $('#row_'+cant_rows).remove();
        }
    }
</script>
