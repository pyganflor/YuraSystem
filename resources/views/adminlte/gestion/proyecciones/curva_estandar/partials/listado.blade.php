<table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d" id="table_curvas">
    <thead>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
            Módulo
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
            Semana P/S
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" colspan="2">
            Temperatura
        </th>
        <th class="text-center hidden" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
            Curva
        </th>
        @php
            $array_prom = [];
            $array_prom_minimos = [];
            $array_prom_maximos = [];
        @endphp
        @for($i = 1; $i <= $max_dia; $i++)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$i}}º
            </th>
            @php
                array_push($array_prom, [
                    'valor' => 0,
                    'positivos' => 0,
                ]);
                array_push($array_prom_minimos, [
                    'valor' => 0,
                    'positivos' => 0,
                ]);
                array_push($array_prom_maximos, [
                    'valor' => 0,
                    'positivos' => 0,
                ]);
            @endphp
        @endfor
    </tr>
    </thead>
    @php
        $semanas_prom = 0;
    @endphp
    <tbody>
    @foreach($ciclos as $c)
        @php
            $modulo = $c['ciclo']->modulo;
            $semanas_prom += count($c['cosechas']);
        @endphp
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$modulo->nombre}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$c['ciclo']->semana_poda_siembra}}
            </th>
            <td class="text-center" style="border-color: #9d9d9d" colspan="2">
                {{number_format($c['acumulado'], 2)}}
            </td>
            <th class="text-center hidden" style="border-color: #9d9d9d">
                {{$c['ciclo']->curva}}
            </th>
            @foreach($c['cosechas'] as $pos => $v)
                @php
                    $porcent = round(($v->cosechados * 100) / $c['total_cosechado']);
                @endphp
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$porcent}}
                </td>
                @php
                    $exist = true;
                    if ($porcent > 0){
                        $array_prom[$pos]['valor'] += $porcent;
                        $array_prom[$pos]['positivos'] ++;
                    }
                @endphp
            @endforeach
            @for($i = count($c['cosechas']) + 1; $i <= $max_dia; $i++)
                <th class="text-center" style="border-color: #9d9d9d">
                </th>
            @endfor
        </tr>
    @endforeach
    </tbody>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" rowspan="1" colspan="2">
            Promedio ({{round($semanas_prom / count($ciclos))}} semanas)
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2">
            {{$temp_prom}}
        </th>
        @foreach($array_prom as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{round($v['valor'] / $v['positivos'])}}%
            </th>
        @endforeach
    </tr>
    {{--ESTIMACIONES--}}
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" rowspan="2" colspan="2">
            Estimaciones
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            {{$min_temp}}
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            {{$temp_prom}}
        </th>
        @foreach($array_prom as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                %
            </th>
        @endforeach
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            {{$temp_prom}}
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            {{$max_temp}}
        </th>
        @foreach($array_prom as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                %
            </th>
        @endforeach
    </tr>
</table>

<script>
    //estructura_tabla('table_curvas', false, false);
    $('#table_curvas_wrapper .row:first').hide();
</script>