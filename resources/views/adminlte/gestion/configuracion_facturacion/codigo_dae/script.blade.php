<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_codigo_dae').val().trim(),
        };
        $.get('{{url('codigo_dae/buscar')}}', datos, function (retorno) {
            $('#div_listado_codigo_dae').html(retorno);
           // estructura_tabla('table_content_codigo_dae');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_codigo_dae .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=' + $('#busqueda_codigo_dae').val() + '&');
        $('#div_listado_codigo_dae').html($('#table_codigo_dae').html());
        $.get(url, function (resul) {
            console.log(resul);
            $('#div_listado_codigo_dae').html(resul);
            estructura_tabla('table_content_codigo_dae');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function add_codigo_dae() {
        $.LoadingOverlay('show');
        $.get('{{url('codigo_dae/seleccionar_pais')}}', datos, function (retorno) {
            modal_form('modal_add_marca', retorno, '<i class="fa fa-fw fa-plus"></i> Seleccionar paises', true, false, '{{isPC() ? '80%' : ''}}', function () {
                exel_paises();
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }

    function buscar_pais(value) {

        $.LoadingOverlay('show');
        datos = {
            nombre : $("#nombre_pais").val()
        };
        $.get('{{url('codigo_dae/busqueda_pais_modal')}}', datos, function (retorno) {
            $("div#paises").html(retorno);
            $('input[name="codigo_pais_selected"]').each(function (i,j) {
                $("#codigo_pais_"+j.value).attr('checked',true);
            })
        });
        $.LoadingOverlay('hide');

    }

    function selected(input) {
        if($("#"+input.id).is(':checked')) {
            var datos = {
                codigo : input.value
            };
            $.get('{{url('codigo_dae/pais')}}', datos, function (retorno) {
                $("#paises_selected").append("" +
                    "<a href='javascript:void(0)' id='" + retorno.codigo + "' style='padding:5px 15px' class='list-group-item list-group-item-action'>" +
                    "<input type='hidden' id='codigo_pais_selected' name='codigo_pais_selected' value='" + retorno.codigo + "'> " +
                    "" + retorno.nombre + "" +
                    "</a>");
            });
        }else{
            $("#"+input.id.split("_")[2]).remove();
        }
    }

    function exel_paises(){

        if($('input[name="codigo_pais_selected"]').length === 0){
            modal_view('modal_view_msg_pais_selected',
                '<div class="alert text-center  alert-warning"><p>Debe seleccionar al menos país para generar el archivo excel</p></div>',
                '<i class="fa fa-times" aria-hidden="true"></i> Paises seleccionados', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        $.LoadingOverlay('show');
        arreglo = [];
        $('input[name="codigo_pais_selected"]').each(function (i,j) {
            arreglo.push(j.value);
        });

        datos = {
            arreglo : arreglo
        };
        $.ajax({
            type: "POST",
            dataType: "html",
            contentType: "application/x-www-form-urlencoded",
            url: '{{url('codigo_dae/exportar_paises')}}',
            data: {
                arreglo : arreglo,
                _token: '{{csrf_token()}}'
            },
            success: function (data) {
                var opResult = JSON.parse(data);
                var $a = $("<a>");
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "codigos DAE.xlsx");
                $a[0].click();
                $a.remove();
                $.LoadingOverlay('hide');
            }
        });
    }

    function subir_codigo_dae() {
        $.LoadingOverlay('show');
        $.get('{{url('codigo_dae/form_file_codigo_dae')}}', datos, function (retorno) {
            modal_form('modal_upload_file_codigo_dae', retorno, '<i class="fa fa-file-excel-o"></i> Seleccionar el archivo Excel', true, false, '{{isPC() ? '40%' : ''}}', function () {
                importar_codigo_dae();
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }
    
    function  importar_codigo_dae() {
        if($("#form_add_codigo_dae").valid()){
            var formData = new FormData($("#form_add_codigo_dae")[0]);
            formData.append('_token','{{csrf_token()}}');
            $.ajax({
                url: '{{url('codigo_dae/importar_codigo_dae')}}',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(retorno){
                    modal_view('modal_view_codigo_dae', retorno, '<i class="fa fa-fw fa-table"></i> Codigo DAE', true, false,
                        '{{isPC() ? '50%' : ''}}');
                    buscar_listado();
                }
            });
        }
    }

    function desactivar_codigo(id_codigo){
        modal_quest('modal_update_estado_codigo_dae', '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este código DAE? </div>', '<i class="fa fa-fw fa-trash"></i> Desactivar código DAE', true, false, '{{isPC() ? '40%' : ''}}', function () {
            $.LoadingOverlay('show');
            var datos = {
                _token: '{{csrf_token()}}',
                id_codigo: id_codigo
            };
            post_jquery('{{url('codigo_dae/descactivar_codigo')}}', datos, function (retorno) {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        });
    }



</script>
