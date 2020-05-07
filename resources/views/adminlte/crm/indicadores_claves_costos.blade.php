<legend style="margin-bottom: 5px"></legend>
<div style="overflow-x: scroll">
    <table class="table-responsive" width="100%">
        <tr>
            <th class="text-center">Campo/<sup>ha</sup>/semana</th>
            <th class="text-center">Cosecha x Tallo</th>
            <th class="text-center">Postcosecha x Tallo</th>
            <th class="text-center">Total x Tallo</th>
        </tr>
        <tr>
            <th class="text-center">
                <canvas id="canvas_campo_ha_semana" style="width: 210px"></canvas>
            </th>
            <th class="text-center">
                <canvas id="canvas_cosecha_x_tallo" style="width: 210px"></canvas>
            </th>
            <th class="text-center">
                <canvas id="canvas_postcosecha_x_tallo" style="width: 210px"></canvas>
            </th>
            <th class="text-center">
                <canvas id="canvas_total_x_tallo" style="width: 210px"></canvas>
            </th>
        </tr>
        <tr>
            <th class="text-center">{{number_format(explode('|', $costos_campo_semana)[0], 2)}}</th>
            <th class="text-center">¢{{number_format($costos_cosecha_x_tallo, 2)}}</th>
            <th class="text-center">¢{{number_format($costos_postcosecha_x_tallo, 2)}}</th>
            <th class="text-center">¢{{number_format($costos_total_x_tallo, 2)}}</th>
        </tr>
    </table>
</div>

<script>
    render_gauge('canvas_campo_ha_semana', '{{round(explode('|', $costos_campo_semana)[0], 2)}}', rangos_costos_campo_ha_semana, true);
    render_gauge('canvas_cosecha_x_tallo', '{{round($costos_cosecha_x_tallo, 2)}}', rangos_costos_cosecha_tallo, true);
    render_gauge('canvas_postcosecha_x_tallo', '{{round($costos_postcosecha_x_tallo, 2)}}', rangos_costos_postcosecha_tallo, true);
    render_gauge('canvas_total_x_tallo', '{{round($costos_total_x_tallo, 2)}}', rangos_costos_total_tallo, true);
</script>