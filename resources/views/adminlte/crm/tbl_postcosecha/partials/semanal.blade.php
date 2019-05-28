<div style="overflow-x: scroll">
    <table class="table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d">
        @php
            $totales_mes = [];
        @endphp
        <tr>
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                Variedad
            </th>
            @foreach($data['labels'] as $pos => $label)
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white"
                    colspan="{{count($data['semanas'])}}">
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
                @foreach($data['semanas'] as $pos_m => $mes)
                    <th class="text-center" title="Semana"
                        style="border-color: #9d9d9d; background-color: #e9ecef; border-right-width: {{($pos_m + 1) % count($data['semanas']) == 0 ? '3px' : '1px'}}">
                        {{$mes}}
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
                    $pos_anno = 0;
                    $pos_parcial = 0;

                    if($fila['encabezado'] != '')
                        $id_variedad = $fila['encabezado']->id_variedad;
                    else
                        $id_variedad = '';
                @endphp
                @foreach($fila['valores'] as $pos => $valor)
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-right-width: {{($pos + 1) % count($data['semanas']) == 0 ? '3px' : '1px'}}">
                        @if($valor > 0)
                            <a href="javascript:void(0)" class="btn btn-link btn-xs" style="color: black"
                               onclick="navegar_tabla('D', '{{$criterio}}', '{{$data['semanas'][$pos_parcial]}}', 'S','{{$data['labels'][$pos_anno]}}', '{{$id_variedad}}')">
                                {{number_format($valor,2)}}
                            </a>
                        @else
                            {{number_format($valor,2)}}
                        @endif
                    </th>
                    @php
                        $total += $valor;
                        $totales_mes[$pos]['valor'] += $valor;
                        if($valor > 0) {
                            $count_positivos++;
                            $totales_mes[$pos]['count_positivo']++;
                        }

                        $pos_parcial++;
                        if($pos_parcial % count($data['semanas']) == 0){
                            $pos_parcial = 0;
                            $pos_anno++;
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
                    style="border-color: white; background-color: #357ca5; color: white; border-right-width: {{($pos + 1) % count($data['semanas']) == 0 ? '3px' : '1px'}}">
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
                    {{number_format(round($total / (count($data['labels']) * count($data['semanas'])), 2), 2)}}
                @endif
            </th>
        </tr>
    </table>
</div>