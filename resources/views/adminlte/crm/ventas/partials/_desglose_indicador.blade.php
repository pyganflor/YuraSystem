<canvas id="chart_desglose_indicador" width="100%" height="40" style="margin-top: 5px"></canvas>

<script>
    construir_char_desglose_indicador('{{$option}}');

    function construir_char_desglose_indicador(label) {
        labels = [];
        datasets = [];
        @for($i = 0; $i < count($labels); $i++)
        labels.push("{{$labels[$i]->dia}}");
        @endfor

                {{-- Data_list --}}
                @foreach($arreglo_variedades as $variedad)
            data_list = [];
        if (label == 'valor') {
            @foreach($variedad['valores'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        } else if (label == 'cajas') {
            @foreach($variedad['cajas'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        } else if (label == 'precios') {
            @foreach($variedad['precios'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        } else if (label == 'precios') {
            @foreach($variedad['precios'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        } else if (label == 'tallos') {
            @foreach($variedad['tallos'] as $item)
            data_list.push("{{$item}}");
            @endforeach
        }


        datasets.push({
            label: '{{$variedad['variedad']->nombre}}' + ' ',
            data: data_list,
            backgroundColor: '{{$variedad['variedad']->color}}',
            borderColor: '{{$variedad['variedad']->color}}',
            borderWidth: 2,
            fill: false,
        });
        @endforeach

            ctx = document.getElementById('chart_desglose_indicador').getContext('2d');
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