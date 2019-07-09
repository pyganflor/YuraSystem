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
            modal_view('modal_etiquetas_factura', retorno, '<i class="fa fa-file-excel-o"></i> <b>Etiquetas</b> ', true, false, '{{isPC() ? '80%' : ''}}', function () {
                $.LoadingOverlay('hide');
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function filas(id_comprobante){
        $.LoadingOverlay('show');
        datos = {
            filas: $("#filas").val(),
            id_comprobante : id_comprobante
        };
        $.get('{{url('etiqueta_factura/campos_etiqueta')}}', datos, function (retorno) {
            $("tbody#tbody").empty().append(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function select_check_etiqueta_factura(check){
        id = check.id.split("_");
        id_btn = id[1];
        label=[];

        $.each($("div#btn_presentaciones_"+id_btn+" ul li input[type='checkbox']"),function (i,j) {
            if($(j).is(":checked")){
                $.each($("div#btn_presentaciones_"+id_btn+" ul li td.td_"+id_btn+"_"+(i+1)+" label"),function (l,m) {
                    label.push({
                        texto :$(m).html(),
                        id_det_esp_emp : m.id
                    });
                });
            }
        });
        id_det_esp_emp = "";
        $("#span_presentaciones_"+id_btn).empty();
        $.each(label,function (n,o) {
            $("#span_presentaciones_"+id_btn).append("<br/>"+o.texto.trim());
            id_det_esp_emp += o.id_det_esp_emp+"|";
        });
        $("#ids_det_esp_emp_"+id_btn).val(id_det_esp_emp);
    }

    function store_etiquetas_factura(id_comprobante,csrf_token){
        $.LoadingOverlay('show');

        data = [];
        $.each($("input.cajas"),function(i,j){
            if(j.value.length > 0){
                data.push({
                    cajas : j.value,
                    siglas : $("#siglas_"+(i+1)).val(),
                    et_inicial : $("#et_inicial_"+(i+1)).val(),
                    et_final : $("#et_final_"+(i+1)).val(),
                    id_det_esp_emp : $("#ids_det_esp_emp_"+(i+1)).val(),
                    empaque :  $("#empaque_"+(i+1)).val()
                });
            }
        });

        datos = {
            _token: csrf_token,
            id_comprobante: id_comprobante,
            data : data
        };
        console.log(datos.data);
        post_jquery('etiqueta_factura/store_etiqueta_factura', datos, function () {
            cerrar_modals();
            form_etiqueta_factura(id_comprobante);
        });
        $.LoadingOverlay('hide');
    }


    function delete_etiquetas_factura(id_comprobante,csrf_token){
        datos = {
            _token: csrf_token,
            id_comprobante: id_comprobante,
        };
        modal_quest('modal_crear_especificacion', '<div class="alert alert-warning text-center"><p>Desea eliminar la etiqueta?</p></div>',
            "<i class='fa fa-cubes'></i> Seleccione una opción",true, false, '{{isPC() ? '25%' : ''}}', function () {
            post_jquery('etiqueta_factura/delete_etiqueta_factura', datos, function () {
                cerrar_modals();
                form_etiqueta_factura(id_comprobante);
            });
            $.LoadingOverlay('hide');
        });
    }
</script>
