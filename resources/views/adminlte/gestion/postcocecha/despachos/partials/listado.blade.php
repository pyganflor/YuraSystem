<div id="table_despachos">
    @if(count($listado)>0)
        <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_aperturas">
            <tr>
                <th style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2">
                    <ul class="list-unstyled">
                        <li>
                            Semana: {{getSemanaByDate($fecha)->codigo}}
                        </li>
                        <li>
                            Día: {{getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime($fecha)))]}}
                        </li>
                    </ul>
                </th>
                <th style="border-color: #9d9d9d; background-color: #e9ecef" class="text-right" colspan="7">
                    <button type="button" class="btn btn-xs btn-success">
                        <i class="fa fa-fw fa-file-excel-o"></i> Exportar a Excel
                    </button>
                </th>
            </tr>
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    CLIENTE
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    FLOR
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    EMPAQUE
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    PRESENTACIÓN
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    RAMOS x CAJA
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    RAMOS
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    PIEZAS
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    CAJAS FULL
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                    PEDIDO
                </th>
            </tr>
            @php
                $piezas_totales = 0;
                $ramos_totales = 0;
                $ramos_totales_estandar = 0;
                $cajas_full_totales = 0;

            $variedades = [];
            $ramos_x_variedades = [];
            @endphp
            @foreach($listado as $pedido)
                @foreach(getPedido($pedido->id_pedido)->detalles as $pos_det_ped => $det_ped)
                    @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp => $esp_emp)
                        @foreach($esp_emp->detalles as $pos_det_esp => $det_esp)
                            <tr style="background-color: {{!in_array($det_esp->id_variedad,explode('|',$pedido->variedad)) ? '#b9ffb4' : ''}}"
                                title="{{!in_array($det_esp->id_variedad,explode('|',$pedido->variedad)) ? 'Confirmado' : 'Por confirmar'}}">
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{getCliente($pedido->id_cliente)->detalle()->nombre}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp->variedad->siglas}}
                                    {{explode('|',$det_esp->clasificacion_ramo->nombre)[0]}}{{$det_esp->clasificacion_ramo->unidad_medida->siglas}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{--{{explode('|',$det_esp->empaque_e->nombre)[0]}}--}}
                                    {{explode('|',$det_esp->empaque_p->nombre)[0]}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp->cantidad}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad}}
                                    @php
                                        $ramos_totales += $det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad;
                                        $ramos_totales_estandar += convertToEstandar($det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad, $det_esp->clasificacion_ramo->nombre);

                                    if (!in_array($det_esp->id_variedad, $variedades)){
                                        array_push($variedades, $det_esp->id_variedad);
                                    }
                                    array_push($ramos_x_variedades, [
                                        'id_variedad' => $det_esp->id_variedad,
                                        'cantidad' => convertToEstandar($det_esp->cantidad * $esp_emp->cantidad * $det_ped->cantidad, $det_esp->clasificacion_ramo->nombre),
                                    ]);
                                    @endphp
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$esp_emp->cantidad * $det_ped->cantidad}}
                                    @php
                                        $piezas_totales += ($esp_emp->cantidad * $det_ped->cantidad);
                                    @endphp
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1]}}
                                    @php
                                        $cajas_full_totales += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1];
                                    @endphp
                                </td>
                                @if($pos_det_esp == 0 && $pos_esp == 0 && $pos_det_ped == 0)
                                    <td class="text-center" style="border-color: #9d9d9d"
                                        rowspan="{{getCantidadDetallesEspecificacionByPedido($pedido->id_pedido)}}">
                                        PED-0{{$pedido->id_pedido}}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </table>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-4">
                <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" colspan="2">
                            TOTALES RAMOS POR VARIEDAD
                        </th>
                    </tr>
                    @foreach($ramos_x_variedad as $item)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{getVariedad($item->id_variedad)->siglas}}
                                {{explode('|',getCalibreRamoById($item->id_clasificacion_ramo)->nombre)[0]}}{{getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                                @if($item->tallos_x_ramos != '')
                                    {{$item->tallos_x_ramos}} tallos
                                @endif
                                @if($item->longitud_ramo != '')
                                    {{$item->longitud_ramo}} {{getUnidadMedida($item->id_unidad_medida)->siglas}}
                                @endif
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$item->cantidad}}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Ramos Totales Pedidos
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$ramos_totales}}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white" colspan="2">
                            CAJAS EQUIVALENTES
                        </th>
                    </tr>
                    @php
                        $cajas_equivalentes = [];
                        foreach ($variedades as $variedad){
                            $cantidad = 0;
                            foreach($ramos_x_variedades as $ramos){
                                if($ramos['id_variedad'] == $variedad){
                                    $cantidad += $ramos['cantidad'];
                                }
                            }
                            array_push($cajas_equivalentes, [
                                'id_variedad' => $variedad,
                                'cantidad' => round($cantidad / getConfiguracionEmpresa()->ramos_x_caja, 2),
                            ]);
                        }
                    @endphp
                    @foreach($cajas_equivalentes as $item)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{getVariedad($item['id_variedad'])->nombre}}
                                ({{getVariedad($item['id_variedad'])->siglas}})
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$item['cantidad']}}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Cajas Equivalentes Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{round($ramos_totales_estandar / getConfiguracionEmpresa()->ramos_x_caja,2)}}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Piezas Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$piezas_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Ramos Totales Pedidos
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$ramos_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Cajas Full Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{$cajas_full_totales}}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
                            Cajas Equivalentes Totales Pedidas
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{round($ramos_totales_estandar / getConfiguracionEmpresa()->ramos_x_caja,2)}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado pedidos para esta fecha</div>
    @endif
</div>