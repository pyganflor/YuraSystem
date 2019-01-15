<div class="row">
    <div class="col-md-7">
        <table width="100%" class="table table-responsive table-bordered sombra_estandar" style="font-size: 0.8em; border-color: #9d9d9d">
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" colspan="2">
                    VARIEDAD
                </th>
                <td style="border-color: #9d9d9d" class="text-center">{{$lote->variedad->nombre}}</td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" colspan="2">
                    CALIBRE
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{explode('|',$lote->clasificacion_unitaria->nombre)[0]}}
                    {{$lote->variedad->unidad_de_medida}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" colspan="2">
                    FECHA INGRESO
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$lote->getCurrentFecha()}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" colspan="2">
                    SEMANA
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{getSemanaByDateVariedad($lote->getCurrentFecha(), $lote->variedad->id_variedad)->codigo}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    RAMOS
                </th>
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <select name="en_tiempo_search" id="en_tiempo_search"
                            onchange="calcular_calibre_ramo($(this).val(), '{{$lote->cantidad_tallos}}', '{{explode('|',$lote->clasificacion_unitaria->nombre)[0]}}')">
                        <option value="">Calibre</option>
                        @foreach(getCalibresRamo() as $ramo)
                            <option value="{{$ramo->nombre}}">
                                {{$ramo->nombre}} {{$lote->variedad->unidad_de_medida}}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td style="border-color: #9d9d9d" class="text-center" id="td_content_ramos">
                    {{round(getLoteREById($lote->id_lote_re)->cantidad_tallos /
                            explode('|',getLoteREById($lote->id_lote_re)->clasificacion_unitaria->nombre)[1],2)}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" colspan="2">
                    TALLOS
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$lote->cantidad_tallos}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" colspan="2">
                    ETAPA
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{getLoteREById($lote->id_lote_re)->getCurrentEtapa()}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d" colspan="2">
                    ESTANCIA
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    <span class="badge" title="Días estimados">
                        {{$lote->getCurrentDiasEstimados()}}
                    </span>
                    <span class="badge" style="background-color: {{$lote->getCurrentEstancia() >= getVariedad($lote->id_variedad)->maximo_apertura
                              ? '#ce8483' : '#357ca5'}}" title="Días reales">
                        {{$lote->getCurrentEstancia()}}
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-5">
        <div class="list-group">
            <a href="javascript:void(0)" class="list-group-item list-group-item-action active">
                Opciones
            </a>
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
               onclick="cargar_opcion('etapas','{{$lote->id_lote_re}}')">
                Etapas del lote
            </a>
            {{------------------- Enlaces para documentos -------------------}}
            @if(count(getDocumentos('lote_re', $lote->id_lote_re))>0)
                <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                   onclick="ver_documentos('lote_re', '{{$lote->id_lote_re}}')">
                    Ver información personalizada
                </a>
            @endif
            <a href="#div_content_opciones" class="list-group-item list-group-item-action"
               onclick="add_info('{{$lote->id_lote_re}}')">
                Añadir información personalizada
            </a>
        </div>
    </div>
</div>

<input type="hidden" id="id_lote_re" value="{{$lote->id_lote_re}}">

@if(count(getDocumentos('lote_re', $lote->id_lote_re))>0)
    <div id="content_documentos"></div>
@endif

<legend></legend>
<div id="div_content_opciones" style="margin-top: 10px"></div>

<script>
    function calcular_calibre_ramo(calibre, tallos, unitaria) {
        f = Math.ceil((calibre / unitaria) * 100) / 100;
        valor = Math.ceil((tallos / f) * 100) / 100;
        $('#td_content_ramos').html(valor);
    }
</script>