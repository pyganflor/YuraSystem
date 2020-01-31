<div class="nav-tabs-custom" style="cursor: move;">
    <ul class="nav nav-pills nav-justified">
        <li class="active li_tab_chart" id="li_tab_cajas"><a href="#cajas-chart" data-toggle="tab" aria-expanded="true">Cajas</a></li>
        <li class="li_tab_chart" id="li_tab_tallos"><a href="#tallos-chart" data-toggle="tab" aria-expanded="false">Tallos</a></li>
        <li class="li_tab_chart" id="li_tab_calibres"><a href="#calibres-chart" data-toggle="tab" aria-expanded="false">Calibres</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active div_tab_chart" id="cajas-chart" style="position: relative; height: 300px;">
            <canvas id="chart_cajas" width="100%" height="40" style="margin-top: 5px; background-color: white"></canvas>
        </div>
        <div class="chart tab-pane div_tab_chart" id="tallos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_tallos" width="100%" height="40" style="margin-top: 5px; background-color: white"></canvas>
        </div>
        <div class="chart tab-pane div_tab_chart" id="calibres-chart" style="position: relative; height: 300px;">
            <canvas id="chart_calibres" width="100%" height="40" style="margin-top: 5px; background-color: white"></canvas>
        </div>
    </div>
</div>
<script>
    construir_char_compuesto('Cajas', 'chart_cajas');
    construir_char_compuesto('Tallos', 'chart_tallos');
    construir_char_compuesto('Calibres', 'chart_calibres');

    function construir_char_compuesto(label, id) {
        labels = [];
        datasets = [];

        @foreach($labels as $label)
        @if($label >= substr($semana_actual, 2))
        labels.push('{{$label}}*');
        @else
        labels.push('{{$label}}');
        @endif
                @endforeach

                @foreach($datasets as $dataset)
            data_list = [];
        if (label == 'Cajas') {
            @foreach($dataset['data_cajas'] as $valor)
            data_list.push('{{$valor}}');
            @endforeach
        } else if (label == 'Tallos') {
            @foreach($dataset['data_tallos'] as $valor)
            data_list.push('{{$valor}}');
            @endforeach
        } else if (label == 'Calibres') {
            @foreach($dataset['data_calibres'] as $valor)
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
