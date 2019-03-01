<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#cajas-chart" data-toggle="tab" aria-expanded="true">Cajas</a></li>
        <li class=""><a href="#tallos-chart" data-toggle="tab" aria-expanded="false">Tallos</a></li>
        <li class=""><a href="#ramos-chart" data-toggle="tab" aria-expanded="false">Ramos</a></li>
        <li class=""><a href="#desechos-chart" data-toggle="tab" aria-expanded="false">Desechos</a></li>
        <li class=""><a href="#rendimientos-chart" data-toggle="tab" aria-expanded="false">Rendimientos</a></li>
        <li class=""><a href="#calibres-chart" data-toggle="tab" aria-expanded="false">Calibres</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="cajas-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_cajas" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="tallos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_tallos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="ramos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_ramos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="desechos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_desechos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="rendimientos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_rendimientos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="calibres-chart" style="position: relative; height: 300px;">
            <canvas id="chart_acumulado_calibres" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>

<script>
    construir_char_acumulado('Cajas', 'chart_acumulado_cajas');
    construir_char_acumulado('Ramos', 'chart_acumulado_ramos');
    construir_char_acumulado('Tallos', 'chart_acumulado_tallos');
    construir_char_acumulado('Desechos', 'chart_acumulado_desechos');
    construir_char_acumulado('Rendimientos', 'chart_acumulado_rendimientos');
    construir_char_acumulado('Calibres', 'chart_acumulado_calibres');

    function construir_char_acumulado(label, id) {
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
        labels.push("{{$labels[$i]['ano']. ' - '.$labels[$i]['mes']}}");
        @endif
        if (label == 'Cajas')
            data_list.push("{{$array_cajas[$i]}}");
        else if (label == 'Ramos')
            data_list.push("{{$array_ramos[$i]}}");
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
            borderColor: 'blue',
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
                            beginAtZero: true
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
