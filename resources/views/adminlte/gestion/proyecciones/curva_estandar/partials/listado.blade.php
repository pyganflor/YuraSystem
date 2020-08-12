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
                    $porcent = $c['total_cosechado'] > 0 ? round(($v->cosechados * 100) / $c['total_cosechado']) : 0;
                @endphp
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$porcent}}
                </td>
                @php
                    $exist = true;
                    if ($porcent > 0){
                        $array_prom[$pos]['valor'] += $porcent;
                        $array_prom[$pos]['positivos'] ++;
                        if ($c['acumulado'] >= $min_temp && $c['acumulado'] < $temp_prom){    // por debajo del promedio
                            $array_prom_minimos[$pos]['valor'] += $porcent;
                            $array_prom_minimos[$pos]['positivos'] ++;
                        } else if ($c['acumulado'] > $temp_prom && $c['acumulado'] <= $max_temp){  // por encima del promedio
                            $array_prom_maximos[$pos]['valor'] += $porcent;
                            $array_prom_maximos[$pos]['positivos'] ++;
                        }
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
            Promedio ({{count($ciclos) > 0 ? round($semanas_prom / count($ciclos)) : 0}} semanas)
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2">
            {{$temp_prom}}
        </th>
        @php
            $suma_total = 0;
            $array_prom_new = [];
            foreach($array_prom as $pos => $v){
                $valor = $v['positivos'] > 0 ? round($v['valor'] / $v['positivos']) : 0;
                $suma_total += $valor;
                if($pos == count($array_prom) - 1){
                    if($suma_total > 100){
                        $new_valor = $valor - ($suma_total - 100);
                    } else if($suma_total < 100){
                        $new_valor = $valor + (100 - $suma_total);
                    }
                    array_push($array_prom_new, $new_valor >= 5 ? $new_valor : 0);
                    if($new_valor < 5){
                        $array_prom_new[$pos - 1] += $new_valor;
                    }
                } else {
                    array_push($array_prom_new, $valor);
                }
            }
        @endphp

        @foreach($array_prom_new as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" id="th_prom_{{$pos}}">
                {{$v}}%
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
        @php
            $suma_total = 0;
            $array_prom_minimos_new = [];
            foreach($array_prom_minimos as $pos => $v){
                $valor = $v['positivos'] > 0 ? round($v['valor'] / $v['positivos']) : 0;
                $suma_total += $valor;
                if($pos == count($array_prom) - 1){
                    if($suma_total > 100){
                        $new_valor = $valor - ($suma_total - 100);
                    } else if($suma_total < 100){
                        $new_valor = $valor + (100 - $suma_total);
                    }
                    array_push($array_prom_minimos_new, $new_valor >= 5 ? $new_valor : 0);
                    if($new_valor < 5){
                        $array_prom_minimos_new[$pos - 1] += $new_valor;
                    }
                } else {
                    array_push($array_prom_minimos_new, $valor);
                }
            }
        @endphp
        @foreach($array_prom_minimos_new as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$v}}%
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
        @php
            $suma_total = 0;
            $array_prom_maximos_new = [];
            foreach($array_prom_maximos as $pos => $v){
                $valor = $v['positivos'] > 0 ? round($v['valor'] / $v['positivos']) : 0;
                $suma_total += $valor;
                if($pos == count($array_prom) - 1){
                    if($suma_total > 100){
                        $new_valor = $valor - ($suma_total - 100);
                    } else if($suma_total < 100){
                        $new_valor = $valor + (100 - $suma_total);
                    }
                    array_push($array_prom_maximos_new, $new_valor >= 5 ? $new_valor : 0);
                    if($new_valor < 5){
                        $array_prom_maximos_new[$pos - 1] += $new_valor;
                    }
                } else {
                    array_push($array_prom_maximos_new, $valor);
                }
            }
        @endphp
        @foreach($array_prom_maximos_new as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$v}}%
            </th>
        @endforeach
    </tr>
</table>

<script>
    //estructura_tabla('table_curvas', false, false);
    $('#table_curvas_wrapper .row:first').hide();
</script>