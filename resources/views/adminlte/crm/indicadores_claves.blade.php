<div class="box box-info">
    <div class="box-body" style="overflow-x: scroll">
        <table class="table-responsive" width="100%">
            <tr>
                <th class="text-center">Precio</th>
                <th class="text-center">Ramos/m<sup>2</sup>/año</th>
                <th class="text-center">Calibre</th>
                <th class="text-center">Tallos/m<sup>2</sup></th>
                <th class="text-center">Ciclo</th>
            </tr>
            <tr>
                <th class="text-center">
                    <canvas id="canvas_precio2" style="width: 210px"></canvas>
                </th>
                <th class="text-center">
                    <canvas id="canvas_ramos_m2_anno2" style="width: 210px"></canvas>
                </th>
                <th class="text-center">
                    <canvas id="canvas_calibre2" style="width: 210px"></canvas>
                </th>
                <th class="text-center">
                    <canvas id="canvas_tallos_m2_2" style="width: 210px"></canvas>
                </th>
                <th class="text-center">
                    <canvas id="canvas_ciclo2" style="width: 210px"></canvas>
                </th>
            </tr>
            <tr>
                <th class="text-center">{{number_format($precio_x_ramo, 2)}}</th>
                <th class="text-center">{{number_format($ramos_m2_anno, 2)}}</th>
                <th class="text-center">{{number_format($calibre, 2)}}</th>
                <th class="text-center">{{number_format($tallos_m2, 2)}}</th>
                <th class="text-center">{{number_format($ciclo, 2)}}</th>
            </tr>
        </table>
    </div>
</div>

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
    }], true);
    render_gauge('canvas_ramos_m2_anno2', '{{number_format($ramos_m2_anno, 2)}}', [{
        desde: 1,
        hasta: 13,
        color: '#f03e3e'    // red
    }, {
        desde: 13,
        hasta: 17,
        color: '#fd0'   // orange
    }, {
        desde: 17,
        hasta: 20,
        color: '#30b32d'    // green
    }]);
    render_gauge('canvas_calibre2', '{{number_format($calibre, 2)}}', [{
        desde: 1,
        hasta: 7.4,
        color: '#30b32d'    // green
    }, {
        desde: 7.4,
        hasta: 7.8,
        color: '#fd0'   // orange
    }, {
        desde: 7.8,
        hasta: 16,
        color: '#f03e3e'    // red
    }]);
    render_gauge('canvas_tallos_m2_2', '{{number_format($tallos_m2, 2)}}', [{
        desde: 1,
        hasta: 35,
        color: '#f03e3e'    // red
    }, {
        desde: 35,
        hasta: 45,
        color: '#fd0'   // orange
    }, {
        desde: 45,
        hasta: 60,
        color: '#30b32d'    // green
    }]);
    render_gauge('canvas_ciclo2', '{{number_format($ciclo, 2)}}', [{
        desde: 1,
        hasta: 115,
        color: '#30b32d'    // green
    }, {
        desde: 115,
        hasta: 125,
        color: '#fd0'   // orange
    }, {
        desde: 125,
        hasta: 150,
        color: '#f03e3e'    // red
    }]);
</script>