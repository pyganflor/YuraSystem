<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#cosecha-chart" data-toggle="tab" aria-expanded="false">Cosecha</a></li>
        <li class=""><a href="#verde-chart" data-toggle="tab" aria-expanded="true">Verde</a></li>
        <li class=""><a href="#blanco-chart" data-toggle="tab" aria-expanded="true">Blanco</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="cosecha-chart" style="position: relative">
            <canvas id="chart_cosecha" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="verde-chart" style="position: relative">
            <canvas id="chart_verde" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="blanco-chart" style="position: relative">
            <canvas id="chart_blanco" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>

<script>
    construir_char('Cosecha', 'chart_cosecha');
    construir_char('Verde', 'chart_verde');
    construir_char('Blanco', 'chart_blanco');

    function construir_char(label, id) {
        labels = [];
        datasets = [];
        data_list = [];
        @for($i = 0; $i < count($labels); $i++)
        @if($periodo == 'diario')
        labels.push("{{$labels[$i]}}");
        @elseif($periodo == 'semanal')
        labels.push("{{$labels[$i]}}");
        @elseif($periodo == 'anual')
        labels.push("{{$labels[$i]->ano}}");
        @else
        labels.push("{{getMeses(TP_ABREVIADO)[$labels[$i]->mes - 1]. ' - '.$labels[$i]->ano}}");
        @endif
        if (label == 'Verde') {
            @if($criterio == 'R')
            data_list.push("{{$data['verde'][$i]['rendimiento']}}");
            @else
            data_list.push("{{$data['verde'][$i]['desecho']}}");
            @endif
        }
        else if (label == 'Cosecha') {
            @if($criterio == 'R')
            data_list.push("{{$data['cosecha'][$i]['rendimiento']}}");
            @else
            data_list.push(0);
            @endif
        }
        else if (label == 'Blanco') {
            @if($criterio == 'R')
            data_list.push("{{$data['blanco'][$i]['rendimiento']}}");
            @else
            data_list.push("{{$data['blanco'][$i]['desecho']}}");
            @endif
        }
        @endfor

            datasets = [{
            label: label + ' ',
            data: data_list,
            //backgroundColor: '#8c99ff54',
            borderColor: 'black',
            borderWidth: 2,
            fill: false,
        }];

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