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
                if($venta_m2_anno_mensual < 28)
                    $color_1  = 'red';
                else if($venta_m2_anno_mensual >= 28 && $venta_m2_anno_mensual <= 32)
                    $color_1 = 'orange';
                else
                    $color_1 = 'green';

                if($venta_m2_anno_anual < 28)
                    $color_1_1  = 'red';
                else if($venta_m2_anno_anual >= 28 && $venta_m2_anno_anual <= 32)
                    $color_1_1 = 'orange';
                else
                    $color_1_1 = 'green';

                if($ciclo < 115)
                    $color_2  = 'green';
                else if($ciclo >= 115 && $ciclo <= 125)
                    $color_2 = 'orange';
                else
                    $color_2 = 'red';

                if($calibre < 7.4)
                    $color_3  = 'green';
                else if($calibre >= 7.4 && $calibre <= 7.8)
                    $color_3 = 'orange';
                else
                    $color_3 = 'red';

                if($precio_x_ramo < 2)
                    $color_4  = 'red';
                else if($precio_x_ramo >= 2 && $precio_x_ramo <= 2.1)
                    $color_4 = 'orange';
                else
                    $color_4 = 'green';

                if($tallos_m2 < 35)
                    $color_5  = 'red';
                else if($tallos_m2 >= 35 && $tallos_m2 <= 45)
                    $color_5 = 'orange';
                else
                    $color_5 = 'green';

                if($ramos_m2_anno < 13)
                    $color_6  = 'red';
                else if($ramos_m2_anno >= 13 && $ramos_m2_anno <= 17)
                    $color_6 = 'orange';
                else
                    $color_6 = 'green';
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
                <div class="box-body" style="overflow-x: scroll;">
                    <div id="chart_org"></div>
                </div>
            </div>
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

        google.charts.load('current', {packages: ["orgchart"]});
        google.charts.setOnLoadCallback(drawChartOrg);

        function drawChartOrg() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');

            // For each orgchart box, provide the name, manager, and tooltip to show.
            data.addRows([
                [{'v': 'Rentabilidad', 'f': '<strong>Rentabilidad</strong>'}, '', 'Rentabilidad'],
                [{
                    'v': 'Ventas_m2_anno',
                    'f': '<strong style="color:{{$color_1}}"><span id="span_venta_m2_mensual">{{number_format($venta_m2_anno_mensual, 2)}}</span><small><sup>(4 meses)</sup></small></strong>' +
                    '<canvas id="canvas_ventas_m2_anno_mensual" style="width: 50px"></canvas>' +
                    '<br><strong style="color:{{$color_1_1}}"><span id="span_venta_m2_anno">{{number_format($venta_m2_anno_anual, 2)}}</span><small><sup>(1 año)</sup></small></strong>' +
                    '<canvas id="canvas_ventas_m2_anno" style="width: 50px"></canvas>'
                }, 'Rentabilidad', 'Ventas/m2/año'],
                [{'v': 'Costos', 'f': '<strong>Costos</strong>'}, 'Rentabilidad', 'Costos'],
                [{
                    'v': 'Indicadores_claves',
                    'f': '<strong style="color: {{$color_4}}"><span id="span_precio_x_ramo">{{number_format($precio_x_ramo, 2)}}</span><small><sup>precio</sup></small></strong>' +
                    '<canvas id="canvas_precio_x_ramo" style="width: 50px"></canvas>' +
                    '<br><strong style="color:{{$color_6}}"><span id="span_ramos_m2_anno">{{number_format($ramos_m2_anno, 2)}}</span><small><sup>r/m<sup>2</sup>/año</sup></small></strong>' +
                    '<canvas id="canvas_ramos_m2_anno" style="width: 50px"></canvas>' +
                    '<br><strong style="color: {{$color_3}}"><span id="span_calibre">{{$calibre}}</span><small><sup>t/r calibre</sup></small></strong>' +
                    '<canvas id="canvas_calibre" style="width: 50px"></canvas>' +
                    '<br><strong style="color: {{$color_5}}"><span id="span_tallos_m2">{{number_format($tallos_m2, 2)}}</span><small><sup>t/m<sup>2</sup></sup></small></strong>' +
                    '<canvas id="canvas_tallos_m2" style="width: 50px"></canvas>' +
                    '<br><strong style="color: {{$color_2}}"><span id="span_ciclo">{{number_format($ciclo, 2)}}</span><small><sup>ciclo</sup></small></strong>' +
                    '<canvas id="canvas_ciclo" style="width: 50px"></canvas>' +
                    '<br><button type="button" class="btn btn-xs btn-block btn-default" onclick="mostrar_indicadores_claves()" disabled style="color: black">Ind. claves</button>'
                }, 'Ventas_m2_anno', 'Indicadores claves'],
                [{
                    'v': 'Datos_importantes',
                    'f': '<strong style="text-decoration: underline">Datos importantes</strong>' +
                    '<br><strong><span id="span_area_produccion">{{number_format(round($area_produccion / 10000, 2), 2)}}</span><small><sup>ha</sup></small></strong>' +
                    '<br><strong>$<span id="span_valor">{{number_format($valor, 2)}}</span></strong>' +
                    '<br><strong title="Tallos cosechados"><span id="span_tallos_cosechados">{{$tallos_cosechados}}</span><small><sup>t/cosechados</sup></small></strong>' +
                    '<br><strong title="Tallos clasificados"><span id="span_tallos">{{number_format($tallos)}}</span><small><sup>t/clasificados</sup></small></strong>'
                }, 'Ventas_m2_anno', 'Datos importantes'],
                [{
                    'v': 'Dashboards',
                    'f': '<strong style="text-decoration: underline">Dashboards</strong>' +
                    '<button type="button" title="Ver dashboard" class="btn btn-block btn-default" onclick="cargar_ventas_m2()">Ventas/m<sup>2</sup>/año</button>' +
                    '<button type="button" title="Ver dashboard" class="btn btn-block btn-default" onclick="cargar_crm_postcosecha()">Postcosecha</button>' +
                    '<button type="button" title="Ver dashboard" class="btn btn-block btn-default" onclick="cargar_crm_ventas()">Venta</button>' +
                    '<button type="button" title="Ver dashboard" class="btn btn-block btn-default" onclick="cargar_crm_area()">Área</button>'
                }, 'Ventas_m2_anno', 'Dashboards'],
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

            render_gauge('canvas_ventas_m2_anno_mensual', '{{number_format($venta_m2_anno_mensual, 2)}}', [{
                desde: 0,
                hasta: 28,
                color: '#f03e3e'
            }, {
                desde: 28,
                hasta: 32,
                color: '#fd0'
            }, {
                desde: 32,
                hasta: 50,
                color: '#30b32d'
            }]);
            render_gauge('canvas_ventas_m2_anno', '{{number_format($venta_m2_anno_anual, 2)}}', [{
                desde: 0,
                hasta: 28,
                color: '#f03e3e'
            }, {
                desde: 28,
                hasta: 32,
                color: '#fd0'
            }, {
                desde: 32,
                hasta: 50,
                color: '#30b32d'
            }]);
            render_gauge('canvas_precio_x_ramo', '{{number_format($precio_x_ramo, 2)}}', [{
                desde: 1,
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
            render_gauge('canvas_ramos_m2_anno', '{{number_format($ramos_m2_anno, 2)}}', [{
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
            render_gauge('canvas_calibre', '{{number_format($calibre, 2)}}', [{
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
            render_gauge('canvas_tallos_m2', '{{number_format($tallos_m2, 2)}}', [{
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
            render_gauge('canvas_ciclo', '{{number_format($ciclo, 2)}}', [{
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

            count('span_venta_m2_anno');
            count('span_venta_m2_mensual');
            count('span_precio_x_ramo');
            count('span_ramos_m2_anno');
            count('span_calibre');
            count('span_tallos_m2');
            count('span_ciclo');
            count('span_area_produccion');
            count('span_valor');
            count('span_tallos_cosechados');
            count('span_tallos');
        }

        function render_gauge(canvas, value, rangos) {
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
                /*staticLabels: {
                    font: "10px sans-serif",  // Specifies font
                    labels: [0, 28, 32, 50],  // Print labels at these values
                    color: "#000000",  // Optional: Label text color
                    fractionDigits: 0  // Optional: Numerical precision. 0=round off.
                },*/
            };
            var target = document.getElementById(canvas); // your canvas element
            var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
            gauge.maxValue = rangos[2]['hasta']; // set max gauge value
            gauge.setMinValue(rangos[0]['desde']);  // Prefer setter over gauge.minValue = 0
            gauge.animationSpeed = 250; // set animation speed (32 is default value)
            gauge.set(value); // set actual value
        }

        function cargar_ventas_m2() {
            location.href = '{{url('ventas_m2')}}';
        }

        function cargar_crm_postcosecha() {
            location.href = '{{url('crm_postcosecha')}}';
        }

        function cargar_crm_ventas() {
            location.href = '{{url('crm_ventas')}}';
        }

        function cargar_crm_area() {
            location.href = '{{url('crm_area')}}';
        }

        function mostrar_indicadores_claves() {
            get_jquery('{{url('mostrar_indicadores_claves')}}', {}, function (retorno) {
                modal_view('modal-view_indicadores_claves', retorno, '<i class="fa fa-fw fa-dashboard"></i> Indicadores claves', true, false, '50%')
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
@endsection