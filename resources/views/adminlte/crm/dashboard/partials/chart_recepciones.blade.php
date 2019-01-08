<canvas id="chart_recepciones" width="100%" height="40"></canvas>

<script>
    labels = [];
    data = [];
    @foreach($labels as $l)
    labels.push("{{getMeses()[$l->mes - 1] . '-' . $l->year}}");
    data.push("{{$l->cantidad}}");
            @endforeach

    var ctx = document.getElementById("chart_recepciones").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tallos ',
                data: data,
                backgroundColor: '#B9FFB4',
                borderColor: '#ce8483',
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
            legend: {
                display: true,
                position: 'bottom',
                fullWidth: false,
                onClick: function () {
                },
                onHover: function () {
                },
                reverse: true,
            }
        }
    });
</script>