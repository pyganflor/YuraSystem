@if($cosecha != '')
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                Rendimiento de <span class="badge">{{$cosecha->personal}}</span> personas en
                <span class="badge">{{$cosecha->getCantidadHorasTrabajo()}}</span> horas de trabajo:
                <span class="badge">{{$cosecha->getRendimiento()}}</span> tallos por persona/hora
            </h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6 text-center">
                    <input type="checkbox" id="check_rendimiento_horas" name="check_rendimiento_horas" onchange="rendimiento_horas()" checked>
                    <label for="check_rendimiento_horas" class="mouse-hand" style="margin-left: 5px" onclick="rendimiento_horas()"
                           id="label_rendimiento_horas">
                        Rendimiento por horas
                    </label>
                </div>
                <div class="col-md-6 text-center">
                    <input type="checkbox" id="check_rendimiento_ingresos" name="check_rendimiento_ingresos" onchange="rendimiento_ingresos()">
                    <label for="check_rendimiento_ingresos" class="mouse-hand" style="margin-left: 5px" onclick="rendimiento_ingresos()"
                           id="label_rendimiento_ingresos">
                        Rendimiento por ingresos
                    </label>
                </div>
            </div>
            <div style="overflow-x: scroll">
                <table class="table-striped table-bordered table-responsive" width="100%" id="table_rendimiento_horas"
                       style="border: 2px solid #9d9d9d; display: none;">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">
                            Hora
                        </th>
                        <th class="text-center detalle_horas" style="border-color: #9d9d9d">
                            Variedad
                        </th>
                        <th class="text-center detalle_horas" style="border-color: #9d9d9d">
                            Módulo
                        </th>
                        <th class="text-center detalle_horas" style="border-color: #9d9d9d">
                            Cantidad
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            Total
                        </th>
                        <th class="text-center detalle_horas" style="border-color: #9d9d9d; background-color: #357CA5; color: white"
                            title="Rendimiento por variedad">
                            x Variedad
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                            Rendimiento
                        </th>
                    </tr>
                    @php
                        $rendimiento_prev = '';
                    @endphp
                    @foreach($cosecha->getIntervalosHoras() as $intervalo)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <span class="badge">{{$intervalo['hora_inicio']}}</span>
                                <span class="badge">{{$intervalo['hora_fin']}}</span>
                            </td>
                            <td class="text-center detalle_horas" style="border-color: #9d9d9d">
                                <ul class="list-unstyled">
                                    @foreach($cosecha->getDetallesByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $detalle)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{getVariedad($detalle->id_variedad)->siglas}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center detalle_horas" style="border-color: #9d9d9d">
                                <ul class="list-unstyled">
                                    @foreach($cosecha->getDetallesByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $detalle)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{getModuloById($detalle->id_modulo)->nombre}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center detalle_horas" style="border-color: #9d9d9d">
                                <ul class="list-unstyled">
                                    @foreach($cosecha->getDetallesByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $detalle)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{$detalle->cantidad}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$cosecha->getTotalTallosByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full'])}}
                            </td>
                            <td class="text-center detalle_horas" style="border-color: #9d9d9d">
                                <ul class="list-unstyled">
                                    @foreach($cosecha->getDetallesByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $detalle)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{round($detalle->cantidad/$cosecha->personal,2)}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{round($cosecha->getTotalTallosByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full'])/$cosecha->personal,2)}}
                                @if($rendimiento_prev != '')
                                    @if($rendimiento_prev < round($cosecha->getTotalTallosByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) / $cosecha->personal, 2))
                                        <i class="fa fa-fw fa-arrow-up pull-right" style="color: green"></i>
                                    @elseif($rendimiento_prev > round($cosecha->getTotalTallosByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) / $cosecha->personal, 2))
                                        <i class="fa fa-fw fa-arrow-down pull-right" style="color: red"></i>
                                    @else
                                        <i class="fa fa-fw fa-arrows-h pull-right" style="color: #ADD8E6"></i>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @php
                            $rendimiento_prev = round($cosecha->getTotalTallosByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) / $cosecha->personal, 2);
                        @endphp
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            Total
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" id="th_total_horas">
                            {{$cosecha->getTotalTallos()}}
                        </th>
                    </tr>
                </table>

                <table class="table-striped table-bordered table-responsive" width="100%" id="table_rendimiento_ingresos"
                       style="border: 2px solid #9d9d9d; display: none;">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d">
                            Hora
                        </th>
                        <th class="text-center detalle_ingresos" style="border-color: #9d9d9d">
                            Variedad
                        </th>
                        <th class="text-center detalle_ingresos" style="border-color: #9d9d9d">
                            Cantidad
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            Total
                        </th>
                        <th class="text-center detalle_ingresos" style="border-color: #9d9d9d; background-color: #357ca5; color: white"
                            title="Rendimiento por Variedad">
                            x Variedad
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" title="Rendimiento">
                            Rendimiento
                        </th>
                    </tr>
                    @php
                        $rendimiento_prev = '';
                    @endphp
                    @foreach($cosecha->recepciones as $recepcion)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{substr($recepcion->fecha_ingreso, 11, 5)}}
                            </td>
                            <td class="text-center detalle_ingresos" style="border-color: #9d9d9d">
                                <ul class="list-unstyled">
                                    @foreach($recepcion->variedades() as $variedad)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{$variedad->siglas}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center detalle_ingresos" style="border-color: #9d9d9d">
                                <ul class="list-unstyled">
                                    @foreach($recepcion->variedades() as $variedad)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{$recepcion->total_x_variedad($variedad->id_variedad)}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$recepcion->cantidad_tallos()}}
                            </td>
                            <td class="text-center detalle_ingresos" style="border-color: #9d9d9d">
                                <ul class="list-unstyled">
                                    @foreach($recepcion->variedades() as $variedad)
                                        <li style="border: 1px solid #9d9d9d">
                                            {{round($recepcion->total_x_variedad($variedad->id_variedad)/$cosecha->personal,2)}}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{round($recepcion->cantidad_tallos()/$cosecha->personal,2)}}
                                @if($rendimiento_prev != '')
                                    @if($rendimiento_prev < round($recepcion->cantidad_tallos() / $cosecha->personal, 2))
                                        <i class="fa fa-fw fa-arrow-up pull-right" style="color: green"></i>
                                    @elseif($rendimiento_prev > round($recepcion->cantidad_tallos() / $cosecha->personal, 2))
                                        <i class="fa fa-fw fa-arrow-down pull-right" style="color: red"></i>
                                    @else
                                        <i class="fa fa-fw fa-arrows-h pull-right" style="color: #ADD8E6"></i>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @php
                            $rendimiento_prev = round($recepcion->cantidad_tallos() / $cosecha->personal, 2);
                        @endphp
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            Total
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" id="th_total_ingresos">
                            {{$cosecha->getTotalTallos()}}
                        </th>
                    </tr>
                </table>
            </div>
            <canvas id="chart_rendimiento_horas" width="100%" height="40" style="display: none"></canvas>

            <canvas id="chart_rendimiento_ingresos" width="100%" height="40" style="display: none"></canvas>
            <div class="text-center">
                {{--<label>
                    <input type="checkbox" id="option_barra_chart" onchange="construir_char()"> <span class="badge">Barra</span>
                </label>--}}
                <label style="margin-left: 10px">
                    <input type="checkbox" id="option_tallos_chart" onchange="construir_char()"> <span class="badge">Ver Tallos</span>
                </label>
                {{--<label style="margin-left: 10px">
                    <input type="color" value="blue"> <span class="badge">Color Rendimiento</span>
                </label>
                <label style="margin-left: 10px">
                    <input type="color" value="red"> <span class="badge">Color Tallos</span>
                </label>--}}
            </div>
        </div>

        <input type="hidden" id="rendimiento_activo" value="horas">
    </div>

    <script>
        rendimiento_horas();
        construir_char_horas();
        construir_char_ingresos();

        function rendimiento_horas() {
            $('#rendimiento_activo').val('horas');
            $('#chart_rendimiento_horas').show();
            $('#table_rendimiento_horas').show();
            $('#chart_rendimiento_ingresos').hide();
            $('#table_rendimiento_ingresos').hide();
            $('#label_rendimiento_horas').addClass('badge');
            $('#label_rendimiento_ingresos').removeClass('badge');
            $('#check_rendimiento_ingresos').prop('checked', false);
            if ($('#check_rendimiento_horas').prop('checked')) {
                $('.detalle_horas').show();
                $('#th_total_horas').prop('colspan', 3);
            } else {
                $('.detalle_horas').hide();
                $('#th_total_horas').prop('colspan', 1);
            }
        }

        function rendimiento_ingresos() {
            $('#rendimiento_activo').val('ingresos');
            $('#chart_rendimiento_ingresos').show();
            $('#table_rendimiento_ingresos').show();
            $('#chart_rendimiento_horas').hide();
            $('#table_rendimiento_horas').hide();
            $('#label_rendimiento_ingresos').addClass('badge');
            $('#label_rendimiento_horas').removeClass('badge');
            $('#check_rendimiento_horas').prop('checked', false);
            if ($('#check_rendimiento_ingresos').prop('checked')) {
                $('.detalle_ingresos').show();
                $('#th_total_ingresos').prop('colspan', 3);
            } else {
                $('.detalle_ingresos').hide();
                $('#th_total_ingresos').prop('colspan', 1);
            }
        }

        function construir_char_horas() {
            labels = [];
            datasets = [];
            data_rendimiento = [];
            data_tallos = [];
            @foreach($cosecha->getIntervalosHoras() as $intervalo)
            labels.push("{{$intervalo['hora_inicio'].' - '.$intervalo['hora_fin']}}");
            data_rendimiento.push("{{round($cosecha->getTotalTallosByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full'])/$cosecha->personal,2)}}");
            data_tallos.push("{{$cosecha->getTotalTallosByIntervalo($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full'])}}");
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
            @foreach($cosecha->recepciones as $recepcion)
            labels.push("{{substr($recepcion->fecha_ingreso, 11, 5)}}");
            data_rendimiento.push("{{round($recepcion->cantidad_tallos()/$cosecha->personal,2)}}");
            data_tallos.push("{{$recepcion->cantidad_tallos()}}");
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
        No se ha cosechado en la fecha seleccionada
    </div>
@endif