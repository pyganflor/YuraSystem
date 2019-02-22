<canvas id="chart_recepciones" width="100%" height="40"></canvas>

<script>
    labels = [];
    data = [];
    @foreach($labels as $l)
    labels.push("{{$l->mes}}");
    data.push("{{$l->cantidad}}");
            @endforeach

    var ctx = document.getElementById("chart_recepciones").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tallos ',
                data: data,
                // backgroundColor: '#B9FFB4',
                borderColor: 'red',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            tooltips: {
                mode: 'point' // nearest, point, index, dataset, x, y
            },
            legend: {
                display: true,
                position: 'top',
                fullWidth: false,
                onClick: function () {
                    alert('ok')
                },
                onHover: function () {
                },
                reverse: true,
            }
        }
    });
</script>