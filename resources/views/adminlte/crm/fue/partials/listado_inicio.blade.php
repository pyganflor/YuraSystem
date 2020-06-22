<div class="box-body" id="div_content_datos_exportacion">
    <table width="100%">
        <tr>
            <td>
                <table style="width:100%">
                    <tr>
                        <td>
                            <label > Cliente</label><br />
                            <select id="id_cliente" name="id_cliente" class="form-control">
                                <option value="">Seleccione un cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{$cliente->id_cliente}}">{{$cliente->detalle()->nombre}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <label > Código dae</label><br />
                            <input type="text" class="form-control"id="filtro_codigo_dae" value="" name="filtro_codigo_dae">
                        </td>
                        <td>
                            <label >Dae completa</label><br />
                            <input type="text" class="form-control" id="filtro_dae_completa" value="" name="filtro_dae_completa" placeholder="">
                        </td>
                        <td>
                            <label > Guía madre</label><br />
                            <input type="text" class="form-control" id="filtro_guia_madre" value="" name="filtro_guia_madre" placeholder="">
                        </td>
                        <td>
                             <label > Fecha desde</label><br />
                            <input type="date" class="form-control" id="filtro_desde" value="{{now()->toDateString()}}" name="filtro_desde">
                        </td>
                        <td>
                             <label > Fecha hasta</label><br />
                            <input type="date" class="form-control" id="filtro_hasta" name="filtro_hasta" value="{{\Carbon\Carbon::parse(now()->toDateString())->addDay(1)->toDateString()}}" >
                        </td>
                        <td>
                             <label style="visibility: hidden"> .</label><br />
                            <button class="btn btn-default" onclick="busqueda_filtro()" title="Buscar">
                                <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i>
                            </button>
                        </td>
                        <td>
                            <label style="visibility: hidden"> .</label><br />
                            <button class="btn btn-primary" onclick="generar_reporte(true)" title="Generar reporte">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Generar excel
                            </button>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
    <div id="div_listado_datos_exportacion"></div>
</div>
<script>
    busqueda_filtro();

    function busqueda_filtro(){
        $.LoadingOverlay('show');
        datos = {
            id_cliente : $("#id_cliente").val(),
            codigo_dae : $("#filtro_codigo_dae").val(),
            guia_madre : $("#filtro_guia_madre").val(),
            dae : $("#filtro_dae_completa").val(),
            desde : $("#filtro_desde").val(),
            hasta : $("#filtro_hasta").val(),
        };
        $.get('{{url('fue/reporte_fue_filtrado')}}',datos, function (retorno) {
            $("#div_listado_datos_exportacion").html(retorno);
           // estructura_tabla('table_content_factura',false);
            $("#table_content_factura_wrapper .col-sm-6").addClass('hide');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function generar_reporte(){
        $.LoadingOverlay('show');
        $.ajax({
            type: "POST",
            dataType: "html",
            contentType: "application/x-www-form-urlencoded",
            url: '{{url('fue/exportar_reporte_dae')}}',
            data: {
                id_cliente : $("#id_cliente").val(),
                codigo_dae : $("#filtro_codigo_dae").val(),
                guia_madre : $("#filtro_guia_madre").val(),
                dae : $("#filtro_dae_completa").val(),
                desde : $("#filtro_desde").val(),
                hasta : $("#filtro_hasta").val(),
                _token: '{{csrf_token()}}',
            },
            success: function (data) {
                var opResult = JSON.parse(data);
                var $a = $("<a>");
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Reporte Facturas por DAE.xlsx");
                $a[0].click();
                $a.remove();
            }
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>
