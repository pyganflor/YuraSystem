<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class=""><a href="#valor-chart" data-toggle="tab" aria-expanded="false">Valor</a></li>
        <li class="active"><a href="#cajas-chart" data-toggle="tab" aria-expanded="true">Cajas</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane" id="valor-chart" style="position: relative; height: 300px;">
            <canvas id="chart_valor" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane active" id="cajas-chart" style="position: relative; height: 300px;">
            <canvas id="chart_cajas" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>

<script>
    construir_char('Cajas', 'chart_cajas');
    construir_char('Ramos', 'chart_valor');

    function construir_char(label, id) {
        labels = [];
        datasets = [];
        data_list = [];
        data_tallos = [];
        @for($i = 0; $i < count($labels); $i++)
        @if($periodo == 'diario')
        labels.push("{{$labels[$i]->dia}}");
        @elseif($periodo == 'semanal')
        labels.push("{{$labels[$i]->semana}}");
        @elseif($periodo == 'anual')
        labels.push("{{$labels[$i]->ano}}");
        @else
        labels.push("{{getMeses(TP_ABREVIADO)[$labels[$i]->mes - 1]. ' - '.$labels[$i]->ano}}");
        @endif
        if (label == 'Cajas')
            data_list.push("{{$array_cajas[$i]}}");
        /*else if (label == 'Ramos')
            data_list.push("{$array_ramos[$i]}}");*/
        else if (label == 'Tallos')
            data_list.push("{{$array_tallos[$i]}}");
        else if (label == 'Desechos')
            data_list.push("{{$array_desecho[$i]}}");
        else if (label == 'Rendimientos')
            data_list.push("{{$array_rendimiento[$i]}}");
        else
            data_list.push("{{$array_calibre[$i]}}");
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