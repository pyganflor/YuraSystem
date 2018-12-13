<script>
    //buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            fecha_desde: $('#fecha_desde_search').val().trim(),
            fecha_hasta: $('#fecha_hasta_search').val(),
            variedad: $('#variedad_search').val(),
            unitaria: $('#unitaria_search').val(),
            clasificacion_ramo: $('#clasificacion_ramo_search').val(),
        };
        $.get('{{url('apertura/buscar_aperturas')}}', datos, function (retorno) {
            $('#div_listado_aperturas').html(retorno);
            estructura_tabla('table_content_aperturas');
            buscar_pedidos();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function exportar_aperturas() {
        $.LoadingOverlay('show');
        window.open('{{url('apertura/exportar_aperturas')}}' + '?busqueda=' + $('#busqueda_aperturas').val().trim() +
            '&fecha_ingreso=' + $('#fecha_ingreso_search').val() +
            '&proceso=' + $('#proceso_search').val() +
            '&anno=' + $('#anno_search').val(), '_blank');
        $.LoadingOverlay('hide');
    }

    function mouseOver(tr, id) {
        tr.css('background-color', '#add8e6');
        $('#table_desglose_apertura_' + id).addClass('sombra_estandar');
        $('#table_desglose_apertura_' + id).css('border', '3px solid #add8e6');
    }

    function mouseLeave(tr, id) {
        tr.css('background-color', '');
        $('#table_desglose_apertura_' + id).removeClass('sombra_estandar');
        $('#table_desglose_apertura_' + id).css('border', '1px solid #add8e6');
    }

    function buscar_pedidos(){
        datos = {
            fecha: $('#fecha_pedidos').val()
        };
        get_jquery('{{url('apertura/listar_pedidos')}}',datos,function(retorno){
            $('#div_listado_pedidos').html(retorno);
        });
    }
</script>