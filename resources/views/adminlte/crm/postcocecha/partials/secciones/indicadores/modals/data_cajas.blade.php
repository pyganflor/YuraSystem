<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class=""><a href="#cosecha-chart" data-toggle="tab" aria-expanded="false">Gráfica</a></li>
        <li class="active"><a href="#cosecha-tabla" data-toggle="tab" aria-expanded="true">Tabla</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane" id="cosecha-chart" style="position: relative">
            <canvas id="chart_data_cajas" width="100%" height="40" style="margin-top: 5px"></canvas>
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
                            $totales_dia[$pos] = 0;
                        @endphp
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                        Total
                    </th>
                </tr>
                @foreach($arreglo_variedades as $v)
                    @php
                        $total = 0;
                    @endphp
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{$v['variedad']->nombre}}
                        </th>
                        @foreach($v['cajas'] as $pos => $valor)
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{number_format($valor, 2)}}
                            </td>
                            @php
                                $total += $valor;
                                $totales_dia[$pos] += $valor;
                            @endphp
                        @endforeach
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{number_format($total, 2)}}
                        </th>
                    </tr>
                @endforeach
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                        Total
                    </th>
                    @php
                        $total = 0;
                    @endphp
                    @foreach($totales_dia as $valor)
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            {{number_format($valor, 2)}}
                        </th>
                        @php
                            $total += $valor;
                        @endphp
                    @endforeach
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                        {{number_format($total, 2)}}*
                    </th>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
    construir_char_acumulado('Cajas', 'chart_data_cajas');

    function construir_char_acumulado(label, id) {
        labels = [];
        datasets = [];
        @for($i = 0; $i < count($labels); $i++)
        labels.push("{{$labels[$i]->dia}}");
        @endfor

                {{-- Data_list --}}
                @foreach($arreglo_variedades as $variedad)
            data_list = [];
        @foreach($variedad['cajas'] as $caja)
        data_list.push("{{$caja}}");
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
