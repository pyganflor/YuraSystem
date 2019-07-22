<div class="row">
    <div class="col-md-6">
        <table width="100%" class="table table-responsive table-bordered sombra_estandar" style="font-size: 0.8em; border-color: #9d9d9d">
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    SEMANA
                </th>
                <td style="border-color: #9d9d9d" class="text-center">{{$clasificacion->semana->codigo}}</td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA
                </th>
                <td style="border-color: #9d9d9d" class="text-center">{{substr($clasificacion->fecha_ingreso,0,16)}}</td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TALLOS RECPCIÓN
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$clasificacion->total_tallos_recepcion()}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TALLOS CLASIFICADOS
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$clasificacion->total_tallos()}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    RAMOS ESTANDAR
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$clasificacion->getTotalRamosEstandar()}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CAJAS EQ.
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{round($clasificacion->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2)}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CALIBRE
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    @if($clasificacion->getTotalRamosEstandar() > 0)
                        {{round($clasificacion->total_tallos() / $clasificacion->getTotalRamosEstandar(),2)}}
                    @else
                        0
                    @endif
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    DESECHO
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$clasificacion->desecho()}}%
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    RENDIMIENTO
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    <a href="javascript:void(0)" onclick="ver_rendimiento('{{$clasificacion->id_clasificacion_verde}}')">
                        {{$clasificacion->getRendimiento()}}
                    </a>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <div class="list-group">
            <a href="javascript:void(0)" class="list-group-item list-group-item-action active">
                Opciones
            </a>
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
               onclick="cargar_opcion('detalles_reales','{{$clasificacion->id_clasificacion_verde}}')">
                Ver detalles ingresados
            </a>
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
               onclick="cargar_opcion('detalles_estandar','{{$clasificacion->id_clasificacion_verde}}')">
                Ver detalles estandar
            </a>
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
               onclick="cargar_opcion('detalles_x_variedad','{{$clasificacion->id_clasificacion_verde}}')">
                Ver ingresos por variedad
            </a>
            {{------------------- Enlaces para documentos -------------------}}
            @if(count(getDocumentos('clasificacion_verde', $clasificacion->id_clasificacion_verde))>0)
                <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                   onclick="ver_documentos('clasificacion_verde', '{{$clasificacion->id_clasificacion_verde}}')">
                    Ver información personalizada
                </a>
            @endif
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
               onclick="add_info('{{$clasificacion->id_clasificacion_verde}}')">
                Añadir información personalizada
            </a>
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
               onclick="cargar_opcion('clasificaciones_x_fecha','{{$clasificacion->id_clasificacion_verde}}')">
                Clasificaciones por fecha
            </a>
        </div>
    </div>
</div>

@if(count(getDocumentos('clasificacion_verde', $clasificacion->id_clasificacion_verde))>0)
    <div id="content_documentos"></div>
@endif

<legend></legend>
<div id="div_content_opciones" style="margin-top: 10px"></div>

<input type="hidden" id="id_clasificacion_verde" value="{{$clasificacion->id_clasificacion_verde}}">
<script>
    function buscar_detalles_reales() {
        $.LoadingOverlay('show');
        datos = {
            id_variedad: $('#_id_variedad_search').val(),
        };
        $.get('{{url('clasificacion_verde/buscar_detalles_reales')}}', datos, function (retorno) {
            $('#div_content_detalles_reales').html(retorno);
            estructura_tabla('table_content_detalles_reales');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_detalles_reales .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?id_variedad=' + $('#_id_variedad_search').val() + '&');
        $('#div_content_detalles_reales').html($('#table_detalles_reales').html());
        $.get(url, function (resul) {
            $('#div_content_detalles_reales').html(resul);
            estructura_tabla('table_content_detalles_reales');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function buscar_detalles_estandar() {
        $.LoadingOverlay('show');
        datos = {
            id_variedad: $('#_id_variedad_search').val(),
            id_clasificacion_verde: $('#id_clasificacion_verde').val(),
        };
        $.get('{{url('clasificacion_verde/buscar_detalles_estandar')}}', datos, function (retorno) {
            $('#div_content_detalles_estandar').html(retorno);
            estructura_tabla('table_content_detalles_estandar');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_detalles_estandar .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?id_variedad=' + $('#_id_variedad_search').val() +
            '&id_clasificacion_verde=' + $('#id_clasificacion_verde').val() + '&');
        $('#div_content_detalles_estandar').html($('#table_detalles_estandar').html());
        $.get(url, function (resul) {
            $('#div_content_detalles_estandar').html(resul);
            estructura_tabla('table_content_detalles_estandar');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });
</script>