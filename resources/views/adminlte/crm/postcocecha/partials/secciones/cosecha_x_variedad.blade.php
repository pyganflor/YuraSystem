<p class="text-center">
    <strong>Cosecha del día</strong>
    <button type="button" class="pull-right btn btn-xs btn-default" onclick="actualizar_cosecha_x_variedad()">
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
            </span>
            <span class="progress-number">
                <strong title="Clasificados">{{$verde != '' ? $verde->tallos_x_variedad($variedad->id_variedad) : 0}}</strong> /
                <span title="Cosechados">
                    {{$cosecha->getTotalTallosByVariedad($variedad->id_variedad)}}
                </span>
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
    @endforeach
    <div class="row">
        <div class="col-sm-3 col-xs-6">
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
                <span class="description-percentage text-{{$class_porcent}}">
                    <i class="fa fa-caret-{{$class_icon}}"></i>
                    {{$porcent}} %
                </span>
                <h5 class="description-header">
                    {{$verde != '' ? $verde->getCalibre() : 0}}
                </h5>
                <a href="javascript:void(0)" class="btn btn-link"
                   onclick="ver_rendimiento_verde('{{$verde != '' ? $verde->id_clasificacion_verde : ''}}')">
                    <strong class="description-text">Calibre</strong>
                </a>
            </div>
        </div>
        <div class="col-sm-3 col-xs-6">
            <div class="description-block border-right">
                <span class="description-percentage" title="Clasificados">
                    {{$verde != '' ? $verde->total_tallos() : 0}}
                </span>
                <h5 class="description-header" title="Cosechados">
                    {{$cosecha->getTotalTallos()}}
                </h5>
                <a href="javascript:void(0)" class="btn btn-link"
                   onclick="ver_rendimiento_cosecha('{{$cosecha != '' ? $cosecha->id_cosecha : ''}}')">
                    <strong class="description-text">Totales</strong>
                </a>
            </div>
        </div>

    </div>
@else
    <div class="alert alert-info text-center">No se han encontrado resultados en el rango de fecha indicado</div>
@endif