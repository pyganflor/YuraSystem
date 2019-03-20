<p class="text-center">
    <strong>Cosecha del día</strong>
</p>
@if(count($listado_variedades) > 0)
    @foreach($listado_variedades as $variedad)
        <div class="progress-group" style="margin-bottom: 0">
            <span class="progress-text">{{$variedad->nombre}}</span>
            <span class="progress-number"><strong title="Clasificados">{{$verde->tallos_x_variedad($variedad->id_variedad)}}</strong> /
            <span title="Cosechados">
                {{$verde->total_tallos_recepcionByVariedad($variedad->id_variedad)}}
            </span>
        </span>
            <div class="progress sm" style="margin-bottom: 5px">
                @php
                    $porcentaje = round((($verde->tallos_x_variedad($variedad->id_variedad) * 100) /
                         $verde->total_tallos_recepcionByVariedad($variedad->id_variedad)), 2);
                if ($porcentaje >= 90)
                $class_bar = 'green';
                elseif ($porcentaje >= 80)
                $class_bar = 'warning';
                else
                $class_bar = 'red';
                @endphp
                <div class="progress-bar progress-bar-{{$class_bar}}" title="{{$porcentaje}}%"
                     style="width: {{$porcentaje}}%"></div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info text-center">No se han encontrado resultados en el rango de fecha indicado</div>
@endif