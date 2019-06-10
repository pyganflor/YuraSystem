<div class="chart tab-pane" id="areas-chart" style="position: relative">
    <canvas id="chart_areas" width="100%" height="33" style="margin-top: 5px"></canvas>
</div>

<script>
    construir_char_annos('Áreas', 'chart_areas');

    function construir_char_annos(label, id) {
        labels = [];
        datasets = [];
        @foreach($semanas as $sem)
        labels.push("{{$sem->codigo}}");
        @endforeach

                {{-- Data_list --}}
                @foreach($grafica as $pos_a => $a)
            data_list = [];
        @foreach($a['valores'] as $item)
        data_list.push("{{round($item/10000, 2)}}");
        @endforeach

        datasets.push({
            label: '{{$a['variedad']->nombre}}' + ' ',
            data: data_list,
            backgroundColor: '{{$a['variedad']->color}}',
            borderColor: '{{$a['variedad']->color}}',
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