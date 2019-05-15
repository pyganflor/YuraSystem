<canvas id="chart_rendimiento" width="100%" height="33" style="margin-top: 5px"></canvas>

<script>
    construir_char();

    function construir_char() {
        labels = [];
        datasets = [];
        data_list = [];
        @foreach($blanco->getIntervalosHoras() as $pos_intervalo => $intervalo)
        labels.push("{{$intervalo['hora_inicio']}}-{{$intervalo['hora_fin']}}");
        data_list.push("{{round($blanco->getTotalRamosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) /
        $blanco->personal, 2)}}");
        @endforeach

            datasets = [{
            label: 'Rendimiento ',
            data: data_list,
            //backgroundColor: '#8c99ff54',
            borderColor: 'blue',
            borderWidth: 2,
            fill: false,
        }];

        ctx = document.getElementById("chart_rendimiento").getContext('2d');
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