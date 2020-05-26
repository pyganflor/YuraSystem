@if(count($variedades) > 0)
    <div class="box-group" id="accordion">
        @php
            $grafica = [];
        @endphp
        @foreach($variedades as $pos_var => $variedad)
            @php
                $total_variedad = [];
            @endphp
            <div class="panel box box-success">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$variedad['variedad']->id_variedad}}"
                           aria-expanded="false" class="collapsed"
                           style="">
                            {{$variedad['variedad']->planta->nombre}} - {{$variedad['variedad']->nombre}}
                        </a>
                    </h4>
                </div>
                <div id="collapse{{$variedad['variedad']->id_variedad}}" class="panel-collapse collapse">
                    <div class="box-body" style="overflow-x: scroll">
                        <table class="table-striped table-bordered" width="100%" style="border: 1px solid #9d9d9d"
                               id="table_variedad_{{$variedad['variedad']->id_variedad}}">
                            <thead>
                            <tr>
                                <th class="text-left background-color_yura"
                                    style="border-color: #9d9d9d; color: white">
                                    Módulo
                                </th>
                                <th class="text-left background-color_yura"
                                    style="border-color: #9d9d9d; color: white">
                                    Fecha Inicio
                                </th>
                                <th class="text-left background-color_yura"
                                    style="border-color: #9d9d9d; color: white">
                                    Semana Inicio
                                </th>
                                @foreach($semanas as $pos_sem => $semana)
                                    <th class="text-left"
                                        style="border-color: #9d9d9d; background-color: #e9ecef">
                                        {{$semana->codigo}}
                                    </th>
                                    @php
                                        $total_variedad[] = 0;
                                    @endphp
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($variedad['ciclos'] as $ciclo)
                                <tr>
                                    <td class="text-left" style="border-color: #9d9d9d">
                                        {{$ciclo['ciclo']->modulo->nombre}}
                                    </td>
                                    <td class="text-left" style="border-color: #9d9d9d">
                                        {{$ciclo['ciclo']->fecha_inicio}}
                                    </td>
                                    <td class="text-left" style="border-color: #9d9d9d">
                                        {{getSemanaByDate($ciclo['ciclo']->fecha_inicio)->codigo}}
                                    </td>
                                    @foreach($ciclo['areas'] as $pos_area => $area)
                                        <td class="text-left" style="border-color: #9d9d9d; background-color: #e9ecef"
                                            title="Final: '{{$ciclo['ciclo']->fecha_fin}}'">
                                            {{number_format($area, 2)}}
                                        </td>
                                        @php
                                            $total_variedad[$pos_area] += $area;
                                        @endphp
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                            <tr>
                                <th class="text-left background-color_yura"
                                    style="border-color: #9d9d9d; color: white" colspan="3">
                                    Total
                                </th>
                                @foreach($total_variedad as $valor)
                                    <th class="text-left"
                                        style="border-color: #9d9d9d; background-color: #e9ecef">
                                        {{number_format(round($valor / 10000, 2), 2)}}  {{--convertir a hectareas--}}
                                    </th>
                                @endforeach
                                @php
                                    array_push($grafica, [
                                        'variedad' => $variedad['variedad'],
                                        'valores' => $total_variedad
                                    ]);
                                @endphp
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        {{-- TABLA TOTAL --}}
        @php
            $totales_semanas = [];
        @endphp
        <div class="panel box box-success">
            <div class="box-header with-border text-right">
                <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTablaTotal"
                       aria-expanded="false" class="collapsed text-black">
                        <i class="fa fa-fw fa-table"></i> TABLA
                    </a>
                </h4>
            </div>
            <div id="collapseTablaTotal" class="panel-collapse collapse">
                <div class="box-body" style="overflow-x: scroll">
                    <table class="table-striped table-bordered" width="100%" style="border: 1px solid #9d9d9d"
                           id="table_total">
                        <thead>
                        <tr>
                            <th class="text-left background-color_yura"
                                style="border-color: #9d9d9d; color: white">
                                Variedad
                            </th>
                            @foreach($semanas as $pos_sem => $semana)
                                <th class="text-left"
                                    style="border-color: #9d9d9d; background-color: #e9ecef">
                                    {{$semana->codigo}}
                                </th>
                                @php
                                    $totales_semanas[] = 0;
                                @endphp
                            @endforeach
                            <th class="text-center background-color_yura"
                                style="border-color: #9d9d9d; color: white">
                                Promedio
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($variedades as $pos => $var)
                            @php
                                $total_parcial = 0;
                            @endphp
                            <tr>
                                <td class="text-left" style="border-color: #9d9d9d">
                                    {{$var['variedad']->nombre}}
                                </td>
                                @foreach($grafica[$pos]['valores'] as $pos_area => $area)
                                    <td class="text-left" style="border-color: #9d9d9d; background-color: #e9ecef">
                                        {{number_format(round($area / 10000, 2), 2)}}
                                    </td>
                                    @php
                                        $totales_semanas[$pos_area] += $area;
                                        $total_parcial += $area;
                                    @endphp
                                @endforeach
                                <th class="text-center" style="border-color: #9d9d9d">
                                    {{number_format(round(($total_parcial / 10000) / count($semanas), 2), 2)}}
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                        <tr>
                            <th class="text-left background-color_yura"
                                style="border-color: #9d9d9d; color: white">
                                Total
                            </th>
                            @php
                                $total_parcial = 0;
                            @endphp
                            @foreach($totales_semanas as $valor)
                                <th class="text-left"
                                    style="border-color: #9d9d9d; background-color: #e9ecef">
                                    {{number_format(round($valor / 10000, 2), 2)}} {{--convertir a hectareas--}}
                                </th>
                                @php
                                    $total_parcial += $valor;
                                @endphp
                            @endforeach
                            <th class="text-center background-color_yura"
                                style="border-color: #9d9d9d; color: white">
                                {{number_format(round(($total_parcial / 10000) / count($semanas), 2), 2)}}
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel box box-success">
            <div class="box-header with-border text-right">
                <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseGrafica"
                       aria-expanded="false" class="collapsed text-black">
                        <i class="fa fa-fw fa-line-chart"></i> GRÁFICA
                    </a>
                </h4>
            </div>
            <div id="collapseGrafica" class="panel-collapse collapse">
                <div class="box-body" style="overflow-x: scroll">
                    @include('adminlte.crm.regalias_semanas.partials._grafica')
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">
        No se han encontrado resultados que mostrar
    </div>
@endif