<div style="overflow-x: scroll">
    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d" id="table_fenograma_ejecucion">
        <thead>
        <tr style="background-color: #357ca5; color: white">
            <th class="text-center" style="border-color: #9d9d9d">
                Módulo
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Inicio
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Poda/Siembra
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Área m<sup>2</sup>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Días
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
            <th class="text-center" style="border-color: #9d9d9d">
                Final
            </th>
        </tr>
        </thead>
        <tbody>
        @php
            $total_area = 0;
            $ciclo = 0;
            $total_tallos = 0;
            $total_tallos_m2 = 0;
        @endphp
        @foreach($ciclos as $item)
            <tr>
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
                    {{number_format($item->area, 2)}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    @if($item->fecha_fin != '')
                        {{difFechas($item->fecha_fin, $item->fecha_inicio)->days}}
                    @else
                        {{difFechas(date('Y-m-d'), $item->fecha_inicio)->days}}
                    @endif
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
                    {{$item->getTallosCosechados()}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{round($item->getTallosCosechados()/$item->area, 2)}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->fecha_fin}}
                </td>
            </tr>
            @php
                $total_area += $item->area;
                $ciclo += $item->fecha_fin != '' ? difFechas($item->fecha_fin, $item->fecha_inicio)->days : difFechas(date('Y-m-d'), $item->fecha_inicio)->days;
                $total_tallos += $item->getTallosCosechados();
                $total_tallos_m2 += round($item->getTallosCosechados()/$item->area, 2);
            @endphp
        @endforeach
        </tbody>
        <tr style="background-color: #357ca5; color: white">
            <th class="text-center" colspan="3" style="border-color: #9d9d9d">
                Totales
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{number_format($total_area, 2)}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{count($ciclos) > 0 ? round($ciclo / count($ciclos), 2) : 0}}
            </th>
            <th class="text-center" colspan="2" style="border-color: #9d9d9d">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{number_format($total_area, 2)}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{count($ciclos) > 0 ? round($total_tallos_m2 / count($ciclos), 2) : 0}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
            </th>
        </tr>
    </table>
</div>

<script>
    estructura_tabla('table_fenograma_ejecucion', false, false);
</script>