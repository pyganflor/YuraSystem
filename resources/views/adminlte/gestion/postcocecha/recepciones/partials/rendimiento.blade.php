<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Rendimiento de <span class="badge">{{$cosecha->personal}}</span> personas en
            <span class="badge">{{$cosecha->getCantidadHorasTrabajo()}}</span> horas de trabajo:
            <span class="badge">{{$cosecha->getRendimiento()}}</span> tallos por persona/hora
        </h3>
    </div>
    <div class="box-body">
        <input type="checkbox" id="check_rendimiento_horas" name="check_rendimiento_horas" onchange="rendimiento_horas()">
        <label for="check_rendimiento_horas" class="mouse-hand" style="margin-left: 5px" onclick="rendimiento_horas()"
               id="label_rendimiento_horas">
            Rendimiento por horas
        </label>

        <label for="check_rendimiento_ingresos" class="mouse-hand pull-right" style="margin-left: 5px" onclick="rendimiento_ingresos()"
               id="label_rendimiento_ingresos">
            Rendimiento por ingresos
        </label>
        <input type="checkbox" id="check_rendimiento_ingresos" name="check_rendimiento_ingresos" class="pull-right"
               onchange="rendimiento_ingresos()">

        <table class="table-striped table-bordered table-responsive" width="100%" id="table_rendimiento_horas"
               style="border: 2px solid #9d9d9d; display: none;">
            <tr>
                <th class="text-center" style="border-color: #9d9d9d">
                    Fecha y Hora
                </th>
                <th class="text-center detalle_horas" style="border-color: #9d9d9d">
                    Variedad
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
                        {{$intervalo['fecha_inicio']}} <span class="badge">{{$intervalo['hora_inicio']}}</span>
                        {{$intervalo['fecha_fin']}} <span class="badge">{{$intervalo['hora_fin']}}</span>
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
                    Fecha y Hora
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
                        {{substr($recepcion->fecha_ingreso, 0, 16)}}
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
</div>

<script>
    function rendimiento_horas() {
        $('#table_rendimiento_horas').show();
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
        $('#table_rendimiento_ingresos').show();
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
</script>