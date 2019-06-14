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
        @foreach($labels as $sem)
        labels.push("{{$sem->semana}}");
        @endforeach

                {{-- Data_list --}}
                @foreach($data as $pos_a => $a)
            data_list = [];
        if (label == 'Area') {
            @foreach($a['area'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }
        else if (label == 'Ciclo') {
            @foreach($a['ciclo'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }
        else if (label == 'Tallos') {
            @foreach($a['tallos'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }
        else if (label == 'Ramos') {
            @foreach($a['ramos'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }
        else {
            @foreach($a['ramos_anno'] as $item)
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