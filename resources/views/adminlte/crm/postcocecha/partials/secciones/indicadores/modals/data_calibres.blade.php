<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#cosecha-chart" data-toggle="tab" aria-expanded="false">Gráfica</a></li>
        <li class=""><a href="#cosecha-tabla" data-toggle="tab" aria-expanded="true">Tabla</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane" id="cosecha-chart" style="position: relative">
            <canvas id="chart_data_calibres" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane active" id="cosecha-tabla" style="position: relative">
            @php
                $totales_dia = [];
            @endphp
            <table class="table-striped table-responsive table-bordered" width="100%" style="border: 2px solid #9d9d9d;">
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                        Variedad
                    </th>
                    @foreach($labels as $pos => $f)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime($f->dia)))]}}
                            {{substr($f->dia,8,2)}}
                        </th>
                        @php
                            $verde = \yura\Modelos\ClasificacionVerde::All()->where('fecha_ingreso', '=', $f->dia)->first();
                            $totales_dia[$pos] = $verde->getCalibre();
                        @endphp
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                        Total
                    </th>
                </tr>
                @foreach($arreglo_variedades as $v)
                    @php
                        $total = 0;
                        $positivos = 0;
                    @endphp
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{$v['variedad']->nombre}}
                        </th>
                        @foreach($v['calibre'] as $pos => $valor)
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$valor}}
                            </td>
                            @php
                                $total += $valor;
                                if($valor > 0)
                                    $positivos++;
                            @endphp
                        @endforeach
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{$positivos > 0 ? round($total / $positivos, 2) : 0}}
                        </th>
                    </tr>
                @endforeach
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                        Total del día
                    </th>
                    @php
                        $total = 0;
                    @endphp
                    @foreach($totales_dia as $valor)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{$valor}}
                        </th>
                        @php
                            $total += $valor;
                        @endphp
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                        {{round($total / count($labels), 2)}}*
                    </th>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
    construir_char_acumulado('Calibres', 'chart_data_calibres');

    function construir_char_acumulado(label, id) {
        labels = [];
        datasets = [];
        @for($i = 0; $i < count($labels); $i++)
        labels.push("{{$labels[$i]->dia}}");
        @endfor

                {{-- Data_list --}}
                @foreach($arreglo_variedades as $variedad)
            data_list = [];
        @foreach($variedad['calibre'] as $calibres)
        data_list.push("{{$calibres}}");
        @endforeach

        datasets.push({
            label: '{{espacios($variedad['variedad']->nombre)}}' + ' ',
            data: data_list,
            backgroundColor: '{{$variedad['variedad']->color}}',
            borderColor: '{{$variedad['variedad']->color}}',
            borderWidth: 2,
            fill: false,
        });
        @endforeach

            ctx = document.getElementById(id).getContext('2d');
        myChart = new Chart(ctx, {
            type: 'bar',
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
    }
</script>
