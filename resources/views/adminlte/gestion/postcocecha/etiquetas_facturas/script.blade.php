<script>
    listado_etiquetas();

    function listado_etiquetas(){
        $.LoadingOverlay('show');
        datos = {
            desde: $('#desde').val(),
            hasta: $('#hasta').val(),
        };
        $.get('{{url('etiqueta_factura/listado')}}', datos, function (retorno) {
            $('#div_listado_etiquetas').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function form_etiqueta_factura(id_comprobante){
        $.LoadingOverlay('show');
        datos = {
            id_comprobante: id_comprobante
        };
        $.get('{{url('etiqueta_factura/form_etiqueta')}}', datos, function (retorno) {
            modal_view('modal_etiquetas_factura', retorno, '<i class="fa fa-file-excel-o"></i> <b>Etiquetas</b> ', true, false, '{{isPC() ? '50%' : ''}}', function () {
                $.LoadingOverlay('hide');
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function filas(){
        $.LoadingOverlay('show');
        datos = {
            filas: $("#filas").val()
        };
        $.get('{{url('etiqueta_factura/campos_etiqueta')}}', datos, function (retorno) {
            $("tbody#tbody").empty().append(retorno);;
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }


    function store_etiquetas_factura(){

    }

</script>
