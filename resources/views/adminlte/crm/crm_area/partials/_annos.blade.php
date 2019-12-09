<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#area-chart" data-toggle="tab" aria-expanded="false">Área <sup>ha</sup></a></li>
        <li class=""><a href="#ciclo-chart" data-toggle="tab" aria-expanded="true">Ciclo</a></li>
        <li class=""><a href="#tallos-chart" data-toggle="tab" aria-expanded="true">Tallos/m<sup>2</sup></a></li>
        <li class=""><a href="#ramos-chart" data-toggle="tab" aria-expanded="true">Ramos/m<sup>2</sup></a></li>
        <li class=""><a href="#ramos_anno-chart" data-toggle="tab" aria-expanded="true">Ramos/m<sup>2</sup>/año</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="area-chart" style="position: relative">
            <canvas id="chart_area" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="ciclo-chart" style="position: relative">
            <canvas id="chart_ciclo" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="tallos-chart" style="position: relative">
            <canvas id="chart_tallos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="ramos-chart" style="position: relative">
            <canvas id="chart_ramos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="ramos_anno-chart" style="position: relative">
            <canvas id="chart_ramos_anno" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>


<script>
    construir_char_annos('Area', 'chart_area');
    construir_char_annos('Ciclo', 'chart_ciclo');
    construir_char_annos('Tallos', 'chart_tallos');
    construir_char_annos('Ramos', 'chart_ramos');
    construir_char_annos('Ramos Año', 'chart_ramos_anno');

    function construir_char_annos(label, id) {
        labels = [];
        datasets = [];

        @foreach($labels as $label)
        @if($label >= substr($semana_actual, 2))
        labels.push('{{$label}}*');
        @else
        labels.push('{{$label}}');
        @endif
                @endforeach

                @foreach($data as $dataset)
            data_list = [];
        if (label == 'Area') {
            @foreach($dataset['data_area'] as $valor)
            data_list.push('{{round($valor / 10000, 2)}}');
            @endforeach
        } else if (label == 'Ciclo') {
            @foreach($dataset['data_ciclo'] as $valor)
            data_list.push('{{$valor}}');
            @endforeach
        } else if (label == 'Tallos') {
            @foreach($dataset['data_tallos'] as $valor)
            data_list.push('{{$valor}}');
            @endforeach
        } else if (label == 'Ramos') {
            @foreach($dataset['data_ramos'] as $valor)
            data_list.push('{{$valor}}');
            @endforeach
        } else if (label == 'Ramos Año') {
            @foreach($dataset['data_ramos_anno'] as $valor)
            data_list.push('{{$valor}}');
            @endforeach
        }

        datasets.push({
            label: '{{$dataset['label']}}' + ' ',
            data: data_list,
            backgroundColor: '{{$dataset['color']}}',
            borderColor: '{{$dataset['color']}}',
            borderWidth: 1.5,
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
                        tension: 0.3, // disables bezier curves
                    }
                },
                tooltips: {
                    mode: 'x' // nearest, point, index, dataset, x, y
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