<script src="https://bernii.github.io/gauge.js/dist/gauge.min.js"></script>

<canvas id="canvas_precio2"></canvas>

<script>
    render_gauge('canvas_precio2', '{{number_format($precio_x_ramo, 2)}}', [{
        desde: 0,
        hasta: 2,
        color: '#f03e3e'    // red
    }, {
        desde: 2,
        hasta: 2.1,
        color: '#fd0'   // orange
    }, {
        desde: 2.1,
        hasta: 3,
        color: '#30b32d'    // green
    }]);
</script>