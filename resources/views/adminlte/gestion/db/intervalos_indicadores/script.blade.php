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
        cant = $("form#form_add_intervalo div.row").length+1;
        datos = {
            inputs: inputs,
            cant : cant
        };
        $.get('{{url('intervalo_indicador/add_row_intervalo')}}', datos, function (retorno) {
            $("#form_add_intervalo").append(retorno);
            if(cant>0)
                $("#alert_intervalo").addClass('hidde');
        });
        $.LoadingOverlay('hide');
    }
    
    function delete_row(id) {
        $("#"+id).remove()
    }

    function cambia_color(id,select) {
        $("span#color_"+id).css('background',$(select).val())
    }
</script>
