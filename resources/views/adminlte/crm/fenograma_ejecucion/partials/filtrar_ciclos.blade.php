<div style="overflow-x: scroll">
    <table data-order='[[ 3, "desc" ]]' class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d"
           id="table_fenograma_ejecucion">
        <thead>
        <tr style="background-color: #357ca5; color: white">
            <th class="text-center" style="border-color: #9d9d9d">
                Módulo
            </th>
            <th class="text-center" style="border-color: #9d9d9d" width="95px">
                Inicio
            </th>
            <th class="text-center" style="border-color: #9d9d9d" width="95px">
                Semana
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                P/S
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Días
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Área m<sup>2</sup>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Total x Semana m<sup>2</sup>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                1ra Flor
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Tallos Cosechados
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Tallos/m<sup>2</sup>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Cosechadop <sup>%</sup>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                Ptas Iniciales
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                Ptas Actuales
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                %<sup>M</sup>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                Dend P.Ini/m<sup>2</sup>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                Conteo T/P
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                Tallos/m<sup>2</sup>
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
        @endphp
        @foreach($ciclos as $item)
            <tr style="font-size: 0.8em" onmouseover="$(this).addClass('bg-teal-active')" onmouseleave="$(this).removeClass('bg-teal-active')">
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->modulo->nombre}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->fecha_inicio}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{getSemanaByDate($item->fecha_inicio)->codigo}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->modulo->getPodaSiembraByCiclo($item->id_ciclo)}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    @if($item->fecha_fin != '')
                        {{difFechas($item->fecha_fin, $item->fecha_inicio)->days}}
                    @else
                        {{difFechas(date('Y-m-d'), $item->fecha_inicio)->days}}
                    @endif
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{number_format($item->area, 2)}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    --
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    @if($item->fecha_cosecha != '')
                        {{difFechas($item->fecha_cosecha, $item->fecha_inicio)->days}}
                    @endif
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{number_format($item->getTallosCosechados())}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{round($item->getTallosCosechados()/$item->area, 2)}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    --
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{number_format($item->plantas_iniciales)}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{number_format($item->plantas_actuales())}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->getMortalidad()}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->getDensidadIniciales()}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->conteo}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{round(($item->plantas_actuales() * $item->conteo) / $item->area, 2)}}
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
                if($item->area > 0 && round(($item->plantas_actuales() * $item->conteo) / $item->area, 2) > 0){
                    $total_tallos_m2_proy['valor'] += round(($item->plantas_actuales() * $item->conteo) / $item->area, 2);
                    $total_tallos_m2_proy['positivos']++;
                }
                $ciclo += $item->fecha_fin != '' ? difFechas($item->fecha_fin, $item->fecha_inicio)->days : difFechas(date('Y-m-d'), $item->fecha_inicio)->days;
                $total_tallos += $item->getTallosCosechados();
                $total_tallos_m2 += round($item->getTallosCosechados()/$item->area, 2);
                if($item->getTallosCosechados() > 0){
                    $positivos_tallos_m2 ++;
                }
            @endphp
        @endforeach
        </tbody>
        <tr style="background-color: #357ca5; color: white">
            <th class="text-center" colspan="3" style="border-color: #9d9d9d">
                Totales
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{count($ciclos) > 0 ? round($ciclo / count($ciclos), 2) : 0}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{number_format(round($total_area/ 10000, 2), 2)}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{number_format($total_tallos, 2)}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                @if($positivos_tallos_m2 > 0)
                    {{count($ciclos) > 0 ? round($total_tallos_m2 / $positivos_tallos_m2, 2) : 0}}
                @else
                    0
                @endif
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                {{number_format($total_iniciales)}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                {{number_format($total_actuales)}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                @if($total_mortalidad['positivos'] > 0)
                    {{round($total_mortalidad['valor'] / $total_mortalidad['positivos'], 2)}}
                @endif
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                @if($total_densidad['positivos'] > 0)
                    {{round($total_densidad['valor'] / $total_densidad['positivos'], 2)}}
                @endif
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">

            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #0b3248">
                @if($total_tallos_m2_proy['positivos'] > 0)
                    {{round($total_tallos_m2_proy['valor'] / $total_tallos_m2_proy['positivos'], 2)}}
                @endif
            </th>
        </tr>
    </table>
</div>

<script>
    estructura_tabla('table_fenograma_ejecucion', false, false);
</script>