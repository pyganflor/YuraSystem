<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#cajas-chart" data-toggle="tab" aria-expanded="true">Cajas</a></li>
        <li class=""><a href="#tallos-chart" data-toggle="tab" aria-expanded="false">Tallos</a></li>
        <li class=""><a href="#calibres-chart" data-toggle="tab" aria-expanded="false">Calibres</a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="cajas-chart" style="position: relative; height: 300px;">
            <canvas id="chart_annos_cajas" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="tallos-chart" style="position: relative; height: 300px;">
            <canvas id="chart_annos_tallos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="calibres-chart" style="position: relative; height: 300px;">
            <canvas id="chart_annos_calibres" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>

<script>
    construir_char_annos('Cajas', 'chart_annos_cajas');
    construir_char_annos('Tallos', 'chart_annos_tallos');
    construir_char_annos('Calibres', 'chart_annos_calibres');

    function construir_char_annos(label, id) {
        labels = [];
        datasets = [];
        @for($i = 0; $i < count($labels); $i++)
        labels.push("{{$labels[$i]}}");
                @endfor

                {{-- Data_list --}}
                @foreach($annos as $pos_a => $a)
            data_list = [];
        if (label == 'Cajas') {
            @foreach($a['cajas'] as $caja)
            data_list.push("{{$caja}}");
            @endforeach
        }
        else if (label == 'Tallos') {
            @foreach($a['tallos'] as $tallos)
            data_list.push("{{$tallos}}");
            @endforeach
        }
        else {
            @foreach($a['calibre'] as $calibres)
            data_list.push("{{$calibres}}");
            @endforeach
        }

        datasets.push({
            label: '{{$a['anno']}}' + ' ',
            data: data_list,
            backgroundColor: '{{getColores()[$pos_a]->fondo}}',
            borderColor: '{{getColores()[$pos_a]->fondo}}',
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
