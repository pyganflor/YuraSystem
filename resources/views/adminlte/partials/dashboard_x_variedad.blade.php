{{-- COLORES SEMAFOROS --}}
@php
    $color_1 = $variedad == '' ? getColorByIndicador('D9') : getColorByIndicadorVariedad('D9', $variedad->id_variedad);   //  venta_m2_anno_mensual
    $color_1_1 = $variedad == '' ? getColorByIndicador('D10') : getColorByIndicadorVariedad('D10', $variedad->id_variedad);    //  venta_m2_anno_anual
    $color_2 = $variedad == '' ? getColorByIndicador('DA1') : getColorByIndicadorVariedad('DA1', $variedad->id_variedad);  //  ciclo
    $color_3 = $variedad == '' ? getColorByIndicador('D1') : getColorByIndicadorVariedad('D1', $variedad->id_variedad);   //  calibre
    $color_4 = $variedad == '' ? getColorByIndicador('D3') : getColorByIndicadorVariedad('D3', $variedad->id_variedad);   //  precio_x_ramo
    $color_5 = $variedad == '' ? getColorByIndicador('D12') : getColorByIndicadorVariedad('D12', $variedad->id_variedad);   //  tallos_m2
    $color_6 = $variedad == '' ? getColorByIndicador('D8') : getColorByIndicadorVariedad('D8', $variedad->id_variedad);   //  ramos_m2_anno
    $color_7 = $variedad == '' ? getColorByIndicador('D14') : getColorByIndicadorVariedad('D14', $variedad->id_variedad);   //  precio_x_tallo
    $color_8 = getColorByIndicador('C3');   //  costos_campo_semana
    $color_9 = getColorByIndicador('C4');   //  costos_cosecha_x_tallo
    $color_10 = getColorByIndicador('C5');   //  costos_postcosecha_x_tallo
    $color_11 = getColorByIndicador('C6');   //  costos_total_x_tallo
    $color_12 = getColorByIndicador('C9');   //  costos_m2_mensual
    $color_13 = getColorByIndicador('C10');   //  costos_m2_anual
