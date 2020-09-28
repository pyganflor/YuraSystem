<script>
    listar_camas();

    function listar_camas() {
        datos = {};
        $.LoadingOverlay('show');
        $.get('{{url('camas_ciclos/listar_camas')}}', datos, function (retorno) {
            $('#listado_camas').html(retorno);
            estructura_tabla('table_camas', false, true);
            //$('#table_camas_filter').remove();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_cama() {
        datos = {};
        $.LoadingOverlay('show');
        $.get('{{url('camas_ciclos/add_cama')}}', datos, function (retorno) {
            $('#div_form_add_cama').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function edit_cama(id) {
        datos = {
            id: id
        };
        $.LoadingOverlay('show');
        $.get('{{url('camas_ciclos/edit_cama')}}', datos, function (retorno) {
            $('#div_form_add_cama').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>