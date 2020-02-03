<br>
<canvas id="div_chart_rentabilidad_m2_mensual" style="width: 100%; height: 400px"></canvas>

<script>
    var grafica = document.getElementById("div_chart_rentabilidad_m2_mensual").getContext('2d');

    Chart.defaults.global.defaultFontFamily = "Arial";
    Chart.defaults.global.defaultFontStyle = "bold";
    Chart.defaults.global.defaultFontSize = 13;

    var ventas = {
        label: 'Ventas/m2/año',
        data: [22, 23, 21, 24, 21, 22, 25, 26, 27, 22, 22, 23, 21, 24, 21, 22, 22, 23, 21, 24, 21, 22, 25, 26, 27, 22, 22, 23, 21, 24, 21, 22],
        fill: false,
        lineTension: 0.3,
        borderColor: 'blue',
        //borderDash: [15, 3],
        pointBorderColor: 'blue',
        pointBackgroundColor: 'blue',
        pointRadius: 1,
        pointHoverRadius: 10,
        pointStyle: 'triangle',
        backgroundColor: 'blue',
        yAxisID: "y-axis-a"
    };

    var costos = {
        label: 'Costos/m2/año',
        data: [25, 21, 25, 26, 23, 24, 21, 22, 24, 25, 25, 21, 25, 26, 23, 24, 25, 21, 25, 26, 23, 24, 21, 22, 24, 25, 25, 21, 25, 26, 23, 24],
        fill: false,
        lineTension: 0.3,
        borderColor: 'red',
        //borderDash: [15, 3],
        pointBorderColor: 'red',
        pointBackgroundColor: 'red',
        pointRadius: 1,
        pointHoverRadius: 10,
        pointStyle: 'rect',
        backgroundColor: 'red',
        yAxisID: "y-axis-a"
    };

    var rentabilidad = {
        label: 'Rentabilidad/m2/año',
        data: [0.35, 0.24, -0.5, 0.65, 0.1, 0.24, 0.31, -0.24, 0.37, 1.25, 0.35, 0.24, -0.5, 0.65, 0.1, 0.24, 0.35, 0.24, -0.5, 0.65, 0.1, 0.24, 0.31, -0.24, 0.37, 1.25, 0.35, 0.24, -0.5, 0.65, 0.1, 0.24],
        fill: false,
        lineTension: 0.3,
        borderColor: 'green',
        //borderDash: [5, 5],
        pointBorderColor: 'black',
        pointBackgroundColor: 'green',
        pointRadius: 1,
        pointHoverRadius: 7,
        pointStyle: 'circle',
        backgroundColor: 'green',
        yAxisID: "y-axis-b",

        type: 'line',
    };

    var data = {
        labels: ["1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908", "1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908", "1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908", "1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908"],
        datasets: [rentabilidad, ventas, costos]
    };

    var opciones = {
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    color: "darkgray"
                },
                scaleLabel: {
                    display: true,
                    labelString: "Semanas",
                    fontColor: "gray"
                },
            }],
            yAxes: [{
                id: "y-axis-a",
                gridLines: {
                    color: "black",
                    borderDash: [2, 5],
                },
                scaleLabel: {
                    display: true,
                    labelString: "Ventas y Costos",
                    fontColor: "black"
                },
                ticks: {
                    beginAtZero: true,
                    max: 30,
                },
                position: "left"
            }, {
                id: "y-axis-b",
                gridLines: {
                    display: false,
                    color: "black",
                    borderDash: [2, 5],
                },
                scaleLabel: {
                    display: true,
                    labelString: "Rentabilidad",
                    fontColor: "green"
                },
                ticks: {
                    min: -2,
                    max: 5,
                },
                position: "right"
            }]
        }
    };

    var lineChart = new Chart(grafica, {
        type: 'line',
        data: data,
        options: opciones
    });
</script>