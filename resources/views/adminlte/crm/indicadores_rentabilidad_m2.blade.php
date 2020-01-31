<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<br>
<div id="div_chart_rentabilidad_m2_mensual"></div>

<script>
    google.charts.load('current', {'packages': ['line', 'corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var chartDiv = document.getElementById('div_chart_rentabilidad_m2_mensual');

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Semanas');
        data.addColumn('number', "Ventas/m2/año");
        data.addColumn('number', "Costos/m2/año");
        data.addColumn('number', "Rentablididad/m2/año");

        data.addRows([
            ['1901', 21, 23, 0.1],
            ['1902', 22, 21, 0.2],
            ['1903', 23, 20, 0.3],
            ['1904', 21, 22, -0.1],
            ['1905', 20, 21, -0.2],
            ['1906', 23, 23, 0.1],
            ['1907', 22, 24, -0.1],
            ['1908', 21, 23, -0.3],
            ['1909', 23, 21, 0.2],
            ['1910', 20, 22, 0.1],
            ['1911', 24, 20, 0.3],
            ['1912', 21, 23, 0.1]
        ]);

        var opciones = {
            chart: {
                title: 'Rentabilidad/m2 (4 meses)'
            },
            width: '100%',
            height: 400,
            series: {
                // Gives each series an axis name that matches the Y-axis below.
                0: {axis: 'Ventas'},
                1: {axis: 'Costos'},
                2: {axis: 'Rentabilidad'}
            },
            axes: {
                // Adds labels to each axis; they don't have to match the axis names.
                y: {
                    Ventas: {label: 'Ventas'},
                    Costos: {label: 'Costos'},
                    Rentabilidad: {label: 'Rentabilidad'}
                }
            },
            selectionMode: 'multiple',
        };

        var Chart = new google.charts.Line(chartDiv);
        Chart.draw(data, opciones);
    }
</script>