<div class="box-body" id="div_content_datos_exportacion">
    <table width="100%">
        <tr>
            <td>
                <div class="form-group input-group" style="padding: 0px">
                    <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_datos_exportacion" name="busqueda_datos_exportacion">
                    <span class="input-group-btn">
                        <button class="btn btn-default" onclick="buscar_listado()"
                                onmouseover="$('#title_btn_buscar').html('Buscar')"
                                onmouseleave="$('#title_btn_buscar').html('')">
                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em id="title_btn_buscar"></em>
                        </button>
                    </span>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" onclick="add_marca()"
                                onmouseover="$('#title_btn_add').html('Añadir')"
                                onmouseleave="$('#title_btn_add').html('')">
                            <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em id="title_btn_add"></em>
                        </button>
                    </span>
                </div>
            </td>
        </tr>
    </table>
    <div id="div_listado_datos_exportacion"></div>
</div>
<script>
    busqueda_filtro($("#id_cliente").val(),$("#codigo_dae").val(),$("#guia_madre").val(),$("#dae").val());

    function busqueda_filtro(id_cliente,codigo_dae,guia_madre,dae){
        $.LoadingOverlay('show');
        datos = {
            id_cliente : id_cliente,
            codigo_dae : codigo_dae,
            guia_madre : guia_madre,
            dae : dae
        };
        $.get('{{url('fue/reporte_fue_filtrado')}}',datos, function (retorno) {
            $("#div_listado_datos_exportacion").html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>
