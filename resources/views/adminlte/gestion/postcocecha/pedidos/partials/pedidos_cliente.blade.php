<table class="table-bordered" style="width: 100%; border: 2px solid #9d9d9d; margin-bottom: 10px">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">{{$cliente->detalle()->nombre}}</th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">{{$mes}}</th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">{{$anno}}</th>
    </tr>
</table>
<table class="table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Fecha
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Empaque
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Flor
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Presentación
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Piezas
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Cajas Full
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Ramos
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Ramos x Caja
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Dinero
        </th>
    </tr>
    @php
        $piezas = 0;
        $cajas = 0;
        $ramos = 0;
        $dinero = 0;
    @endphp
    @foreach($pedidos as $pos_p => $p)
        @php
            $getCantidadDetallesEspecificacionByPedido = getCantidadDetallesEspecificacionByPedido($p->id_pedido);
            $anulado = getFacturaAnulada($p->id_pedido);
        @endphp
        @foreach($p->detalles as $pos_det => $det_ped)
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_emp => $esp_emp)
                @php
                    $getCantidadDetallesByEspecificacion = getCantidadDetallesByEspecificacion($esp_emp->id_especificacion);
                @endphp
                @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                    <tr style="border-bottom: 2px solid #9d9d9d; color: {{$anulado ? 'red' : ''}}">
                        @if($pos_det_esp == 0 && $pos_emp == 0 && $pos_det == 0)
                            <td class="text-center" style="border-color: #9d9d9d"
                                rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                {{$p->fecha_pedido}}
                            </td>
                        @endif
                        @if($pos_det_esp == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                {{explode('|', $esp_emp->empaque->nombre)[0]}}
                            </td>
                        @endif
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->variedad->siglas}}
                            {{$det_esp->clasificacion_ramo->nombre}}{{$det_esp->clasificacion_ramo->unidad_medida->siglas}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$det_esp->empaque_p->nombre}}
                        </td>
                        @if($pos_det_esp == 0)
                            @php
                                $piezas += $esp_emp->cantidad * $det_ped->cantidad;
                            @endphp
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{$getCantidadDetallesByEspecificacion}}">
                                {{$esp_emp->cantidad * $det_ped->cantidad}}
                            </td>
                        @endif
                        @if($pos_det_esp == 0)
                            @php
                                $cajas += $esp_emp->cantidad * $det_ped->cantidad * explode('|',$esp_emp->empaque->nombre)[1];
                            @endphp
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                {{($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1]}}
                            </td>
                        @endif
                        @php
                            $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp->id_detalle_especificacionempaque);
                            $ramos += (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad)
                        @endphp
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp->cantidad)}}
                        </td>
                        @if($pos_det_esp == 0 && $pos_emp == 0 && $pos_det == 0)
                            @php
                                $valor = $p->getPrecioByPedido();
                                $dinero += $anulado ? 0 : $valor;
                            @endphp
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{$getCantidadDetallesEspecificacionByPedido}}">
                                <strong>${{number_format($valor, 2)}}</strong>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    @endforeach
    {{-- TOTALES --}}
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d" colspan="4">
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            {{number_format($piezas, 2)}}
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            {{number_format($cajas, 2)}}
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            {{number_format($ramos, 2)}}
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">

        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            {{number_format($dinero, 2)}}
        </th>
    </tr>
</table>
