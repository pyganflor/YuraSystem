@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard
@endsection

@section('script_inicio')

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

            @if(!isPC())
                <div class="box box-primary">
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
            @else
                {{-- CHART 1 --}}
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

                <style>
                    .nodo_org {
                        background-color: #e9ecef !important;
                        width: 200px;
                        cursor: pointer;
                        -webkit-box-shadow: 9px 8px 11px -2px rgba(0, 0, 0, 0.34);
                        -moz-box-shadow: 9px 8px 11px -2px rgba(0, 0, 0, 0.34);
                        box-shadow: 5px 3px 11px -2px rgba(0, 0, 0, 0.34);
                    }

                    .nodo_org_selected {
                        background-color: #ccc9c9 !important;
                    }
                </style>

                <div class="box box-success">
                    <div class="box-body">
                        <div id="chart_org"></div>
                    </div>
                </div>

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
                            [{'v': 'Rentabilidad', 'f': '<strong>Rentabilidad</strong>'}, '', 'Rentabilidad'],
                            [{
                                'v': 'Ventas_m2_anno',
                                'f': '<strong style="color:{{$color_1}}">{{number_format($venta_m2_anno_mensual, 2)}}<small><sup>(4 meses)</sup></small></strong>' +
                                '<br><strong style="color:{{$color_1}}">{{number_format($venta_m2_anno_anual, 2)}}<small><sup>(1 año)</sup></small></strong>' +
                                '<br><button type="button" title="Ver dashboard" class="btn btn-xs btn-block btn-default" onclick="cargar_ventas_m2()">$/m<sup>2</sup>/año <i class="fa fa-fw fa-long-arrow-right pull-right"></i></button>'
                            }, 'Rentabilidad', 'Ventas/m2/año'],
                            [{'v': 'Costos', 'f': '<strong>Costos</strong>'}, 'Rentabilidad', 'Costos'],
                            [{
                                'v': 'Postcosecha',
                                'f': '<strong style="color: {{$color_3}}">{{$calibre}}<small><sup>t/r calibre</sup></small></strong>' +
                                '<br><strong>{{number_format($tallos)}}<small><sup>tallos</sup></small></strong>' +
                                '<br><button type="button" title="Ver dashboard" class="btn btn-xs btn-block btn-default" onclick="cargar_crm_postcosecha()">Postcosecha <i class="fa fa-fw fa-long-arrow-right pull-right"></i></button>'
                            }, 'Ventas_m2_anno', 'Postcosecha'],
                            [{
                                'v': 'Ventas',
                                'f': '<strong style="color: {{$color_4}}">{{number_format($precio_x_ramo, 2)}}<small><sup>precio</sup></small></strong>' +
                                '<br><strong>${{number_format($valor, 2)}}</strong>' +
                                '<br><button type="button" title="Ver dashboard" class="btn btn-xs btn-block btn-default" onclick="cargar_crm_ventas()">Ventas <i class="fa fa-fw fa-long-arrow-right pull-right"></i></button>'
                            }, 'Ventas_m2_anno', 'Ventas'],
                            [{
                                'v': 'Produccion',
                                'f': '<strong style="color: {{$color_5}}">{{number_format($tallos_m2, 2)}}<small><sup>t/m<sup>2</sup></sup></small></strong>' +
                                '<br><strong style="color:{{$color_6}}">{{number_format($ramos_m2_anno, 2)}}<small><sup>r/m<sup>2</sup>/año</sup></small></strong>' +
                                '<br><button type="button" title="Ver dashboard" class="btn btn-xs btn-block btn-default" onclick="cargar_crm_area()">Producción <i class="fa fa-fw fa-long-arrow-right pull-right"></i></button>'
                            }, 'Ventas_m2_anno', 'Producción'],
                            [{
                                'v': 'Cosecha',
                                'f': '<strong>Cosecha</strong><br><strong>{{number_format($tallos_cosechados)}} <small>tallos</small></strong>'
                            }, 'Rentabilidad', 'Cosecha'],
                            [{
                                'v': 'Area',
                                'f': '<strong>Área</strong><br><strong>{{number_format(round($area_produccion / 10000, 2), 2)}} <small><sup>ha</sup></small></strong>' +
                                '<br><strong style="color: {{$color_2}}">{{number_format($ciclo, 2)}} <small>ciclo</small></strong>'
                            }, 'Rentabilidad', 'Área'],

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
                </script>
            @endif
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
    </script>
@endsection
