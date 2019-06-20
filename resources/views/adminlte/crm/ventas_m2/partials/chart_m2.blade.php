<canvas id="chart_m2" width="100%" height="40" style="margin-top: 5px"></canvas>

<script>
    construir_char('Ventas m2', 'chart_m2');

    function construir_char(label, id) {
        labels = [];
        datasets = [];
        data_list = [];
        data_tallos = [];
        @for($i = 0; $i < count($meses); $i++)
        labels.push("{{getMeses(TP_ABREVIADO)[$meses[$i]['mes'] - 1]. '-'.$meses[$i]['anno']}}");

        data_list.push("{{$array_valor[$i]}}");
        @endfor

            datasets = [{
            label: label + ' ',
            data: data_list,
            backgroundColor: '#ADD8E6',
            borderColor: 'black',
            borderWidth: 2,
            fill: false,
        }];

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