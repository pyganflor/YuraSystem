<div class="box box-info">
    <div class="box-body" style="overflow-x: scroll">
        <select name="variedad" id="variedad" class="pull-right" onchange="mostrar_indicadores_claves(0, '')">
            <option value="">Acumulado</option>
            @foreach(getVariedades()->where('estado', 1) as $v)
                <option value="{{$v->id_variedad}}">{{$v->nombre}}</option>
            @endforeach
        </select>
        <table class="table-responsive" width="100%">
            <tr>
                <th class="text-right" style="padding-right: 50px">$/m<sup>2</sup>/año (4 meses)</th>
                <th class="text-left" style="padding-left: 50px">$/m<sup>2</sup>/año (1 año)</th>
            </tr>
            <tr>
                <th class="text-right">
                    <canvas id="canvas_venta_m2_anno_mensual2" style="width: 210px"></canvas>
                </th>
                <th class="text-left">
                    <canvas id="canvas_venta_m2_anno_anual2" style="width: 210px"></canvas>
                </th>
            </tr>
            <tr>
                <th class="text-right" style="padding-right: 90px">{{number_format($venta_m2_anno_mensual, 2)}}</th>
                <th class="text-left" style="padding-left: 90px">{{number_format($venta_m2_anno_anual, 2)}}</th>
            </tr>
        </table>
    </div>
</div>

<script>
    render_gauge('canvas_venta_m2_anno_mensual2', '{{number_format($venta_m2_anno_mensual, 2)}}', rangos_venta_m2_mensual, true, 100);
    render_gauge('canvas_venta_m2_anno_anual2', '{{number_format($venta_m2_anno_anual, 2)}}', rangos_venta_m2_anno, true, 100);

    function select_variedad() {
        datos = {
            variedad: $('#variedad').val()
        };
        get_jquery('{{url('')}}')
    }
</script>