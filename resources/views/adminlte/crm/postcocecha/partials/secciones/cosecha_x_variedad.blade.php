<p class="text-center">
    <strong>Cosecha del día</strong>
    <button type="button" class="pull-right btn btn-xs btn-default" onclick="actualizar_cosecha_x_variedad()" id="btn_actualizar">
        <i class="fa fa-fw fa-refresh"></i>
    </button>
</p>
@if(count($listado_variedades) > 0)
    @foreach($listado_variedades as $variedad)
        @php
            $variedad = getVariedad($variedad->id_variedad);
            $porcentaje = 0;
            if ($verde != '')
                $porcentaje = round((($verde->tallos_x_variedad($variedad->id_variedad) * 100) /
                     $cosecha->getTotalTallosByVariedad($variedad->id_variedad)), 2);
            if ($porcentaje >= 90)
            $class_bar = 'green';
            elseif ($porcentaje >= 80)
            $class_bar = 'warning';
            else
            $class_bar = 'red';
        @endphp
        <div class="progress-group" style="margin-bottom: 0">
            <span class="progress-text">
                {{$variedad->nombre}}
                <span class="badge bg-{{$class_bar}}" title="Calibre">
                    {{$verde != '' ? $verde->calibreByVariedad($variedad->id_variedad) : 0}}
                </span>
                <input type="hidden" id="calibre_var_{{$variedad->id_variedad}}"
                       value="{{$verde != '' ? $verde->calibreByVariedad($variedad->id_variedad) : 0}}">
            </span>
            <span class="progress-number">
                <strong title="Clasificados">{{$verde != '' ? $verde->tallos_x_variedad($variedad->id_variedad) : 0}}</strong> /
                <span title="Cosechados">
                    {{$cosecha->getTotalTallosByVariedad($variedad->id_variedad)}}
                </span>

                <input type="hidden" id="clasificados_var_{{$variedad->id_variedad}}"
                       value="{{$verde != '' ? $verde->tallos_x_variedad($variedad->id_variedad) : 0}}">
                <input type="hidden" id="cosechados_var_{{$variedad->id_variedad}}"
                       value="{{$cosecha->getTotalTallosByVariedad($variedad->id_variedad)}}">
            </span>
            <div class="progress sm" style="margin-bottom: 5px">
                <div class="progress progress-sm active">
                    <div class="progress-bar progress-bar-{{$class_bar}} progress-bar-striped" role="progressbar" aria-valuenow="20"
                         aria-valuemin="0"
                         aria-valuemax="100" style="width: {{$porcentaje}}%" title="{{$porcentaje}}%">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" class="listado_variedades" value="{{$variedad->id_variedad}}">
    @endforeach
    <div class="row">
        <div class="col-sm-6 col-xs-6">
            @php
                $class_porcent = 'orange';
                $class_icon = 'left';
                if ($porcent > 0){
                    $class_porcent = 'green';
                    $class_icon = 'down';
                }else if($porcent < 0){
                    $class_porcent = 'red';
                    $class_icon = 'up';
                    $porcent = substr($porcent,1);
                }
            @endphp
            <div class="description-block border-right">
                <span class="description-percentage text-{{$class_porcent}}" title="Relación con última clasificación">
                    <i class="fa fa-caret-{{$class_icon}}"></i>
                    {{round($porcent,2)}} %
                </span>
                <input type="hidden" id="porcentaje_calibre" value="{{$porcent}}">
                <h5 class="description-header" title="Calibre">
                    {{$verde != '' ? $verde->getCalibre() : 0}}
                </h5>
                <input type="hidden" id="calibre_dia" value="{{$verde != '' ? $verde->getCalibre() : 0}}">
                <button type="button" class="btn btn-link btn-xs" title="Ver Rendimiento en Verde"
                        onclick="ver_rendimiento_verde('{{$verde != '' ? $verde->id_clasificacion_verde : ''}}')">
                    <strong class="description-text">Calibre</strong>
                </button>
            </div>
        </div>
        <div class="col-sm-6 col-xs-6">
            <div class="description-block border-right">
                <span class="description-percentage" title="Clasificados">
                    {{$verde != '' ? number_format($verde->total_tallos()) : 0}}
                </span>
                <input type="hidden" id="clasificados_dia" value="{{$verde != '' ? $verde->total_tallos() : 0}}">
                <h5 class="description-header" title="Cosechados">
                    {{number_format($cosecha->getTotalTallos())}}
                </h5>
                <input type="hidden" id="cosechados_dia" value="{{$cosecha->getTotalTallos()}}">
                <button type="button" class="btn btn-link btn-xs" title="Ver Rendimiento en Cosecha"
                        onclick="ver_rendimiento_cosecha('{{$cosecha != '' ? $cosecha->id_cosecha : ''}}')">
                    <strong class="description-text">Totales</strong>
                </button>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">No se han encontrado resultados en el rango de fecha indicado</div>
@endif