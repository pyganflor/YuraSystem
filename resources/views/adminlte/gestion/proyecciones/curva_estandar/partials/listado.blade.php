<table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5">
            Módulo
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5">
            Semana P/S
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5">
            Temperatura
        </th>
        <th class="text-center hidden" style="border-color: #9d9d9d; background-color: #357CA5">
            Curva
        </th>
        @php
            $array_prom = [];
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
            @endphp
        @endfor
    </tr>
    @foreach($ciclos as $c)
        @php
            $modulo = $c['ciclo']->modulo;
        @endphp
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$modulo->nombre}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$c['ciclo']->semana_poda_siembra}}
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{number_format($c['acumulado'], 2)}}
            </th>
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
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" rowspan="1" colspan="3">
            Promedio
        </th>
        @foreach($array_prom as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{round($v['valor'] / $v['positivos'])}}%
            </th>
        @endforeach
    </tr>
</table>