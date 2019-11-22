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
    construir_char_simple('Cajas', 'chart_cajas');
    construir_char_simple('Tallos', 'chart_tallos');
    construir_char_simple('Calibres', 'chart_calibres');

    function construir_char_simple(label, id) {
        labels = [];
        datasets = [];

        @for($i = 0; $i < count($labels); $i++)
        labels.push('{{$labels[$i]}}');
        @endfor

                @for($i = 0; $i < count($datasets); $i++)
            data_list = [];
        if (label == 'Cajas')
            @for($y = 0; $y < count($datasets[$i]['data_cajas']); $y++)
            data_list.push('{{$datasets[$i]['data_cajas'][$y]}}');
        @endfor
        if (label == 'Tallos')
            @for($y = 0; $y < count($datasets[$i]['data_tallos']); $y++)
            data_list.push('{{$datasets[$i]['data_tallos'][$y]}}');
        @endfor
        if (label == 'Calibres')
            @for($y = 0; $y < count($datasets[$i]['data_calibres']); $y++)
            data_list.push('{{$datasets[$i]['data_calibres'][$y]}}');
        @endfor
        datasets.push({
            label: '{{$datasets[$i]['label']}}' + ' ',
            data: data_list,
            //backgroundColor: '#8c99ff54',
            borderColor: '{{$datasets[$i]['color']}}',
            borderWidth: 2,
            fill: false,
        });
        @endfor

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
