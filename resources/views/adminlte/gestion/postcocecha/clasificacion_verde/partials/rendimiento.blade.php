@if($clasificacion_verde->personal > 0 && $clasificacion_verde->getCantidadHorasTrabajo() > 0)
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                Rendimiento de
                <span class="badge">{{$clasificacion_verde->personal}}</span>
                personas en
                <span class="badge">{{$clasificacion_verde->getCantidadHorasTrabajo()}}</span>
                horas de trabajo:
                <span class="badge">{{$clasificacion_verde->getRendimiento()}}</span>
                tallos por persona/hora
            </h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <input type="checkbox" onchange="ocultar_mostrar_detalles_rendimiento_x_horas()"
                           id="check_mostrar_ocultar_detalles_rendimiento_x_horas" title="Mostrar/ocultar detalles">
                    <label for="check_mostrar_ocultar_detalles_rendimiento_x_horas" class="mouse-hand" style="margin-left: 5px"
                           id="label_rendimiento_x_horas">
                        Rendimiento por horas
                    </label>
                </div>
               {{-- <div class="col-md-6 text-right">
                    <input type="checkbox" onchange="ocultar_mostrar_detalles_rendimiento()" checked
                           id="check_mostrar_ocultar_detalles_rendimiento" title="Mostrar/ocultar detalles">
                    <label for="check_mostrar_ocultar_detalles_rendimiento" class="mouse-hand" style="margin-left: 5px"
                           id="label_rendimiento_x_ingresos">
                        Rendimiento por ingresos
                    </label>
                </div>--}}
            </div>

            <div style="overflow-x: scroll">
                {{--<table id="table_rendimiento_x_ingresos" class="table-responsive table-striped table-bordered" width="100%"
                       style="border: 2px solid #9d9d9d; display: none;">
                    <tr style="background-color: #e9ecef">
                        <th class="text-center" style="border-color: #9d9d9d">
                            Hora
                        </th>
                        <th class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                            Variedad
                        </th>
                        <th class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                            Calibre
                        </th>
                        <th class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                            Cantidad
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            Total
                        </th>
                        <th class="text-center elemento_especifico" title="Rendimiento personal por calibre"
                            style="border-color: #9d9d9d; background-color: #357CA5; color: white; display: none;">
                            x Calibre
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;"
                            title="Rendimiento personal global">
                            Rendimiento
                        </th>
                    </tr>
                    @php
                        $rendimiento_prev = '';
                    @endphp
                    @foreach($listado as $item)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{substr($item->fecha, 11,5)}}
                            </td>
                            <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                        <li style="border-bottom: 1px solid #9d9d9d">
                                            {{$li->variedad->siglas}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                        <li style="border-bottom: 1px solid #9d9d9d">
                                            {{explode('|',$li->clasificacion_unitaria->nombre)[0]}}{{$li->clasificacion_unitaria->unidad_medida->siglas}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                        <li style="border-bottom: 1px solid #9d9d9d">
                                            {{$li->cantidad_ramos * $li->tallos_x_ramos}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$item->cantidad}}
                            </td>
                            <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                        <li style="border-bottom: 1px solid #9d9d9d">
                                            {{round(($li->cantidad_ramos * $li->tallos_x_ramos) / $clasificacion_verde->personal,2)}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{round($item->cantidad / $clasificacion_verde->personal, 2)}}
                                @if($rendimiento_prev != '')
                                    @if($rendimiento_prev < round($item->cantidad / $clasificacion_verde->personal, 2))
                                        <i class="fa fa-fw fa-arrow-up pull-right" style="color: green"></i>
                                    @elseif($rendimiento_prev > round($item->cantidad / $clasificacion_verde->personal, 2))
                                        <i class="fa fa-fw fa-arrow-down pull-right" style="color: red"></i>
                                    @else
                                        <i class="fa fa-fw fa-arrows-h pull-right" style="color: #ADD8E6"></i>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @php
                            $rendimiento_prev = round($item->cantidad / $clasificacion_verde->personal, 2);
                        @endphp
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            Total
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" id="th_total_tallos">
                            {{$clasificacion_verde->total_tallos_rendimiento()}}
                        </th>
                    </tr>
                </table>--}}

                <table id="table_rendimiento_x_horas" class="table-responsive table-striped table-bordered" width="100%"
                       style="border: 2px solid #9d9d9d; display: none;">
                    <tr style="background-color: #e9ecef">
                        <th class="text-center" style="border-color: #9d9d9d">
                            Hora
                        </th>
                        <th class="text-center elemento_especifico_horas" style="border-color: #9d9d9d">
                            Variedad
                        </th>
                        <th class="text-center elemento_especifico_horas" style="border-color: #9d9d9d">
                            Calibre
                        </th>
                        <th class="text-center elemento_especifico_horas" style="border-color: #9d9d9d">
                            Cantidad
                        </th>
                        <th class="text-center elemento_especifico_horas" style="border-color: #9d9d9d">
                            Total x Variedad
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            Total
                        </th>
                        <th class="text-center elemento_especifico_horas"
                            style="border-color: #9d9d9d; background-color: #357CA5; color: white" title="Rendimiento personal por calibre">
                            x Calibre
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;"
                            title="Rendimiento personal global">
                            Rendimiento
                        </th>
                    </tr>

                    @php
                        $rendimiento_prev = '';
                    @endphp
                    @foreach($clasificacion_verde->getIntervalosHoras() as $item)
                        @php
                            $total_x_intervalo = 0;
                        @endphp
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <span class="badge">{{$item['hora_inicio']}}</span>
                                <span class="badge">{{$item['hora_fin']}}</span>
                            </td>
                            <td class="text-center elemento_especifico_horas" style="border-color: #9d9d9d; display: none">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByIntervaloFecha($item['fecha_inicio_full'],$item['fecha_fin_full']) as $variedad)
                                        <li style="border: 1px solid #9d9d9d">{{getVariedad($variedad->id_variedad)->siglas}}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center elemento_especifico_horas" style="border-color: #9d9d9d; display: none">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByIntervaloFecha($item['fecha_inicio_full'],$item['fecha_fin_full']) as $unitaria)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{explode('|',getUnitaria($unitaria->id_clasificacion_unitaria)->nombre)[0]}}{{getUnitaria($unitaria->id_clasificacion_unitaria)->unidad_medida->siglas}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center elemento_especifico_horas" style="border-color: #9d9d9d; display: none">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByIntervaloFecha($item['fecha_inicio_full'],$item['fecha_fin_full']) as $cantidad)
                                        <li style="border: 1px solid #9d9d9d">{{$cantidad->cantidad}}</li>
                                        @php
                                            $total_x_intervalo+= $cantidad->cantidad;
                                        @endphp
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center elemento_especifico_horas" style="border-color: #9d9d9d; display: none">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getVariedadesByIntervaloFecha($item['fecha_inicio_full'],$item['fecha_fin_full']) as $variedad)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{getVariedad($variedad->id_variedad)->siglas}}
                                            <strong>
                                                {{$clasificacion_verde->getTotalTallosByVariedadIntervaloFecha($variedad->id_variedad, $item['fecha_inicio_full'],$item['fecha_fin_full'])}}
                                            </strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$total_x_intervalo}}
                            </td>
                            <td class="text-center elemento_especifico_horas" style="border-color: #9d9d9d; display: none">
                                <ul class="list-unstyled">
                                    @foreach($clasificacion_verde->getDetallesByIntervaloFecha($item['fecha_inicio_full'],$item['fecha_fin_full']) as $cantidad)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{round($cantidad->cantidad / $clasificacion_verde->personal, 2)}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{round($total_x_intervalo / $clasificacion_verde->personal, 2)}}
                                @if($rendimiento_prev != '')
                                    @if($rendimiento_prev < round($total_x_intervalo / $clasificacion_verde->personal, 2))
                                        <i class="fa fa-fw fa-arrow-up pull-right" style="color: green"></i>
                                    @elseif($rendimiento_prev > round($total_x_intervalo / $clasificacion_verde->personal, 2))
                                        <i class="fa fa-fw fa-arrow-down pull-right" style="color: red"></i>
                                    @else
                                        <i class="fa fa-fw fa-arrows-h pull-right" style="color: #ADD8E6"></i>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @php
                            $rendimiento_prev = round($total_x_intervalo / $clasificacion_verde->personal, 2);
                        @endphp
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            Total
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" id="th_total_tallos_x_horas">
                            {{$clasificacion_verde->total_tallos_rendimiento()}}
                        </th>
                    </tr>
                </table>
            </div>

            <canvas id="chart_rendimiento_horas" width="100%" height="40" style="display: none"></canvas>

            {{--<canvas id="chart_rendimiento_ingresos" width="100%" height="40" style="display: none"></canvas>--}}
        </div>
    </div>

    <input type="hidden" id="rendimiento_activo" value="horas">

    <script>
        ocultar_mostrar_detalles_rendimiento_x_horas();
        construir_char_horas();
        //construir_char_ingresos();

        function ocultar_mostrar_detalles_rendimiento() {
            $('#rendimiento_activo').val('ingresos');
            $('#table_rendimiento_x_ingresos').show();
            $('#label_rendimiento_x_ingresos').addClass('badge');
            $('#label_rendimiento_x_horas').removeClass('badge');
            $('#table_rendimiento_x_horas').hide();
            $('#chart_rendimiento_ingresos').show();
            $('#chart_rendimiento_horas').hide();
            $('#check_mostrar_ocultar_detalles_rendimiento_x_horas').prop('checked', false);
            if ($('#check_mostrar_ocultar_detalles_rendimiento').prop('checked')) {
                $('.elemento_especifico').show();
                $('#th_total_tallos').prop('colspan', 4);
            } else {
                $('.elemento_especifico').hide();
                $('#th_total_tallos').prop('colspan', 1);
            }
        }

        function ocultar_mostrar_detalles_rendimiento_x_horas() {
            $('#rendimiento_activo').val('horas');
            $('#table_rendimiento_x_horas').show();
            $('#label_rendimiento_x_horas').addClass('badge');
            $('#label_rendimiento_x_ingresos').removeClass('badge');
            $('#table_rendimiento_x_ingresos').hide();
            $('#chart_rendimiento_ingresos').hide();
            $('#chart_rendimiento_horas').show();
            $('#check_mostrar_ocultar_detalles_rendimiento').prop('checked', false);
            if ($('#check_mostrar_ocultar_detalles_rendimiento_x_horas').prop('checked')) {
                $('#table_rendimiento_x_horas').show();
                $('#th_total_tallos_x_horas').prop('colspan', 5);
                $('.elemento_especifico_horas').show();
            } else {
                $('.elemento_especifico_horas').hide();
                $('#th_total_tallos_x_horas').prop('colspan', 1);
            }
        }

        function construir_char_horas() {
            labels = [];
            datasets = [];
            data_rendimiento = [];
            data_tallos = [];
            @foreach($clasificacion_verde->getIntervalosHoras() as $item)
            @php
                $total_x_intervalo = 0;
            @endphp
            @foreach($clasificacion_verde->getDetallesByIntervaloFecha($item['fecha_inicio_full'],$item['fecha_fin_full']) as $cantidad)
            @php
                $total_x_intervalo+= $cantidad->cantidad;
            @endphp
            @endforeach
            labels.push("{{$item['hora_inicio'].' - '.$item['hora_fin']}}");
            data_rendimiento.push("{{round($total_x_intervalo / $clasificacion_verde->personal, 2)}}");
            @endforeach

            /*if ($('#option_barra_chart').prop('checked'))
                tipo = 'bar';
            else
                tipo = 'line';*/

            datasets = [{
                label: 'Rendimiento ',
                data: data_rendimiento,
                //backgroundColor: '#8c99ff54',
                borderColor: 'blue',
                borderWidth: 2,
                fill: false,
            }];

            ctx = document.getElementById("chart_rendimiento_horas").getContext('2d');
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    elements: {
                        line: {
                            tension: 0, // disables bezier curves
                        }
                    },
                    tooltips: {
                        mode: 'point' // nearest, point, index, dataset, x, y
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        fullWidth: false,
                        onClick: function () {
                        },
                        onHover: function () {
                        },
                        reverse: true,
                    },
                    showLines: true, // for all datasets
                    borderCapStyle: 'round',    // "butt" || "round" || "square"
                }
            });
        }

        function construir_char_ingresos() {
            labels = [];
            datasets = [];
            data_rendimiento = [];
            data_tallos = [];
            @foreach($listado as $item)
            labels.push("{{substr($item->fecha, 11,5)}}");
            data_rendimiento.push("{{round($item->cantidad / $clasificacion_verde->personal, 2)}}");
            @endforeach

            /*if ($('#option_barra_chart').prop('checked'))
                tipo = 'bar';
            else
                tipo = 'line';*/

            datasets = [{
                label: 'Rendimiento ',
                data: data_rendimiento,
                //backgroundColor: '#8c99ff54',
                borderColor: 'blue',
                borderWidth: 2,
                fill: false,
            }];
            if ($('#option_tallos_chart').prop('checked'))
                datasets.push({
                    label: 'Tallos ',
                    data: data_tallos,
                    backgroundColor: '#b9f1b973',
                    borderColor: 'green',
                    borderWidth: 2,
                });

            ctx = document.getElementById("chart_rendimiento_ingresos").getContext('2d');
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    elements: {
                        line: {
                            tension: 0, // disables bezier curves
                        }
                    },
                    tooltips: {
                        mode: 'point' // nearest, point, index, dataset, x, y
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        fullWidth: false,
                        onClick: function () {
                        },
                        onHover: function () {
                        },
                        reverse: true,
                    },
                    showLines: true, // for all datasets
                    borderCapStyle: 'round',    // "butt" || "round" || "square"
                }
            });
        }

        function construir_char() {
            if ($('#rendimiento_activo').val() == 'horas') {
                construir_char_horas();
            } else {
                construir_char_ingresos();
            }
        }
    </script>

@else
    <div class="alert alert-info text-center">
        <h3>La cantidad de trabajadores no puede ser 0.</h3>
    </div>
@endif