@endphp
<div style="overflow-x: scroll;">
    <div id="chart_org"></div>
    <script>
        google.charts.load('current', {packages: ["orgchart"]});
        google.charts.setOnLoadCallback(drawChartOrg);

        function drawChartOrg() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');

            // For each orgchart box, provide the name, manager, and tooltip to show.
            data.addRows([
                [{'v': 'Rentabilidad', 'f': '<strong>Rentabilidad/m<sup>2</sup></strong>'}, '', 'Rentabilidad'],
                [{
                    'v': 'Ventas_m2_anno',
                    'f': '<strong style="color:{{$color_1}}"><small>$</small><span id="span_venta_m2_mensual">{{number_format($venta_m2_anno_mensual, 2)}}</span><small><sup>(4 meses)</sup></small></strong>' +
                    '<br><strong style="color:{{$color_1_1}}"><small>$</small><span id="span_venta_m2_anno">{{number_format($venta_m2_anno_anual, 2)}}</span><small><sup>(1 año)</sup></small></strong>' +
                    '<br><button type="button" class="btn btn-xs btn-block btn-default" onclick="mostrar_indicadores_claves(0, {{$variedad->id_variedad}})" style="color: black">Ventas/m<sup>2</sup>/año</button>'
                }, 'Rentabilidad', 'Ventas/m2/año'],
                [{
                    'v': 'Costos',
                    'f': '<strong><small>$</small><span id="span_costos_m2_mensual">{{number_format($costos_m2_mensual, 2)}}</span><small><sup>(4 meses)</sup></small></strong>' +
                    '<br><strong><small>$</small><span id="span_costos_m2_anual">{{number_format($costos_m2_anual, 2)}}</span><small><sup>(4 meses)</sup></small></strong>' +
                    '<br><button type="button" class="btn btn-xs btn-block btn-default" onclick="mostrar_indicadores_claves(3, {{$variedad->id_variedad}})" style="color: black">Costos/m<sup>2</sup>/año</button>'
                }, 'Rentabilidad', 'Costos/m2/año'],
                [{
                    'v': 'C1', 'f': '<strong></strong>' +
                    '<br><strong title="Campo/ha/Semana" style="color:{{$color_8}}"><small>Campo/<sup>ha</sup>/Semana: </small><span id="span_costos_campo_semana">${{number_format(explode('|', $costos_campo_semana)[0] , 2)}}</span></strong>' +
                    '<br><strong title="Cosecha x Tallo" style="color:{{$color_9}}"><small>Cosecha x Tallo: </small><span id="span_costos_cosecha_tallo">¢{{number_format($costos_cosecha_x_tallo, 2)}}</span></strong>' +
                    '<br><strong title="Postcosecha x Tallo" style="color:{{$color_10}}"><small>Postcosecha x Tallo: </small><span id="span_costos_postcosecha_tallo">¢{{number_format($costos_postcosecha_x_tallo, 2)}}</span></strong>' +
                    '<br><strong title="Total x Tallo" style="color:{{$color_11}}"><small>Total x Tallo: </small><span id="span_costos_total_tallo">¢{{number_format($costos_total_x_tallo, 2)}}</span></strong>' +
                    '<br><button type="button" class="btn btn-xs btn-block btn-default" style="color: black" onclick="mostrar_indicadores_claves(2)">Indicadores claves</button>'
                }, 'Costos', 'C1'],
                [{
                    'v': 'C2',
                    'f': '<strong title="Total"><small>Total: </small><span id="span_costos_total">${{number_format(explode(':', $costos_mano_obra)[1] + explode(':', $costos_insumos)[1] + explode(':', $costos_fijos)[1] + explode(':', $costos_regalias)[1] , 2)}}</span></strong>' +
                    '<br><strong title="Mano de Obra, Semana: {{explode(':', $costos_mano_obra)[0]}}"><small>MO: </small><span id="span_costos_mano_obra">${{number_format(explode(':', $costos_mano_obra)[1] , 2)}}</span></strong>' +
                    '<br><strong title="MP, Semana: {{explode(':', $costos_insumos)[0]}}"><small>MP: </small><span id="span_costos_insumos">${{number_format(explode(':', $costos_insumos)[1] , 2)}}</span></strong>' +
                    '<br><strong title="Fijos, Semana: {{explode(':', $costos_fijos)[0]}}"><small>Fijos: </small><span id="span_costos_fijos">${{number_format(explode(':', $costos_fijos)[1] , 2)}}</span></strong>' +
                    '<br><strong title="Regalías, Semana: {{explode(':', $costos_regalias)[0]}}"><small>Regalías: </small><span id="span_costos_regalias">${{number_format(explode(':', $costos_regalias)[1] , 2)}}</span></strong>' +
                    '<br><button type="button" class="btn btn-xs btn-block btn-default" style="color: black" disabled>Datos importantes</button>'
                }, 'Costos', 'C2'],
                [{
                    'v': 'Indicadores_claves',
                    'f': '<strong style="color: {{$color_4}}"><small>Precio: $</small><span id="span_precio_x_ramo" title="Ramo">{{number_format($precio_x_ramo, 2)}}</span></strong>-<span title="Tallo" style="color:{{$color_7}}"><small>$</small>{{$precio_x_tallo}}</span>' +
                    '<br><strong style="color:{{$color_6}}"><small>Productividad: </small><span id="span_ramos_m2_anno">{{number_format($ramos_m2_anno, 2)}}</span></strong>' +
                    '<br><strong style="color: {{$color_3}}"><small>Calibre: </small><span id="span_calibre">{{$calibre}}</span></strong>' +
                    '<br><strong style="color: {{$color_5}}"><small>Tallos x m<sup>2</sup>: </small><span id="span_tallos_m2">{{number_format($tallos_m2, 2)}}</span></strong>' +
                    '<br><strong style="color: {{$color_2}}"><small>Ciclo: </small><span id="span_ciclo">{{number_format($ciclo, 2)}}</span></strong>' +
                    '<br><button type="button" class="btn btn-xs btn-block btn-default" onclick="mostrar_indicadores_claves(1, {{$variedad->id_variedad}})" style="color: black">Indicadores claves</button>'
                }, 'Ventas_m2_anno', 'Indicadores claves'],
                [{
                    'v': 'Datos_importantes',
                    'f': '<strong><small>Área: </small><span id="span_area_produccion">{{number_format(round($area_produccion / 10000, 2), 2)}}</span></strong>' +
                    '<br><strong><small>Venta: </small>$<span id="span_valor">{{number_format($valor, 2)}}</span></strong>' +
                    '<br><strong title="Tallos cosechados"><small>T/cosechados: </small><span id="span_tallos_cosechados">{{number_format($tallos_cosechados)}}</span></strong>' +
                    '<br><strong title="Tallos clasificados"><small>T/clasificados: </small><span id="span_tallos">{{number_format($tallos)}}</span></strong>' +
                    '<br><strong title="Cajas exportadas"><small>Cajas exp: </small>{{number_format($cajas_exportadas, 2)}}</strong>' +
                    '<br><button type="button" class="btn btn-xs btn-block btn-default" disabled style="color: black">Datos importantes</button>'
                }, 'Ventas_m2_anno', 'Datos importantes'],
            ]);

            var options = {
                'allowHtml': true,
                'allowCollapse': true,
                'color': '#edf7ff',
                'nodeClass': 'nodo_org',
                'selectedNodeClass': 'nodo_org_selected',
                'size': 'large',
            };

            // Create the chart.
            var chart = new google.visualization.OrgChart(document.getElementById('chart_org'));
            // Draw the chart, setting the allowHtml option to true for the tooltips.
            chart.draw(data, options);
        }
    </script>
</div>

<div id="div_indicadores_claves"></div>