<p class="text-center">
    <strong>Cosecha por variedades</strong>
    <a href="javascript:void(0)" class="pull-right btn btn-xs" onclick="actualizar_cosecha_x_variedad()">
        <i class="fa fa-fw fa-refresh"></i>
    </a>
</p>
@if(count($listado_variedades) > 0)
    @foreach($listado_variedades as $item)
        <div class="progress-group" style="margin-bottom: 0">
            <span class="progress-text">{{$item['variedad']->nombre}}</span>
            <span class="progress-number"><strong title="Clasificados">{{$item['clasificacion']}}</strong> /
            <span title="Cosechados">
                {{$item['cosecha']}}
            </span>
        </span>
            <div class="progress sm" style="margin-bottom: 5px">
                @php
                    $porcentaje = 0;
                    if ($item['cosecha'] > 0)
                        $porcentaje = round((($item['clasificacion'] * 100) /
                             $item['cosecha']), 2);
                    if ($porcentaje >= 90)
                    $class_bar = 'green';
                    elseif ($porcentaje >= 80)
                    $class_bar = 'warning';
                    else
                    $class_bar = 'red';
                @endphp
                <div class="progress progress-sm active">
                    <div class="progress-bar progress-bar-{{$class_bar}} progress-bar-striped" role="progressbar" aria-valuenow="20"
                         aria-valuemin="0"
                         aria-valuemax="100" style="width: {{$porcentaje}}%" title="{{$porcentaje}}%">
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info text-center">No se han encontrado resultados en el rango de fecha indicado</div>
@endif