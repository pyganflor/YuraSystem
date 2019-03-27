<canvas id="chart_data_tallos" width="100%" height="40" style="margin-top: 5px"></canvas>

<script>
    construir_char_acumulado('Tallos', 'chart_data_tallos');

    function construir_char_acumulado(label, id) {
        labels = [];
        datasets = [];
        @for($i = 0; $i < count($labels); $i++)
        labels.push("{{$labels[$i]->dia}}");
        @endfor

                {{-- Data_list --}}
                @foreach($arreglo_variedades as $variedad)
            data_list = [];
        @foreach($variedad['tallos'] as $tallos)
        data_list.push("{{$tallos}}");
        @endforeach

        datasets.push({
            label: '{{$variedad['variedad']->nombre}}' + ' ',
            data: data_list,
            backgroundColor: '{{$variedad['variedad']->color}}',
            borderColor: '{{$variedad['variedad']->color}}',
            borderWidth: 2,
            fill: false,
        });
        @endforeach

            ctx = document.getElementById(id).getContext('2d');
        myChart = new Chart(ctx, {
            type: 'bar',
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
