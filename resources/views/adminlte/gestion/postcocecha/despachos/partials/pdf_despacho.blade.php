<table width="100%" style="font-size: 0.8em">
    <tr>
        <td style="text-align: left;border:1px solid #9d9d9d"><b>Despacho N#: {{str_pad(($data['despacho'][0]->n_despacho), 9, "0", STR_PAD_LEFT)}}</b></td>
        <td style="text-align: center;border:1px solid #9d9d9d"><b>DESPACHO DE FINCA</b></td>
        <td style="text-align: right;border:1px solid #9d9d9d"><b>{{strtoupper($data['empresa']->razon_social)}}</b></td>
    </tr>
</table>
<table width="100%" style="font-size: 0.8em;">
    <tr>
        <td colspan="2" style="border:1px solid #9d9d9d;vertical-align: middle;font-size: 0.7em;" >
            <ul style="list-style: none;padding: 0;margin: 0">
                <li>
                    CÓDIGO: LS-3.1-04
                </li>
                <li style="list-style: none;padding: 0;margin: 0">
                    FECHA : 01/NOV/2008
                </li>
                <li style="list-style: none;padding: 0;margin: 0">
                    VERSIÓN : 01
                </li>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"><b>Transportisa</b></td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{getTransportista($data['despacho'][0]->id_transportista)->nombre_empresa}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Camión</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{getCamion($data['despacho'][0]->id_camion)->modelo}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Placa</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{getCamion($data['despacho'][0]->id_camion)->placa}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Chofer</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            @php $conductor = getChofer($data['despacho'][0]->id_conductor) @endphp
            {{$conductor->nombre." C.I:".$conductor->ruc}}
        </td>
    </tr>
    <tr>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Fecha</b>
        </td>
        <td style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->fecha_despacho}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Sello de salida</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->sello_salida}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Responsable</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle" >
            {{$data['despacho'][0]->resp_transporte}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Horario</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->horario}}
        </td>
    </tr>
    <tr>
        <td  class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Semana</b>
        </td>
        <td style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->semana}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Rango Tmp</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->rango_temp}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Sellos entregados</b>
        </td>
        <td class="text-center" id="cant_sellos" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{count(explode("|",$data['despacho'][0]->sellos))}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Sellos adicionales</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->rango_temp}}
        </td>
    </tr>
    <tr>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Viaje N#</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->n_viaje}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Hora de salida</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->hora_salida}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Temperatura</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->temp}}
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Kilometraje</b>
        </td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            {{$data['despacho'][0]->kilometraje}}
        </td>
    </tr>
    <tr>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
            <b>Sellos</b>
        </td>
        @php $arr_sellos = explode("|",$data['despacho'][0]->sellos); @endphp
        @foreach ($arr_sellos as $sello)
            <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
                {{$sello}}
            </td>
        @endforeach
    </tr>
</table>
<table width="100%" style="font-size: 0.8em">
    <thead>
        <tr>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center;width: 170px;">Cliente</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Agencia</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Anden</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Cajas Fules</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Piezas</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Guia</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Temp</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Persona que recibe</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Hora de llegada</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Hora de salida</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Sello inicial</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center">Sello final</td>
            <td class="text-center" style="border:1px solid #9d9d9d;text-align: center;width: 110px;">Observaciones</td>
        </tr>
    </thead>
    <tbody>

        @php $total_caja_full = 0; $piezas_totales = 0; @endphp
        @foreach($data['despacho'] as $pedido)
            <tr>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
                    @foreach(getPedido($pedido->id_pedido)->cliente->detalles as $det_cliente)
                        {{$det_cliente->estado == 1 ? $det_cliente->nombre : "" }}
                    @endforeach
                    <input type="hidden" class="id_pedido" name="id_pedido" value="{{$pedido->id_pedido}}">
                </td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
                    {{getPedido($pedido->id_pedido)->detalles[0]->agencia_carga->nombre}}
                </td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
                    {{getPedido($pedido->id_pedido)->envios[0]->almacen}}
                </td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle;text-align:center">
                    @php $caja_full = 0; @endphp
                    @foreach(getPedido($pedido->id_pedido)->detalles as $det_ped)
                        @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                            @php $caja_full += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1] @endphp
                        @endforeach
                    @endforeach
                    @php $total_caja_full +=  $caja_full @endphp
                    {{$caja_full}}
                </td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle;text-align:center">
                    {{$pedido['cantidad']}}
                    @php $piezas_totales += $pedido['cantidad']; @endphp
                </td>

                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle">
                    {{ getPedido($pedido->id_pedido)->envios[0]->detalles[0]->id_aerolinea ==  ""
                         ? "No se ha asignado aerolínea"
                         : getPedido($pedido->id_pedido)->envios[0]->detalles[0]->aerolinea->codigo}}
                </td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"></td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"></td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"> </td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"></td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"></td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"></td>
                <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle"></td>
            </tr>
        @endforeach
    <tr>
        <td colspan="2"></td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle;text-align:center">Total:</td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle;text-align:center"> {{$total_caja_full}}</td>
        <td class="text-center" style="border:1px solid #9d9d9d;vertical-align: middle;text-align:center"> {{$piezas_totales}}</td>
    </tr>
    <tr>
    </tr>
    </tbody>
</table>
<table width="100%" class="table-responsive table-bordered" style="border:none;margin-top:50px;font-size: 0.8em">
    <tr>
        <td class="text-center" style="border:none;vertical-align: middle">
            __________________________
            <br/>
            Oficina de despacho
            <br/>
            {{$data['despacho'][0]->resp_ofi_despacho}}
            <br/>
            {{"CI: ".$data['despacho'][0]->id_resp_ofi_despacho}}
        </td>
        <td class="text-center" style="vertical-align: middle">
            __________________________
            <br/>
            Aux. de Cuartos Fríos
            <br/>
            {{$data['despacho'][0]->aux_cuarto_fri}}
            <br/>
            {{"CI: ".$data['despacho'][0]->id_aux_cuarto_fri}}
        </td>
        <td class="text-center" style="vertical-align: middle">
            __________________________
            <br/>
            Responsable de transporte
            <br/>
            {{$data['despacho'][0]->resp_transporte}}
            <br/>
            CI:
        </td>
        <td class="text-center" style="vertical-align: middle">
            __________________________
            <br/>
            Guardia de turno
            <br/>
            {{$data['despacho'][0]->guardia_turno}}
            <br/>
            {{"CI: ".$data['despacho'][0]->id_guardia_turno}}
        </td>
        <td class="text-center" style="vertical-align: middle">
            __________________________
            <br/>
            Jefe de ventas
            <br/>
            {{$data['despacho'][0]->asist_comercial_ext}}
            <br/>
            {{"CI: ".$data['despacho'][0]->id_asist_comrecial_ext}}
        </td>
    </tr>
</table>
