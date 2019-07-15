<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <strong>$/m<sup>2</sup>/año (4 semanas)</strong>
        </h3>
    </div>
    <div class="box-body">
        <canvas id="chart_mensual" width="100%" height="40" style="margin-top: 5px"></canvas>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <strong>$/m<sup>2</sup>/año (1 año)</strong>
        </h3>
    </div>
    <div class="box-body">
        <canvas id="chart_anual" width="100%" height="40" style="margin-top: 5px"></canvas>
    </div>
</div>
{{$total_mensual}}
--------
{{$total_anual}}
<script>
    construir_mensual();
    construir_anual();

    function construir_mensual() {
        labels = [];
        data_list = [];
        data_colores = [];
        @for($i = 0; $i < count($variedades); $i++)
        labels.push("{{$variedades[$i]['variedad']->siglas}}");
        data_colores.push("{{$variedades[$i]['variedad']->color}}");
        @if($variedades[$i]['area_cerrada'] > 0)
        data_list.push("{{round(((($variedades[$i]['venta'] / $variedades[$i]['area_cerrada']) * $variedades[$i]['ciclo_anno']) / $total_mensual) * 100, 2)}}");
        @else
        data_list.push(0);
        @endif
                @endfor

            datasets = [{
            label: ' ',
            data: data_list,
            backgroundColor: data_colores,
            borderColor: 'black',
            borderWidth: 1,
            fill: false,
        }];

        ctx = document.getElementById("chart_mensual").getContext('2d');
        myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            display: false,
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
                    position: 'top',
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

    function construir_anual() {
        labels = [];
        data_list = [];
        data_colores = [];
        @for($i = 0; $i < count($variedades); $i++)
        labels.push("{{$variedades[$i]['variedad']->siglas}}");
        data_colores.push("{{$variedades[$i]['variedad']->color}}");
        data_list.push("{{round((($variedades[$i]['venta_anual'] / round($variedades[$i]['area_anual'] * 10000, 2)) / $total_anual) * 100, 2)}}");
        @endfor

            datasets = [{
            label: ' ',
            data: data_list,
            backgroundColor: data_colores,
            borderColor: 'black',
            borderWidth: 1,
            fill: false,
        }];

        ctx = document.getElementById("chart_anual").getContext('2d');
        myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            display: false,
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