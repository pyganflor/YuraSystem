<div style="overflow-x: scroll">
    <table class="table-bordered table-striped table-responsive" width="100%" style="border: 2px solid #9d9d9d">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d">
                Hora
                <a href="javascript:void(0)" class="pull-left btn btn-xs btn-default" title="Mostrar/Ocultar Detalles"
                   onclick="mostrar_ocultar_rendimiento()">
                    <i class="fa fa-fw fa-eye"></i>
                </a>
                <input type="checkbox" id="check_mostrar_ocultar_rendimiento" style="display: none" checked>

            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d">
                Variedad
            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d">
                Peso
            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d">
                Presentación
            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d">
                Tallos
            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d">
                Longitud
            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d">
                Cantidad
            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d">
                Total x Variedad
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Total
            </th>
            <th class="text-center elemento_horas" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                x Presentación
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                Rendimiento
            </th>
        </tr>
        @php
            $rendimiento_anterior = 0;
        @endphp
        @foreach($blanco->getIntervalosHoras() as $pos_intervalo => $intervalo)
            @php
                $total_x_intervalo = 0;
            @endphp
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    <span class="badge">{{$intervalo['hora_inicio']}}</span>
                    <span class="badge">{{$intervalo['hora_fin']}}</span>
                    <br>
                    @if(substr($intervalo['fecha_inicio_full'],0,10) != $blanco->fecha_ingreso)
                        <em>{{substr($intervalo['fecha_inicio_full'],0,10)}}</em>
                    @endif
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getInventariosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $inv)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                {{getVariedad($inv->id_variedad)->siglas}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getInventariosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $inv)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                {{getClasificacionRamo($inv->id_clasificacion_ramo)->nombre}}
                                {{getClasificacionRamo($inv->id_clasificacion_ramo)->unidad_medida->siglas}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getInventariosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $inv)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                {{getEmpaque($inv->id_empaque_p)->nombre}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getInventariosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $inv)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                {{$inv->tallos_x_ramo}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getInventariosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $inv)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                @if($inv->longitud_ramo != '')
                                    {{$inv->longitud_ramo}} {{getUnidadMedida($inv->id_unidad_medida)->siglas}}
                                @else
                                    -
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getInventariosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $inv)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                {{$inv->cantidad}}
                            </li>
                            @php
                                $total_x_intervalo += $inv->cantidad;
                            @endphp
                        @endforeach
                    </ul>
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getVariedadesByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $v)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                {{getVariedad($v->id_variedad)->siglas}}
                                <strong>{{$blanco->getTotalRamosByVariedadIntervaloFecha($v->id_variedad, $intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full'])}}</strong>
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$total_x_intervalo}}
                </td>
                <td class="text-center elemento_horas" style="border-color: #9d9d9d">
                    <ul class="list-unstyled">
                        @foreach($blanco->getInventariosByIntervaloFecha($intervalo['fecha_inicio_full'], $intervalo['fecha_fin_full']) as $inv)
                            <li class="text-center" style="border: 1px solid #9d9d9d">
                                {{round($inv->cantidad / $blanco->personal, 2)}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{round($total_x_intervalo / $blanco->personal, 2)}}
                    @if($pos_intervalo > 0)
                        @if(round($total_x_intervalo / $blanco->personal, 2) > $rendimiento_anterior)
                            <i class="fa fa-fw fa-arrow-up pull-right" style="color: green"></i>
                        @elseif(round($total_x_intervalo / $blanco->personal, 2) < $rendimiento_anterior)
                            <i class="fa fa-fw fa-arrow-down pull-right" style="color: red"></i>
                        @else
                            <i class="fa fa-fw fa-exchange pull-right" style="color: orange"></i>
                        @endif
                    @endif
                </td>
            </tr>
            @php
                $rendimiento_anterior = round($total_x_intervalo / $blanco->personal, 2);
            @endphp
        @endforeach
        <tr>
            <th class="text-center" style="border-color: #9d9d9d">
                Total
            </th>
            <th class="text-center" id="th_total_rendimiento" style="border-color: #9d9d9d" colspan="8">
                {{$blanco->total_ramos()}}
            </th>
        </tr>
    </table>
</div>