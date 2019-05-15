<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#cosecha-chart" data-toggle="tab" aria-expanded="false">Cosecha (Rendimiento)</a></li>
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
        if (label == 'Cosecha') {
            @for($i = 0; $i < count($s_cos); $i++)
            labels.push("{{$s_cos[$i]}}");
            @endfor
        }
        if (label == 'Verde') {
            @for($i = 0; $i < count($s_ver); $i++)
            labels.push("{{$s_ver[$i]}}");
            @endfor
        }
        if (label == 'Blanco') {
            @for($i = 0; $i < count($s_bla); $i++)
            labels.push("{{$s_bla[$i]}}");
            @endfor
        }

        {{-- Data_list --}}
        if (label == 'Cosecha') {
            @foreach($a_cos as $pos_a => $a)
                data_list = [];
            @foreach($a['arreglo'] as $valor)
            data_list.push("{{$valor}}");
            @endforeach

            datasets.push({
                label: '{{$a['anno']}}' + ' ',
                data: data_list,
                backgroundColor: '{{getListColores()[$pos_a]}}',
                borderColor: '{{getListColores()[$pos_a]}}',
                borderWidth: 1,
                fill: false,
            });
            @endforeach
        }
        if (label == 'Verde') {
            @foreach($a_ver as $pos_a => $a)
                data_list = [];
            @foreach($a['arreglo'] as $valor)
            data_list.push("{{$valor}}");
            @endforeach

            datasets.push({
                label: '{{$a['anno']}}' + ' ',
                data: data_list,
                backgroundColor: '{{getListColores()[$pos_a]}}',
                borderColor: '{{getListColores()[$pos_a]}}',
                borderWidth: 1,
                fill: false,
            });
            @endforeach
        }
        if (label == 'Blanco') {
            @foreach($a_bla as $pos_a => $a)
                data_list = [];
            @foreach($a['arreglo'] as $valor)
            data_list.push("{{$valor}}");
            @endforeach

            datasets.push({
                label: '{{$a['anno']}}' + ' ',
                data: data_list,
                backgroundColor: '{{getListColores()[$pos_a]}}',
                borderColor: '{{getListColores()[$pos_a]}}',
                borderWidth: 1,
                fill: false,
            });
            @endforeach
        }

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