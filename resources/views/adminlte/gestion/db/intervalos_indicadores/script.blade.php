<script>
    function add_intervalo(id_indicador) {
        datos = {
            id_indicador : id_indicador
        };
        $.LoadingOverlay('show');
        $.get('{{url('intervalo_indicador/add_intervalo')}}', datos, function (retorno) {
            modal_form('modal_add_intervalos_indicadores', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir intervalos', true, false, '{{isPC() ? '60%' : ''}}', function () {
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }

    function add_row(inputs){
        $.LoadingOverlay('show');
        datos = {
            inputs: inputs,
            cant : $("form#form_add_intervalo div.row").length+1
        };
        $.get('{{url('intervalo_indicador/add_row_intervalo')}}', datos, function (retorno) {
            $("#form_add_intervalo").append(retorno);
        });
        $.LoadingOverlay('hide');
    }

    function cambia_color(id,select) {
        $("span#color_"+id).attr('background',$(select).val())
    }
</script>
