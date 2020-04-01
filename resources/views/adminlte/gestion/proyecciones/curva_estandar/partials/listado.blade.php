<table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" rowspan="2">
            Módulo
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" rowspan="2">
            Semana P/S
        </th>
        <th class="text-center hidden" style="border-color: #9d9d9d; background-color: #e9ecef" rowspan="2">
            Curva
        </th>
        @php
            $array_prom = [];
        @endphp
        @for($i = $min_dia; $i <= $max_dia; $i++)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$i}}
            </th>
            @php
                array_push($array_prom, [
                    'valor' => 0,
                    'positivos' => 0,
                ]);
            @endphp
        @endfor
    </tr>
    <tr>
        @for($i = $min_dia, $pos = 1; $i <= $max_dia; $i++, $pos++)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$pos}}º
            </th>
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
            <th class="text-center hidden" style="border-color: #9d9d9d">
                {{$c['ciclo']->curva}}
            </th>
            @for($i = $min_dia, $pos = 0; $i <= $max_dia; $i++, $pos++)
                @php
                    $exist = false;
                @endphp
                @foreach($c['cosechas'] as $v)
                    @if(explode('º', $v->info)[0] == $i)
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
                    @endif
                @endforeach
                @if(!$exist)
                    <th class="text-center" style="border-color: #9d9d9d">
                    </th>
                @endif
            @endfor
        </tr>
    @endforeach
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" rowspan="1" colspan="2">
            Promedio
        </th>
        @foreach($array_prom as $pos => $v)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{round($v['valor'] / $v['positivos'])}}%
            </th>
        @endforeach
    </tr>
</table>