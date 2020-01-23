@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard
@endsection

@section('css_inicio')
    <style>
        .nodo_org {
            background-color: #e9ecef !important;
            width: 200px;
            cursor: pointer;
            -webkit-box-shadow: 9px 8px 11px -2px rgba(0, 0, 0, 0.34);
            -moz-box-shadow: 9px 8px 11px -2px rgba(0, 0, 0, 0.34);
            box-shadow: 5px 3px 11px -2px rgba(0, 0, 0, 0.34);
            font-size: 1em;
        }

        .nodo_org_selected {
            background-color: #ccc9c9 !important;
        }
    </style>
@endsection

@section('script_inicio')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="https://bernii.github.io/gauge.js/dist/gauge.min.js"></script>

    <script>
        var rangos_venta_m2_mensual = [];
        @foreach(getIntervalosIndicador('D9') as $r)
        rangos_venta_m2_mensual.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
                @endforeach

        var rangos_venta_m2_anno = [];
        @foreach(getIntervalosIndicador('D10') as $r)
        rangos_venta_m2_anno.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
                @endforeach

        var rangos_precio = [];
        @foreach(getIntervalosIndicador('D3') as $r)
        rangos_precio.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
                @endforeach

        var rangos_ramos_m2_anno = [];
        @foreach(getIntervalosIndicador('D8') as $r)
        rangos_ramos_m2_anno.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
                @endforeach

        var rangos_calibre = [];
        @foreach(getIntervalosIndicador('D1') as $r)
        rangos_calibre.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
                @endforeach

        var rangos_tallos_m2 = [];
        @foreach(getIntervalosIndicador('D12') as $r)
        rangos_tallos_m2.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
                @endforeach

        var rangos_ciclo = [];
        @foreach(getIntervalosIndicador('DA1') as $r)
        rangos_ciclo.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
                @endforeach

        var rangos_precio_tallo = [];
        @foreach(getIntervalosIndicador('D14') as $r)
        rangos_precio_tallo.push({
            desde: parseFloat('{{$r->desde}}'),
            hasta: parseFloat('{{$r->hasta}}'),
            color: '{{$r->color}}',
        });
        @endforeach
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Bienvenido
            <small>a <a href="{{url('')}}">{{explode('//',url(''))[1]}}</a></small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Inicio
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        @if(count(getUsuario(Session::get('id_usuario'))->rol()->getSubmenusByTipo('C')) > 0)
            {{-- COLORES SEMAFOROS --}}
            @php
                $color_1 = getColorByIndicador('D9');   //  venta_m2_anno_mensual
                $color_1_1 = getColorByIndicador('D10');    //  venta_m2_anno_anual
                $color_2 = getColorByIndicador('DA1');  //  ciclo
                $color_3 = getColorByIndicador('D1');   //  calibre
                $color_4 = getColorByIndicador('D3');   //  precio_x_ramo
                $color_5 = getColorByIndicador('D12');   //  tallos_m2
                $color_6 = getColorByIndicador('D8');   //  ramos_m2_anno
                $color_7 = getColorByIndicador('D14');   //  precio_x_tallo
                $color_8 = getColorByIndicador('C3');   //  costos_campo_semana
                $color_9 = getColorByIndicador('C4');   //  costos_cosecha_x_tallo
                $color_10 = getColorByIndicador('C5');   //  costos_postcosecha_x_tallo
                $color_11 = getColorByIndicador('C6');   //  costos_total_x_tallo
            @endphp

            <div id="box_cuadros" class="box box-primary hide">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mouse-hand sombra_pequeña bg-gray" onclick="location.href='{{url('ventas_m2')}}'"
                                 onmouseover="$(this).removeClass('bg-gray')" onmouseleave="$(this).addClass('bg-gray')">
                                <span class="info-box-icon"><i class="fa fa-fw fa-diamond"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text text-center" style="font-size: 1.2em">Ventas/
                                        <small>m<sup>2</sup></small>
                                        /año
                                    </strong>
                                    <span class="info-box-number text-center" style="color: {{$color_1}}">
                                            {{number_format($venta_m2_anno_mensual, 2)}}
                                        <small>$/m<sup>2</sup>/año (4 meses)</small>
                                    </span>
                                    <span class="info-box-number text-center" style="color: {{$color_1}};">
                                            {{number_format($venta_m2_anno_anual, 2)}}
                                        <small>$/m<sup>2</sup>/año (1 año)</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box sombra_pequeña" onmouseover="$(this).addClass('bg-gray-light')"
                                 onmouseleave="$(this).removeClass('bg-gray-light')">
                                <span class="info-box-icon"><i class="fa fa-fw fa-tree"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Cosecha</strong>
                                    <span class="info-box-number">{{number_format($tallos_cosechados, 2)}}
                                        <small>tallos</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box sombra_pequeña" onmouseover="$(this).addClass('bg-gray-light')"
                                 onmouseleave="$(this).removeClass('bg-gray-light')">
                                <span class="info-box-icon"><i class="fa fa-fw fa-map"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Área</strong>
                                    <span class="info-box-number">
                                        {{number_format(round($area_produccion / 10000, 2), 2)}}
                                        <small> <sup>ha</sup></small>
                                    </span>
                                    <span class="info-box-number" style="color: {{$color_2}};">
                                        {{number_format($ciclo, 2)}}
                                        <small> ciclo</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box mouse-hand sombra_pequeña" onmouseover="$(this).addClass('bg-gray-light')"
                                 onmouseleave="$(this).removeClass('bg-gray-light')" onclick="location.href='{{url('crm_postcosecha')}}'">
                                <span class="info-box-icon"><i class="fa fa-fw fa-leaf"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Postcosecha</strong>
                                    <span class="info-box-number" style="color: {{$color_3}}">
                                        {{$calibre}}
                                        <small>t/r calibre</small></span>
                                    <strong class="info-box-number" title="Tallos">{{number_format($tallos)}}
                                        <small>tallos</small>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box mouse-hand sombra_pequeña" onmouseover="$(this).addClass('bg-gray-light')"
                                 onmouseleave="$(this).removeClass('bg-gray-light')" onclick="location.href='{{url('crm_ventas')}}'">
                                <span class="info-box-icon"><i class="fa fa-fw fa-usd"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Ventas</strong>
                                    <span class="info-box-number" style="color: {{$color_4}};">
                                        {{number_format($precio_x_ramo, 2)}}
                                        <small>precio</small></span>
                                    <span class="info-box-number" title="Valor">
                                        <small>$</small>
                                        {{number_format($valor, 2)}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box mouse-hand sombra_pequeña" onmouseover="$(this).addClass('bg-gray-light')"
                                 onmouseleave="$(this).removeClass('bg-gray-light')" onclick="location.href='{{url('crm_rendimiento')}}'">
                                <span class="info-box-icon"><i class="ion ion-ios-people-outline"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Rend/Desecho</strong>
                                    <span class="info-box-number">
                                        {{number_format($rendimiento , 2)}}
                                        <small>r/hr rend</small>
                                    </span>
                                    <span class="info-box-number" title="Desecho">
                                        {{number_format($desecho , 2)}}
                                        <small>% desecho</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box mouse-hand sombra_pequeña" onmouseover="$(this).addClass('bg-gray-light')"
                                 onmouseleave="$(this).removeClass('bg-gray-light')" onclick="location.href='{{url('crm_area')}}'">
                                <span class="info-box-icon"><i class="fa fa-fw fa-cube"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Producción</strong>
                                    <span class="info-box-number" style="color: {{$color_5}};">
                                        {{number_format($tallos_m2, 2)}}
                                        <small> t/m<sup>2</sup></small>
                                    </span>
                                    <span class="info-box-number" title="Ramos/m2" style="color: {{$color_6}}">
                                        {{number_format($ramos_m2_anno, 2)}}
                                        <small>r/m<sup>2</sup>/año</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="box_arbol" class="box box-success hide">
                <div class="box-header with-border">
                    <select name="filtro_variedad" id="filtro_variedad" class="pull-left" onchange="select_filtro_variedad()">
                        <option value="" id="option_acumulado_var">Acumulado</option>
                        @foreach(getVariedades() as $var)
                            <option value="{{$var->id_variedad}}">{{$var->siglas}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="box-body" style="overflow-x: scroll;" id="div_box_body">
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
                                    '<br><button type="button" class="btn btn-xs btn-block btn-default" onclick="mostrar_indicadores_claves(0)" style="color: black">Ventas/m<sup>2</sup>/año</button>'
                                }, 'Rentabilidad', 'Ventas/m2/año'],
                                [{'v': 'Costos', 'f': '<strong>Costos/m<sup>2</sup></strong>'}, 'Rentabilidad', 'Costos'],
                                [{
                                    'v': 'C1', 'f': '<strong></strong>' +
                                    '<br><strong title="Campo/ha/Semana" style="color:{{$color_8}}"><small>Campo/<sup>ha</sup>/Semana: </small><span id="span_costos_campo_semana">${{number_format(explode('|', $costos_campo_semana)[0] , 2)}}</span></strong>' +
                                    '<br><strong title="Cosecha x Tallo" style="color:{{$color_9}}"><small>Cosecha x Tallo: </small><span id="span_costos_cosecha_tallo">${{number_format($costos_cosecha_x_tallo , 4)}}</span></strong>' +
                                    '<br><strong title="Postcosecha x Tallo" style="color:{{$color_10}}"><small>Postcosecha x Tallo: </small><span id="span_costos_postcosecha_tallo">${{number_format($costos_postcosecha_x_tallo , 4)}}</span></strong>' +
                                    '<br><strong title="Total x Tallo" style="color:{{$color_11}}"><small>Total x Tallo: </small><span id="span_costos_total_tallo">${{number_format($costos_total_x_tallo , 4)}}</span></strong>' +
                                    '<br><button type="button" class="btn btn-xs btn-block btn-default" style="color: black">Indicadores claves</button>'
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
                                    '<br><button type="button" class="btn btn-xs btn-block btn-default" onclick="mostrar_indicadores_claves(1)" style="color: black">Indicadores claves</button>'
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

                    <div id="div_indicadores_claves"></div>

                </div>
            </div>


            <script>
                function render_gauge(canvas, value, rangos, indices = false, time = 250) {
                    var staticLabels = false;
                    if (indices) {
                        staticLabels = {
                            font: "10px sans-serif",  // Specifies font
                            labels: [rangos[0]['desde'], rangos[1]['desde'], rangos[2]['desde'], rangos[2]['hasta']],  // Print labels at these values
                            color: "#000000",  // Optional: Label text color
                            fractionDigits: 0  // Optional: Numerical precision. 0=round off.
                        };
                    }

                    var opts = {
                        angle: 0, // The span of the gauge arc
                        lineWidth: 0.2, // The line thickness
                        radiusScale: 1, // Relative radius
                        pointer: {
                            length: 0.46, // // Relative to gauge radius
                            strokeWidth: 0.033, // The thickness
                            color: '#000000', // Fill color
                        },
                        limitMax: false,     // If false, max value increases automatically if value > maxValue
                        limitMin: true,     // If true, the min value of the gauge will be fixed
                        colorStart: '#6F6EA0',   // Colors
                        colorStop: '#C0C0DB',    // just experiment with them
                        strokeColor: '#EEEEEE',  // to see which ones work best for you
                        generateGradient: true,
                        highDpiSupport: true,     // High resolution support
                        // renderTicks is Optional
                        renderTicks: {
                            divisions: 4,
                            divWidth: 1,
                            divLength: 0.79,
                            divColor: '#333333',
                            subDivisions: 5,
                            subLength: 0.45,
                            subWidth: 0.4,
                            subColor: '#666666'
                        },
                        staticZones: [
                            {strokeStyle: rangos[0]['color'], min: rangos[0]['desde'], max: rangos[0]['hasta'], height: 0.6}, // Red from 0 to 15
                            {strokeStyle: rangos[1]['color'], min: rangos[1]['desde'], max: rangos[1]['hasta'], height: 1}, // Orange
                            {strokeStyle: rangos[2]['color'], min: rangos[2]['desde'], max: rangos[2]['hasta'], height: 1.2}  // Green
                        ],
                        staticLabels: staticLabels,
                    };

                    var target = document.getElementById(canvas); // your canvas element
                    var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
                    gauge.maxValue = rangos[2]['hasta']; // set max gauge value
                    gauge.setMinValue(rangos[0]['desde']);  // Prefer setter over gauge.minValue = 0
                    gauge.animationSpeed = time; // set animation speed (32 is default value)
                    gauge.set(value); // set actual value
                }

                function mostrar_indicadores_claves(view, variedad = '') {
                    var views = ['indicadores_ventas_m2', 'indicadores_claves'];
                    datos = {
                        view: views[view],
                        variedad: variedad
                    };
                    get_jquery('{{url('mostrar_indicadores_claves')}}', datos, function (retorno) {
                        $('#div_indicadores_claves').html(retorno);
                        location.href = '#div_indicadores_claves';
                    });
                }

                function count(id) {
                    var $el = $("#" + id),
                        value = $el.html();

                    $({percentage: 0}).stop(true).animate({percentage: value}, {
                        duration: 4000,
                        easing: "easeOutExpo",
                        step: function () {
                            // percentage with 1 decimal;
                            var percentageVal = Math.round(this.percentage * 10) / 10;

                            $el.text(percentageVal);
                        }
                    }).promise().done(function () {
                        // hard set the value after animation is done to be
                        // sure the value is correct
                        $el.text(value);
                    });
                }
            </script>

        @endif
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    <script>
        notificar('Bienvenid@ {{explode(' ',getUsuario(Session::get('id_usuario'))->nombre_completo)[0]}}',
            '{{url('')}}', function () {
            }, null, false);

        $(window).ready(function () {
            if ($(document).width() >= 833) { // mostrar arbol
                $('#box_arbol').removeClass('hide');
                $('#box_cuadros').addClass('hide');
            } else {    // ocultar arbol
                $('#box_arbol').addClass('hide');
                $('#box_cuadros').removeClass('hide');
            }
        });

        $(window).resize(function () {
            if ($(document).width() >= 833) { // mostrar arbol
                $('#box_arbol').removeClass('hide');
                $('#box_cuadros').addClass('hide');
            } else {    // ocultar arbol
                $('#box_arbol').addClass('hide');
                $('#box_cuadros').removeClass('hide');
            }
        });

        function select_filtro_variedad() {
            datos = {
                variedad: $('#filtro_variedad').val()
            };
            get_jquery('{{url('select_filtro_variedad')}}', datos, function (retorno) {
                $('#div_box_body').html(retorno);
                $('#option_acumulado_var').hide();
            })
        }
    </script>
@endsection