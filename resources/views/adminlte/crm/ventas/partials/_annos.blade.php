<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#valor-chart" data-toggle="tab" aria-expanded="true">Valor</a></li>
        <li class=""><a href="#fisicas-chart" data-toggle="tab" aria-expanded="false">Cajas Físicas</a></li>
        <li class=""><a href="#equivalentes-chart" data-toggle="tab" aria-expanded="false">Cajas Equivalentes</a></li>
        <li class=""><a href="#precio-chart" data-toggle="tab" aria-expanded="false">Precio x Ramo</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="valor-chart" style="position: relative">
            <canvas id="chart_annos_valor" width="100%" height="33" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="fisicas-chart" style="position: relative">
            <canvas id="chart_annos_fisicas" width="100%" height="33" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="equivalentes-chart" style="position: relative">
            <canvas id="chart_annos_equivalentes" width="100%" height="33" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="precio-chart" style="position: relative">
            <canvas id="chart_annos_precio" width="100%" height="33" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>


<script>
    construir_char_annos('Valor', 'chart_annos_valor');
    construir_char_annos('Fisicas', 'chart_annos_fisicas');
    construir_char_annos('Equivalentes', 'chart_annos_equivalentes');
    construir_char_annos('Precio', 'chart_annos_precio');

    function construir_char_annos(label, id) {
        labels = [];
        datasets = [];
        @for($i = 0; $i < 12; $i++)
        labels.push("{{getMeses(TP_ABREVIADO)[$i]}}");
        @endfor

                {{-- Data_list --}}
                @foreach($arreglo_annos as $pos_a => $a)
            data_list = [];
        if (label == 'Valor') {
            @foreach($a['valores'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }
        else if (label == 'Fisicas') {
            @foreach($a['fisicas'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }
        else if (label == 'Equivalentes') {
            @foreach($a['equivalentes'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }
        else {
            @foreach($a['precios'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }

        datasets.push({
            label: '{{$a['anno']}}' + ' ',
            data: data_list,
            backgroundColor: '{{getListColores()[$pos_a]}}',
            borderColor: '{{getListColores()[$pos_a]}}',
            borderWidth: 1,
            fill: false,
        });
        @endforeach

            ctx = document.getElementById(id).getContext('2d');
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
    }
</script>