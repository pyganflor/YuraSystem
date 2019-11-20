<div class="nav-tabs-custom">
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Gráfica</a></li>
        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Tabla</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <canvas id="chart" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane" id="tab_2">
            {{--<table class="table-striped table-responsive table-bordered" width="100%" style="border: 2px solid #9d9d9d">
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        Variedad
                    </th>
                    @php
                        $totales_dia = [];
                    @endphp
                    @foreach($labels as $pos => $f)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{$f->dia}}
                        </th>
                        @php
                            $totales_dia[$pos] = 0;
                        @endphp
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        @if($option == 'valor' || $option == 'cajas')
                            Total
                        @else
                            Promedio
                        @endif
                    </th>
                </tr>
                @foreach($arreglo_variedades as $pos_v => $v)
                    <tr style="color: {{$v['variedad']->color}}">
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{$v['variedad']->nombre}}
                        </th>
                        @php
                            if($option == 'valor')
                                $array = $v['valores'];
                            if($option == 'cajas')
                                $array = $v['cajas'];
                            if($option == 'precios')
                                $array = $v['precios'];
                            if($option == 'tallos')
                                $array = $v['tallos'];

                            $total_var = 0;
                        @endphp
                        @foreach($array as $pos => $valor)
                            <th class="text-center" style="border-color: #9d9d9d">
                                {{$valor}}
                            </th>
                            @php
                                $totales_dia[$pos] += $valor;
                                $total_var += $valor;
                            @endphp
                        @endforeach
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            @if($option == 'valor' || $option == 'cajas')
                                {{number_format($total_var, 2)}}
                            @else
                                {{round($total_var / count($labels), 2)}}
                            @endif
                        </th>
                    </tr>
                @endforeach
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        @if($option == 'valor' || $option == 'cajas')
                            Total
                        @else
                            Promedio
                        @endif
                    </th>
                    @php
                        $total = 0;
                    @endphp
                    @foreach($totales_dia as $pos => $valor)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            @if($option == 'valor' || $option == 'cajas')
                                {{number_format($valor, 2)}}
                                @php
                                    $total += $valor;
                                @endphp
                            @else
                                {{round($valor / count($arreglo_variedades), 2)}}
                                @php
                                    $total += round($valor / count($arreglo_variedades), 2);
                                @endphp
                            @endif
                        </th>
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                        @if($option == 'valor' || $option == 'cajas')
                            {{number_format($total, 2)}}*
                        @else
                            {{round($total / count($totales_dia), 2)}}*
                        @endif
                    </th>
                </tr>
            </table>--}}
        </div>
    </div>
</div>

<script>
    ctx = document.getElementById('chart').getContext('2d');
    datasets=({
        label: 'XXXXX',
        data: [],
        backgroundColor: '',
        borderColor: '',
        borderWidth: 2,
        fill: false,
    });
    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    }
                }]
            },
            elements: {
                line: {
                    tension: 0, // disables bezier curves
                }
            },
            tooltips: {
                mode: 'point' // nearest, point, index, dataset, x, y
            },
            legend: {
                display: true,
                position: 'bottom',
                fullWidth: false,
                onClick: function () {
                },
                onHover: function () {
                },
                reverse: true,
            },
            showLines: true, // for all datasets
            borderCapStyle: 'round',    // "butt" || "round" || "square"
        }
    });
</script>
