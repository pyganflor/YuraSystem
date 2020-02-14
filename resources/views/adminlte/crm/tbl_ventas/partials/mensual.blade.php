<div style="overflow-x: scroll; width: 100%">
    <table class="table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d" id="table_mensual_ventas">
        @php
            $totales_mes = [];
        @endphp
        <thead>
        <tr>
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                @if($cliente == 'P')
                    País
                @else
                    Cliente
                @endif
            </th>
            @foreach($data['labels'] as $pos => $label)
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white"
                    colspan="{{$acumulado == 'false' ? count($data['meses']) + 1 : count($data['meses'])}}">
                    {{$label}}
                </th>
            @endforeach
            @if($acumulado == 'false')
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                    @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
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
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
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
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; padding: 5px">
                    @if($fila['encabezado'] != '')
                        @if($cliente == 'P')
                            {{$fila['encabezado']->nombre}}
                        @else
                            {{$fila['encabezado']->detalle()->nombre}}
                        @endif
                    @else
                        Todas
                    @endif
                </th>
                @php
                    $total = 0;
                    $count_positivos = 0;
                    $parcial = 0;
                    $count_parcial = count($data['meses']);
                @endphp
                @foreach($fila['valores'] as $pos => $valor)
                    <th class="text-center" style="border-color: #9d9d9d; padding: 5px">
                        @if($cliente != 'P' && $cliente != 'A')
                            <a href="javascript:void(0)" style="color: black; font-weight: bold" class="btn btn-xs btn-link"
                               onclick="pedidos_cliente('{{$fila['encabezado']->id_cliente}}', '{{$data['meses'][count($data['meses']) - $count_parcial]}}', '{{$data['labels'][intval(($pos) / count($data['meses']))]}}')">
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
                                @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
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
                    @endphp
                @endforeach
                @if($acumulado == 'false')
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; padding: 5px">
                        @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
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
            <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px" rowspan="2">
                @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                    Total
                @else
                    Promedio
                @endif
            </th>
            @php
                $total = 0;
                $parcial = 0;
                $count_parcial = count($data['meses']);
            @endphp
            @foreach($totales_mes as $pos => $valor)
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; padding: 5px">
                    @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
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
                            @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
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
                    if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q'){
                        $total += $valor['valor'];
                    }
                    else
                        $total += $valor['count_positivo'] > 0 ? number_format(round($valor['valor'] / $valor['count_positivo'], 2), 2) : 0;
                @endphp
            @endforeach
            @if($acumulado == 'false')
                <th class="text-center" style="border-color: white; background-color: #357ca5; color: white; width: 80px; padding: 5px"
                    rowspan="2">
                    @if($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
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
    estructura_tabla('table_mensual_ventas', false);

    $('#table_mensual').DataTable({
        responsive: false,
    });

    function pedidos_cliente(cliente, mes, anno) {
        datos = {
            cliente: cliente,
            mes: mes,
            anno: anno,
        };
        get_jquery('{{url('tbl_ventas/pedidos_cliente')}}', datos, function (retorno) {
            modal_view('modal-view_pedidos_cliente', retorno, '<i class="fa fa-fw fa-table"></i> Pedidos por cliente', true, false, '95%');
        });
    }
</script>