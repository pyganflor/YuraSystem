<div style="overflow-x: scroll">
    <table class="table-striped table-responsive table-bordered" width="100%" style="border: 2px solid #9d9d9d;">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 85px">
                Fechas
            </th>
            @php
                $totales_int = [];
                $total = 0;
            @endphp
            @foreach(getIntervalosHorasDiarias() as $pos => $int)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <span class="badge">{{$int['inicio']}}</span>
                    <span class="badge">{{$int['fin']}}</span>
                </th>
                @php
                    $totales_int[$pos] = 0;
                @endphp
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Promedio
            </th>
        </tr>
        @foreach($arreglo_horarios as $pos_a => $a)
            <tr style="color: {{getListColores()[$pos_a]}}">
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$a['fecha']}}
                </th>
                @foreach($a['arreglo'] as $pos => $item)
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$item['valor']}}
                    </td>
                    @php
                        $totales_int[$pos] += $item['valor'];
                    @endphp
                @endforeach
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    @php
                        if ($option == 'cosecha')
                            $object = \yura\Modelos\Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $a['fecha'])->first();
                        if ($option == 'verde')
                            $object = \yura\Modelos\ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $a['fecha'])->first();
                        if ($option == 'blanco')
                            $object = \yura\Modelos\ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $a['fecha'])->first();

                        $inicio = $a['fecha'] . ' ' . $int['inicio'];
                        $fin = $a['fecha'] . ' ' . $int['fin'];

                            if ($id_variedad == '') {
                                $valor = $object->getRendimiento();
                           } else {
                                $valor = $object->getRendimientoByVariedad($id_variedad);
                           }

                    $total += $valor;
                    @endphp
                    {{$valor}}
                </th>
            </tr>
        @endforeach
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Promedio
            </th>
            @foreach($totales_int as $item)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{round($item / count($arreglo_horarios), 2)}}
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{round($total / count($arreglo_horarios), 2)}}*
            </th>
        </tr>
    </table>
</div>