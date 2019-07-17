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
                1ra Flor
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                80%
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Tallos Cosechados
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
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
        @endphp
        @foreach($ciclos as $item)
            <tr style="font-size: 0.8em">
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->modulo->nombre}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->fecha_inicio}}
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
                    @if($item->fecha_cosecha != '')
                        {{difFechas($item->fecha_cosecha, $item->fecha_inicio)->days}}
                    @endif
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->get80Porciento()}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{number_format($item->getTallosCosechados())}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{round($item->getTallosCosechados()/$item->area, 2)}}
                </td>
            </tr>
            @php
                $total_area += $item->area;
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
            <th class="text-center" colspan="2" style="border-color: #9d9d9d">
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
            <th class="text-center" style="border-color: #9d9d9d">
            </th>
        </tr>
    </table>
</div>

<script>
    estructura_tabla('table_fenograma_ejecucion', false, false);
</script>