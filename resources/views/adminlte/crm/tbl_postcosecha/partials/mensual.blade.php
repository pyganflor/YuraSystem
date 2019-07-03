<div style="overflow-x: scroll">
    <table class="table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d" id="table_mensual_postcosecha">
        @php
            $totales_mes = [];
        @endphp
        <thead>
        <tr>
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                Variedad
            </th>
            @foreach($data['labels'] as $pos => $label)
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white"
                    colspan="{{$acumulado == 'false' ? count($data['meses']) + 1 : count($data['meses'])}}">
                    {{$label}}
                </th>
            @endforeach
            @if($acumulado == 'false')
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                    @if($criterio == 'C' || $criterio == 'T' || $criterio == 'E')
                        Total
                    @else
                        Promedio
                    @endif
                </th>
            @endif
        </tr>
        <tr>
            @foreach($data['labels'] as $pos_a => $label)
                @foreach($data['meses'] as $pos_m => $mes)
                    <th class="text-center"
                        style="border-color: #9d9d9d; background-color: #e9ecef; border-right-width: {{($pos_m + 1) % count($data['meses']) == 0 ? '3px' : '1px'}}">
                        {{$mes}}
                    </th>
                    @php
                        array_push($totales_mes, [
                            'valor' => 0,
                            'count_positivo' => 0
                        ]);
                    @endphp
                @endforeach
                @if($acumulado == 'false')
                    <th class="text-center bg-gray"
                        style="border-color: #9d9d9d; border-right-width: 3px">
                        Subtotal
                    </th>
                @endif
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
                    $pos_anno = 0;
                    $pos_parcial = 0;

                    if($fila['encabezado'] != '')
                        $id_variedad = $fila['encabezado']->id_variedad;
                    else
                        $id_variedad = '';

                $parcial = 0;
                $count_parcial = count($data['meses']);
                @endphp
                @foreach($fila['valores'] as $pos => $valor)
                    <th class="text-center"
                        style="border-color: #9d9d9d; border-right-width: {{($pos + 1) % count($data['meses']) == 0 ? '3px' : '1px'}}">
                        @if($valor > 0)
                            <a href="javascript:void(0)" class="btn btn-link btn-xs" style="color: black"
                               onclick="navegar_tabla('D', '{{$criterio}}', '{{$data['meses'][$pos_parcial]}}', 'M','{{$data['labels'][$pos_anno]}}', '{{$id_variedad}}')">
                                {{number_format($valor,2)}}
                            </a>
                        @else
                            {{number_format($valor,2)}}
                        @endif
                    </th>
                    @if($count_parcial <= 1)
                        @php
                            $parcial += $valor;
                        @endphp
                        @if($acumulado == 'false')
                            <th class="text-center bg-gray"
                                style="border-color: #9d9d9d; border-right-width: 3px; padding: 5px">
                                @if($criterio == 'C' || $criterio == 'T' || $criterio == 'E')
                                    {{number_format($parcial, 2)}}
                                @else
                                    {{number_format(round($parcial / count($data['meses']), 2), 2)}}
                                @endif
                            </th>
                        @endif
                        @php
                            $parcial = 0;
                            $count_parcial = count($data['meses']);
                        @endphp
                    @else
                        @php
                            $parcial += $valor;
                            $count_parcial--;
                        @endphp
                    @endif
                    @php
                        $total += $valor;
                        $totales_mes[$pos]['valor'] += $valor;
                        if($valor > 0){
                            $count_positivos++;
                            $totales_mes[$pos]['count_positivo']++;
                        }
                        $pos_parcial++;
                        if($pos_parcial % count($data['meses']) == 0){
                            $pos_parcial = 0;
                            $pos_anno++;
                        }
                    @endphp
                @endforeach
                @if($acumulado == 'false')
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        @if($criterio == 'C' || $criterio == 'T' || $criterio == 'E')
                            {{number_format($total,2)}}
                        @else
                            {{$count_positivos > 0 ? number_format(round($total / $count_positivos, 2),2) : 0}}
                        @endif
                    </th>
                @endif
            </tr>
        @endforeach
        </tbody>
        <tr>
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                @if($criterio == 'C' || $criterio == 'T' || $criterio == 'E')
                    Total
                @else
                    Promedio
                @endif
            </th>
            @php
                $total = 0;
                $count_parcial = count($data['meses']);
            @endphp
            @foreach($totales_mes as $pos => $valor)
                <th class="text-center"
                    style="border-color: white; background-color: #357ca5; color: white; border-right-width: {{($pos + 1) % count($data['meses']) == 0 ? '3px' : '1px'}}">
                    @if($criterio == 'C' || $criterio == 'T' || $criterio == 'E')
                        {{number_format($valor['valor'], 2)}}
                    @else
                        {{$valor['count_positivo'] > 0 ? number_format(round($valor['valor'] / $valor['count_positivo'], 2), 2) : 0}}
                    @endif
                </th>
                @if($count_parcial <= 1)
                    @php
                        $parcial += $valor['valor'];
                    @endphp
                    @if($acumulado == 'false')
                        <th class="text-center bg-gray"
                            style="border-color: #9d9d9d; border-right-width: 3px; padding: 5px">
                            @if($criterio == 'C' || $criterio == 'T' || $criterio == 'E')
                                {{number_format($parcial, 2)}}
                            @else
                                {{number_format(round($parcial / count($data['meses']), 2), 2)}}
                            @endif
                        </th>
                    @endif
                    @php
                        $parcial = 0;
                        $count_parcial = count($data['meses']);
                    @endphp
                @else
                    @php
                        $parcial += $valor['valor'];
                        $count_parcial--;
                    @endphp
                @endif
                @php
                    if($criterio == 'C' || $criterio == 'T' || $criterio == 'E'){
                        $total += $valor['valor'];
                    }
                    else
                        $total += $valor['count_positivo'] > 0 ? number_format(round($valor['valor'] / $valor['count_positivo'], 2), 2) : 0;
                @endphp
            @endforeach
            @if($acumulado == 'false')
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                    @if($criterio == 'C' || $criterio == 'T' || $criterio == 'E')
                        {{number_format($total, 2)}}
                    @else
                        {{number_format(round($total / (count($data['labels']) * count($data['meses'])), 2), 2)}}
                    @endif
                </th>
            @endif
        </tr>
    </table>
</div>

<script>
    estructura_tabla('table_mensual_postcosecha', false);
</script>