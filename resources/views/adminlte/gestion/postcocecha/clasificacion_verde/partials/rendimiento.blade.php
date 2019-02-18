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
        <label for="check_mostrar_ocultar_detalles_rendimiento" class="pull-right mouse-hand" style="margin-left: 5px"
               id="label_rendimiento_x_ingresos">
            Rendimiento por ingresos
        </label>
        <input type="checkbox" class="pull-right" onchange="ocultar_mostrar_detalles_rendimiento()"
               id="check_mostrar_ocultar_detalles_rendimiento" title="Mostrar/ocultar detalles">
        <input type="checkbox" class="pull-left" onchange="ocultar_mostrar_detalles_rendimiento_x_horas()"
               id="check_mostrar_ocultar_detalles_rendimiento_x_horas" title="Mostrar/ocultar detalles">
        <label for="check_mostrar_ocultar_detalles_rendimiento_x_horas" class="pull-left mouse-hand" style="margin-left: 5px"
               id="label_rendimiento_x_horas">
            Rendimiento por horas
        </label>

        <table id="table_rendimiento_x_ingresos" class="table-responsive table-striped table-bordered" width="100%"
               style="border: 2px solid #9d9d9d; display: none;">
            <tr style="background-color: #e9ecef">
                <th class="text-center" style="border-color: #9d9d9d">
                    Fecha y Hora
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
                        {{$item->fecha}}
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
        </table>

        <table id="table_rendimiento_x_horas" class="table-responsive table-striped table-bordered" width="100%"
               style="border: 2px solid #9d9d9d; display: none;">
            <tr style="background-color: #e9ecef">
                <th class="text-center" style="border-color: #9d9d9d">
                    Fecha y Hora
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
                        {{$item['fecha_inicio']}} <span class="badge">{{$item['hora_inicio']}}</span> -
                        {{$item['fecha_fin']}} <span class="badge">{{$item['hora_fin']}}</span>
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
</div>

<script>
    function ocultar_mostrar_detalles_rendimiento() {
        $('#table_rendimiento_x_ingresos').show();
        $('#label_rendimiento_x_ingresos').addClass('badge');
        $('#label_rendimiento_x_horas').removeClass('badge');
        $('#table_rendimiento_x_horas').hide();
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
        $('.elemento_especifico_horas').show();
        $('#label_rendimiento_x_horas').addClass('badge');
        $('#label_rendimiento_x_ingresos').removeClass('badge');
        $('#table_rendimiento_x_ingresos').hide();
        $('#check_mostrar_ocultar_detalles_rendimiento').prop('checked', false);
        if ($('#check_mostrar_ocultar_detalles_rendimiento_x_horas').prop('checked')) {
            $('#table_rendimiento_x_horas').show();
            $('#th_total_tallos_x_horas').prop('colspan', 5);
        } else {
            $('.elemento_especifico_horas').hide();
            $('#th_total_tallos_x_horas').prop('colspan', 1);
        }
    }
</script>