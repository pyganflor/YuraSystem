<script>
    function add_intervalo(id_indicador) {
        datos = {
            id_indicador : id_indicador
        };
        $.LoadingOverlay('show');
        $.get('{{url('intervalo_indicador/add_intervalo')}}', datos, function (retorno) {
            modal_form('modal_add_intervalos_indicadores', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir intervalos', true, false, '{{isPC() ? '50%' : ''}}', function () {
                store_intervalo();
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
                $("#alert_intervalo").addClass('hide');
        });
        $.LoadingOverlay('hide');
    }
    
    function delete_row(id) {
        cant = $("form#form_add_intervalo div.row").length;
        if(cant>1)
            $("#row_input_"+id).remove()
    }

   function store_intervalo() {
        if($("#form_add_intervalo").valid()){
           $.LoadingOverlay('show');
           datos=[];
           $.each($("form#form_add_intervalo div.row"),function (i,j) {
                if($(j).find('.tipo').val() === "I"){
                    datos.push({
                        tipo: $(j).find(".tipo").val(),
                        desde: $(j).find('.desde').val(),
                        hasta: $(j).find('.hasta').val(),
                        color :$(j).find('.color').val()
                    });
                }else{
                    datos.push({
                        tipo: $(j).find(".tipo").val(),
                        condicional: $(j).find('.condicional').val(),
                        hasta: $(j).find('.hasta').val(),
                        color :$(j).find('.color').val()
                    });
                }
               datos.push({
                   id_indicador : $("#id_indicador").val()
               });
           });
           console.log(datos);
           post_jquery('{{url('intervalo_indicador/store_intervalo')}}', datos, function () {
               cerrar_modals();
           });
           $.LoadingOverlay('hide');
        }
   }
</script>
