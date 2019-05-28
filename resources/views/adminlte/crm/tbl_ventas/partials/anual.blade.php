<div style="overflow-x: scroll">
    <table class="table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d" id="table_anual">
        @php
            $totales_anno = [];
        @endphp

        <thead>
        <tr>
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                Cliente
            </th>
            @foreach($data['labels'] as $pos => $label)
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white">
                    {{$label}}
                </th>
                @php
                    $totales_anno[$pos] = [
                        'valor' => 0,
                        'count_positivo' => 0
                    ];
                @endphp
            @endforeach
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                    Total
                @else
                    Promedio
                @endif
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['filas'] as $fila)
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef;">
                    @if($fila['encabezado'] != '')
                        @if($cliente == 'P')
                            {{$fila['encabezado']->nombre}}
                        @else
                            {{$fila['encabezado']->detalle()->nombre}}
                        @endif
                    @else
                        Todos
                    @endif
                </th>
                @php
                    $total = 0;
                    $count_positivos = 0;

                    if($fila['encabezado'] != '')
                        $id_cliente = $fila['encabezado']->id_cliente;
                    else
                        $id_cliente = '';
                @endphp
                @foreach($fila['valores'] as $pos => $valor)
                    <th class="text-center" style="border-color: #9d9d9d">
                        @if($valor > 0)
                            <a href="javascript:void(0)" class="btn btn-link btn-xs" style="color: black"
                               onclick="navegar_tabla('M', '{{$criterio}}', '', 'A','{{$data['labels'][$pos]}}', '{{$id_cliente}}', '{{$desde}}', '{{$hasta}}')">
                                {{number_format($valor,2)}}
                            </a>
                        @else
                            {{number_format($valor,2)}}
                        @endif
                    </th>
                    @php
                        $total += $valor;
                        $totales_anno[$pos]['valor'] += $valor;
                        if($valor > 0){
                            $count_positivos++;
                            $totales_anno[$pos]['count_positivo']++;
                        }
                    @endphp
                @endforeach
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                        {{number_format($total,2)}}
                    @else
                        {{$count_positivos > 0 ? number_format(round($total / $count_positivos, 2),2) : 0}}
                    @endif
                </th>
            </tr>
        @endforeach
        </tbody>
        <tfooter>
            <tr>
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                    @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                        Total
                    @else
                        Promedio
                    @endif
                </th>
                @php
                    $total = 0;
                @endphp
                @foreach($totales_anno as $valor)
                    <th class="text-center" style="border-color: white; background-color: #357ca5; color: white">
                        @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                            {{number_format($valor['valor'], 2)}}
                        @else
                            {{$valor['count_positivo'] > 0 ? number_format(round($valor['valor'] / $valor['count_positivo'], 2), 2) : 0}}
                        @endif
                    </th>
                    @php
                        if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                            $total += $valor['valor'];
                        else
                            $total += $valor['count_positivo'] > 0 ? number_format(round($valor['valor'] / $valor['count_positivo'], 2), 2) : 0;
                    @endphp
                @endforeach

                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px">
                    @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                        {{number_format($total, 2)}}
                    @else
                        {{number_format(round($total / count($data['labels']), 2), 2)}}
                    @endif
                </th>
            </tr>
        </tfooter>
    </table>
</div>

<script>
    estructura_tabla('table_anual', false);
</script>