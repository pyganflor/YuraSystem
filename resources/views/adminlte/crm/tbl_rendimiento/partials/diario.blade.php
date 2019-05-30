<div style="overflow-x: scroll">
    <table class="table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d" id="table_diario_postcosecha">
        @php
            $totales_mes = [];
        @endphp
        <thead>
        <tr>
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                Variedad
            </th>
            @foreach($data['labels'] as $pos => $label)
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white" colspan="{{count($data['dias'])}}">
                    {{$label}}
                </th>
            @endforeach
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                @if($criterio == 'C' || $criterio == 'T')
                    Total
                @else
                    Promedio
                @endif
            </th>
        </tr>
        <tr>
            @foreach($data['labels'] as $pos_a => $label)
                @foreach($data['dias'] as $pos_m => $dia)
                    <th class="text-center" width="250px"
                        style="border-color: #9d9d9d; background-color: #e9ecef; border-right-width: {{($pos_m + 1) % count($data['dias']) == 0 ? '3px' : '1px'}}">
                        <strong>
                            {{substr($dia, 8)}} <em style="font-size: 0.8em">{{getMeses()[intval(substr($dia,5,2)) - 1]}}</em>
                        </strong>
                    </th>
                    @php
                        array_push($totales_mes, [
                            'valor' => 0,
                            'count_positivo' => 0
                        ]);
                    @endphp
                @endforeach
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($data['filas'] as $fila)
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef;">
                    @if($fila['encabezado'] != '')
                        {{$fila['encabezado']->siglas}}
                    @else
                        Todas
                    @endif
                </th>
                @php
                    $total = 0;
                    $count_positivos = 0;
                @endphp
                @foreach($fila['valores'] as $pos => $valor)
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-right-width: {{($pos + 1) % count($data['dias']) == 0 ? '3px' : '1px'}}">
                        <span style="padding: 5px">{{number_format($valor,2)}}</span>
                    </th>
                    @php
                        $total += $valor;
                        $totales_mes[$pos]['valor'] += $valor;
                        if($valor > 0){
                            $count_positivos++;
                            $totales_mes[$pos]['count_positivo']++;
                        }
                    @endphp
                @endforeach
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    @if($criterio == 'C' || $criterio == 'T')
                        {{number_format($total,2)}}
                    @else
                        {{$count_positivos > 0 ? number_format(round($total / $count_positivos, 2),2) : 0}}
                    @endif
                </th>
            </tr>
        @endforeach
        </tbody>
        <tr>
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                @if($criterio == 'C' || $criterio == 'T')
                    Total
                @else
                    Promedio
                @endif
            </th>
            @php
                $total = 0;
            @endphp
            @foreach($totales_mes as $pos => $valor)
                <th class="text-center"
                    style="border-color: white; background-color: #357ca5; color: white; border-right-width: {{($pos + 1) % count($data['dias']) == 0 ? '3px' : '1px'}}">
                    @if($criterio == 'C' || $criterio == 'T')
                        {{number_format($valor['valor'], 2)}}
                    @else
                        {{$valor['count_positivo'] > 0 ? number_format(round($valor['valor'] / $valor['count_positivo'], 2), 2) : 0}}
                    @endif
                </th>
                @php
                    if($criterio == 'C' || $criterio == 'T'){
                        $total += $valor['valor'];
                    }
                    else
                        $total += $valor['count_positivo'] > 0 ? number_format(round($valor['valor'] / $valor['count_positivo'], 2), 2) : 0;
                @endphp
            @endforeach
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                @if($criterio == 'C' || $criterio == 'T')
                    {{number_format($total, 2)}}
                @else
                    {{number_format(round($total / (count($data['labels']) * count($data['dias'])), 2), 2)}}
                @endif
            </th>
        </tr>
    </table>
</div>

<script>
    estructura_tabla('table_diario_postcosecha', false);
</script>