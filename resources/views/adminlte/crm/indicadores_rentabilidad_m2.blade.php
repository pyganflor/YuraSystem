<br>
<div id="div_chart_rentabilidad_m2_mensual"></div>

<script>
    var grafica = document.getElementById("div_chart_rentabilidad_m2_mensual");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var ventas = {
        label: 'A',
        data: [22, 23, 21, 24, 21, 22, 25, 26, 27, 22, 22, 23, 21, 24, 21, 22, 22, 23, 21, 24, 21, 22, 25, 26, 27, 22, 22, 23, 21, 24, 21, 22],
        fill: false,
        lineTension: 0.3,
        borderColor: 'blue',
        borderDash: [5, 5],
        pointBorderColor: 'black',
        pointBackgroundColor: 'red',
        pointRadius: 6,
        pointHoverRadius: 10,
        pointStyle: 'triangle',
        backgroundColor: 'blue',
        yAxisID: "y-axis-a"
    };

    var costos = {
        label: 'B',
        data: [25, 21, 25,26, 23, 24, 21, 22, 24, 25, 25, 21, 25,26, 23, 24, 25, 21, 25,26, 23, 24, 21, 22, 24, 25, 25, 21, 25,26, 23, 24],
        fill: false,
        lineTension: 0.3,
        borderColor: 'red',
        borderDash: [5, 5],
        pointBorderColor: 'black',
        pointBackgroundColor: 'blue',
        pointRadius: 6,
        pointHoverRadius: 10,
        pointStyle: 'rect',
        backgroundColor: 'red',
        yAxisID: "y-axis-a"
    };

    var rentabilidad = {
        label: 'C',
        data: [0.35, 0.24, -0.5,0.65, 0.1, 0.24, 0.31, -0.24, 0.37, 1.25, 0.35, 0.24, -0.5,0.65, 0.1, 0.24, 0.35, 0.24, -0.5,0.65, 0.1, 0.24, 0.31, -0.24, 0.37, 1.25, 0.35, 0.24, -0.5,0.65, 0.1, 0.24],
        fill: false,
        lineTension: 0.3,
        borderColor: 'green',
        borderDash: [5, 5],
        pointBorderColor: 'black',
        pointBackgroundColor: 'green',
        pointRadius: 6,
        pointHoverRadius: 10,
        pointStyle: 'circle',
        backgroundColor: 'green',
        yAxisID: "y-axis-a"
    };

    var data = {
        labels: ["1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908", "1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908", "1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908", "1901", "1902", "1903", "1904", "1905", "1906", "1907", "1908"],
        datasets: [ventas, costos, rentabilidad]
    };

    var opciones = {
        scales: {
            xAxes: [{
                gridLines: {
                    display: true,
                    color: "darkgray"
                },
                scaleLabel: {
                    display: true,
                    labelString: "Semanas",
                    fontColor: "black"
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
            }]
        }
    };

    var lineChart = new Chart(grafica, {
        type: 'line',
        data: data,
        options: opciones
    });
</script>