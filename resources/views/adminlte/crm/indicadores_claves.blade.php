<legend style="margin-bottom: 5px"></legend>
<table class="table-responsive" width="100%">
    <tr>
        <th class="text-center">Precio x Ramo</th>
        <th class="text-center">Precio x Tallo</th>
    </tr>
    <tr>
        <th class="text-center">
            <canvas id="canvas_precio2" style="width: 210px"></canvas>
        </th>
        <th class="text-center">
            <canvas id="canvas_precio_x_tallo2" style="width: 210px"></canvas>
        </th>
    </tr>
    <tr>
        <th class="text-center">{{number_format($precio_x_ramo, 2)}}</th>
        <th class="text-center">{{number_format($precio_x_tallo, 2)}}</th>
    </tr>
</table>
<legend style="margin-bottom: 5px"></legend>
<div style="overflow-x: scroll">
    <table class="table-responsive" width="100%">
        <tr>
            <th class="text-center">Ramos/m<sup>2</sup>/año</th>
            <th class="text-center">Calibre</th>
            <th class="text-center">Tallos/m<sup>2</sup></th>
            <th class="text-center">Ciclo</th>
        </tr>
        <tr>
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
            <th class="text-center">{{number_format($ramos_m2_anno, 2)}}</th>
            <th class="text-center">{{number_format($calibre, 2)}}</th>
            <th class="text-center">{{number_format($tallos_m2, 2)}}</th>
            <th class="text-center">{{number_format($ciclo, 2)}}</th>
        </tr>
    </table>
</div>

<script>
    render_gauge('canvas_precio2', '{{number_format($precio_x_ramo, 2)}}', rangos_precio, true);
    render_gauge('canvas_precio_x_tallo2', '{{number_format($precio_x_tallo, 2)}}', rangos_precio_tallo, true);
    render_gauge('canvas_ramos_m2_anno2', '{{number_format($ramos_m2_anno, 2)}}', rangos_ramos_m2_anno, true);
    render_gauge('canvas_calibre2', '{{number_format($calibre, 2)}}', rangos_calibre, true);
    render_gauge('canvas_tallos_m2_2', '{{number_format($tallos_m2, 2)}}', rangos_tallos_m2, true);
    render_gauge('canvas_ciclo2', '{{number_format($ciclo, 2)}}', rangos_ciclo, true);
</script>