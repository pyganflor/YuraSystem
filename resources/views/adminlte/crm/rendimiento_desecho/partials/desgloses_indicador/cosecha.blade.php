<canvas id="chart_desgloses_cosecha" width="100%" height="33" style="margin-top: 5px"></canvas>

<script>
    construir_char_annos();

    function construir_char_annos() {
        labels = [];
        datasets = [];
        @for($i = 0; $i < 24; $i++)
        labels.push("{{getIntervalosHorasDiarias()[$i]['inicio'].'-'.getIntervalosHorasDiarias()[$i]['fin']}}");
        @endfor

                {{-- Data_list --}}
                @foreach($arreglo_dias as $pos_a => $a)
            data_list = [];
        @foreach($a['arreglo'] as $item)
        data_list.push("{{$item['valor']}}");
        @endforeach

        datasets.push({
            label: '{{$a['fecha']}}' + ' ',
            data: data_list,
            backgroundColor: '{{getListColores()[$pos_a]}}',
            borderColor: '{{getListColores()[$pos_a]}}',
            borderWidth: 1,
            fill: false,
        });
        @endforeach

            ctx = document.getElementById("chart_desgloses_cosecha").getContext('2d');
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