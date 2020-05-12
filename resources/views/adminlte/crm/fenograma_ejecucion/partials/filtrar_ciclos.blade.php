@if(count($ciclos) > 0)
    <div id="div_content_fixed">
        <table {{--data-order='[[ 3, "desc" ]]'--}} class="table-striped table-bordered" width="100%"
               style="border: 1px solid #9d9d9d; border-radius: 18px 18px 0 0;"
               id="table_fenograma_ejecucion">
            <thead>
            <tr style="color: white">
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px; border-radius: 18px 0 0 0">
                    Módulo
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px" width="95px">
                    Inicio
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; width: 30px; padding-left: 5px">
                    Semana
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    P/S
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Días
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Área m<sup>2</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Total x Semana m<sup>2</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    1ra Flor
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    %<sup>M</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Tallos Cosechados
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Real <br>
                    Tallos/m<sup>2</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Cosechado <sup>%</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    Proy <br>
                    Tallos/m<sup>2</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    Ptas Iniciales
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    Ptas Actuales
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    Dend P.Ini/m<sup>2</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px; border-radius: 0 18px 0 0">
                    Conteo T/P
                </th>
            </tr>
            </thead>
            <tbody>
            @php
                $total_area = 0;
                $ciclo = 0;
                $total_tallos = 0;
                $total_tallos_m2 = 0;
                $positivos_tallos_m2 = 0;
                $total_iniciales = 0;
                $total_actuales = 0;
                $total_mortalidad = [
                    'valor' => 0,
                    'positivos' => 0,
                ];
                $total_densidad = [
                    'valor' => 0,
                    'positivos' => 0,
                ];
                $total_tallos_m2_proy = [
                    'valor' => 0,
                    'positivos' => 0,
                ];

            $codigo_semana = $ciclos[0]->semana()->codigo;
            $area = 0;
            @endphp
            @foreach($ciclos as $pos_item => $item)
                @php
                    $semana = $item->semana();
                    $poda_siembra = $item->modulo->getPodaSiembraByCiclo($item->id_ciclo);
                    $tallos_cosechados = $item->getTallosCosechados();

                    $desecho = $item->desecho > 0 ? $item->desecho : $semana->desecho;
                    $desecho = $desecho > 0 ? $desecho : 20;

                    $conteo = $item->conteo;
                    if($item->conteo <= 0)
                        if ($poda_siembra > 0)
                            $conteo = $semana->tallos_planta_poda;
                        else
                            $conteo = $semana->tallos_planta_siembra;

                    $tallos_m2_cos = round($tallos_cosechados / $item->area, 2);
                    $tallos_m2_proy = round((($item->plantas_actuales() * $conteo) * ((100 - $desecho) / 100)) / $item->area, 2);
                @endphp
                <tr style="font-size: 0.8em" onmouseover="$(this).css('background-color','#e5f7f3 !important');"
                    onmouseleave="$(this).css('background-color','');">
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{$item->modulo->nombre}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{$item->fecha_inicio}}
                    </td>
                    <td style="border-color: #9d9d9d;; padding-left: 5px">
                        {{$semana->codigo}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{$poda_siembra}}
                    </td>
                    <td style="border-color: #9d9d9d;; padding-left: 5px">
                        @if($item->fecha_fin != '')
                            {{difFechas($item->fecha_fin, $item->fecha_inicio)->days}}
                        @else
                            {{difFechas(date('Y-m-d'), $item->fecha_inicio)->days}}
                        @endif
                    </td>
                    <td style="border-color: #9d9d9d;; padding-left: 5px">
                        {{number_format($item->area, 2)}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        @php
                            if($codigo_semana == $semana->codigo){
                                $area += $item->area;
                            } else {
                                $area = $item->area;
                                $codigo_semana = $semana->codigo;
                            }

                            if($pos_item + 1 < count($ciclos)){
                                if($ciclos[$pos_item + 1]->semana()->codigo != $codigo_semana){
                                    echo number_format($area, 2);
                                }
                            } else {
                                echo number_format($area, 2);
                            }
                        @endphp
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        @if($item->fecha_cosecha != '')
                            {{difFechas($item->fecha_cosecha, $item->fecha_inicio)->days}}
                        @endif
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        @php
                            $mortalidad = $item->getMortalidad();

                            $color = 'orange';
                            if ($mortalidad < 10)
                                $color = 'green';
                            if ($mortalidad > 20)
                                $color = 'red';
                        @endphp
                        <span style="color: {{$color}}">{{$mortalidad}}</span>
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{number_format($tallos_cosechados)}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{$tallos_m2_cos}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{$tallos_m2_proy > 0 ? round(($tallos_m2_cos / $tallos_m2_proy) * 100, 2) : 0}}%
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        @php
                            $color = '#EF6E11';
                            if ($tallos_m2_proy < 35)
                                $color = '#D01C62';
                            if ($tallos_m2_proy > 45)
                                $color = '#00B388';
                        @endphp
                        <span style="color: {{$color}}">{{$tallos_m2_proy}}</span>
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{number_format($item->plantas_iniciales)}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{number_format($item->plantas_actuales())}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px">
                        {{$item->getDensidadIniciales()}}
                    </td>
                    <td style="border-color: #9d9d9d; padding-left: 5px"
                        title="{{$item->conteo <= 0 ? 'semana':''}}">
                        {{$conteo}}
                    </td>
                </tr>
                @php
                    $total_area += $item->area;
                    $total_iniciales += $item->plantas_iniciales;
                    $total_actuales += $item->plantas_actuales();
                    if($item->plantas_iniciales > 0 && $item->plantas_actuales() > 0){
                        $total_mortalidad['valor'] += $item->getMortalidad();
                        $total_mortalidad['positivos']++;
                    }
                    if($item->plantas_iniciales > 0 && $item->area > 0){
                        $total_densidad['valor'] += $item->getDensidadIniciales();
                        $total_densidad['positivos']++;
                    }
                    if($item->area > 0 && $tallos_m2_proy > 0){
                        $total_tallos_m2_proy['valor'] += $tallos_m2_proy;
                        $total_tallos_m2_proy['positivos']++;
                    }
                    $ciclo += $item->fecha_fin != '' ? difFechas($item->fecha_fin, $item->fecha_inicio)->days : difFechas(date('Y-m-d'), $item->fecha_inicio)->days;
                    $total_tallos += $tallos_cosechados;
                    $total_tallos_m2 += $tallos_m2_cos;
                    if($tallos_cosechados > 0){
                        $positivos_tallos_m2 ++;
                    }
                @endphp
            @endforeach
            </tbody>
            <tr style="background-color: #00B388; color: white">
                <th colspan="4" style="border-color: #9d9d9d; padding-left: 5px">
                    Totales
                </th>
                <th style="border-color: #9d9d9d; padding-left: 5px">
                    {{count($ciclos) > 0 ? round($ciclo / count($ciclos), 2) : 0}}
                </th>
                <th style="border-color: #9d9d9d; padding-left: 5px">
                    {{number_format(round($total_area/ 10000, 2), 2)}}
                </th>
                <th style="border-color: #9d9d9d; padding-left: 5px">
                </th>
                <th style="border-color: #9d9d9d; padding-left: 5px">
                </th>
                <th style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    @if($total_mortalidad['positivos'] > 0)
                        {{round($total_mortalidad['valor'] / $total_mortalidad['positivos'], 2)}}
                    @endif
                </th>
                <th style="border-color: #9d9d9d; padding-left: 5px">
                    {{number_format($total_tallos, 2)}}
                </th>
                <th style="border-color: #9d9d9d; padding-left: 5px">
                    @if($positivos_tallos_m2 > 0)
                        {{count($ciclos) > 0 ? round($total_tallos_m2 / $positivos_tallos_m2, 2) : 0}}
                    @else
                        0
                    @endif
                </th>
                <th style="border-color: #9d9d9d; padding-left: 5px">
                </th>
                <th style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    @if($total_tallos_m2_proy['positivos'] > 0)
                        {{round($total_tallos_m2_proy['valor'] / $total_tallos_m2_proy['positivos'], 2)}}
                    @endif
                </th>
                <th style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    {{number_format($total_iniciales)}}
                </th>
                <th style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    {{number_format($total_actuales)}}
                </th>
                <th style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    @if($total_densidad['positivos'] > 0)
                        {{round($total_densidad['valor'] / $total_densidad['positivos'], 2)}}
                    @endif
                </th>
                <th style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">

                </th>
            </tr>
        </table>
    </div>

    <script>
        estructura_tabla('table_fenograma_ejecucion', false, false);
        $('#table_fenograma_ejecucion_filter label').addClass('text-color_yura');
        $('#table_fenograma_ejecucion_filter label input').addClass('input-yura_default');
    </script>

    <style>
        #div_content_fixed {
            overflow-x: scroll;
            overflow-y: scroll;
            width: 100%;
            max-height: 450px;
        }

        #table_fenograma_ejecucion {
            border-spacing: 0 !important;
            border: 1px solid #9d9d9d !important;
        }

        #table_fenograma_ejecucion th, #table_fenograma_ejecucion td {
            border-spacing: 0;
        }

        #table_fenograma_ejecucion thead .fila_fija1 {
            background-color: #00B388 !important;
            border: 1px solid #9d9d9d !important;
            z-index: 9;
            position: sticky;
            top: 0;
        }

        #table_fenograma_ejecucion thead .fila_fija2 {
            background-color: #0b3248 !important;
            border: 1px solid #9d9d9d !important;
            z-index: 9;
            position: sticky;
            top: 0;
        }
    </style>
@endif
