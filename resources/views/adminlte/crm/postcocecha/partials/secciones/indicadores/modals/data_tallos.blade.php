<canvas id="chart_data_tallos" width="100%" height="40" style="margin-top: 5px"></canvas>

<script>
    construir_char();

    function construir_char() {
        labels = [];
        colores = [];
        data_list = [];
        @for($i = 0; $i < count($arreglo_variedades); $i++)
        labels.push("{{$arreglo_variedades[$i]['variedad']->nombre.': '.$arreglo_variedades[$i]['tallos']}}");
        data_list.push("{{$arreglo_variedades[$i]['tallos']}}");
        colores.push("{{$arreglo_variedades[$i]['variedad']->color}}");
        @endfor

            datasets = [{
            data: data_list,
            backgroundColor: colores,
            borderColor: colores,
            borderWidth: 0,
            hoverBorderColor: colores,
            hoverBorderWidth: 5,
        }];

        ctx = document.getElementById('chart_data_tallos').getContext('2d');
        myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                tooltips: {
                    mode: 'point' // nearest, point, index, dataset, x, y
                },
                cutoutPercentage: 50,
                //circumference: 1,
                legend: {
                    display: true,
                    position: 'right',
                    fullWidth: false,
                    onClick: function () {
                    },
                    onHover: function () {
                    },
                    reverse: true,
                },
            }
        });
    }
</script>