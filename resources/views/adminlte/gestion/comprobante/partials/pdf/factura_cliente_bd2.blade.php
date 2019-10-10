@php
    $detalleFactura = getDetalleFactura($data['comprobante']->id_comprobante);
    $cliente = getCliente(getEnvio($data['comprobante']->envio->id_envio)->pedido->id_cliente)->detalle();
    $factura_tercero = getFacturaClienteTercero(getComprobante($data['comprobante']->id_comprobante)->id_envio);
    $envio = getEnvio($data['comprobante']->envio->id_envio);
    $precio_total_sin_impuestos = 0.00;
    $total_ramos = 0.00;
    $total_piezas = 0.00;
    $full_equivalente_real = 0.00;
    $full = 0;
    $half = 0;
    $cuarto = 0;
    $sexto = 0;
    $octavo = 0;
    $peso_neto = 0;
    $peso_bruto = 0;
    $peso_caja=0;
    $descripcion= "";
    $frac_piezas = 0;
    $total_tallos = 0;
    $datos_tinturados = [];
    $data_body_table =[];
    $pieza=0;
@endphp
<table style="width:100%;font-family:arial, sans-serif">
    <tr>
        <td style="vertical-align: middle;text-align: center">
            <h4>COMERCIAL INVOICE / FACTURA COMERCIAL </h4>
        </td>
    </tr>
    <tr>
</table>
<table style="width:100%;font-family:arial, sans-serif">
    <tr>
        <td>
            <div style="width: 300px;">
                <table>
                    <tr>
                        <td>
                            <b> SHIPPER (Empresa):</b>
                        </td>
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr><td style="font-size: 18px;">{{$detalleFactura->nombre_comercial_emisor}}</td></tr>
                    <tr><td style="font-size:12px">{{$detalleFactura->direccion_matriz_emisor}}</td></tr>
                    <tr><td style="font-size:12px">Quito - {{getPais($data['empresa']->codigo_pais)->nombre}}</td></tr>
                    <tr><td style="font-size:12px">Teléfono: {{$data['empresa']->telefono}}</td></tr>
                    <tr><td style="font-size:12px">Fax: {{$data['empresa']->fax}}</td></tr>
                    <tr><td style="font-size:12px">Email: {{$data['empresa']->correo}}</td></tr>
                </table>
                <table style="margin-top: 10px;">
                    <tr>
                        <td>
                            <b> BUYER (comprador):</b>
                        </td>
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr><td style="font-size:12px">{{$cliente->nombre}}</td></tr>
                    <tr><td style="font-size:12px">{{$cliente->direccion." ".$cliente->provincia}}</td></tr>
                    <tr><td style="font-size:12px">{{getPais($cliente->codigo_pais)->nombre ." ". $cliente->provincia}}</td></tr>
                    <tr><td style="font-size:12px">{{"ID: ".$cliente->ruc}}</td></tr>
                </table>
                {{--<table style="margin-top: 10px;">
                        <td>
                            <b>AQUI LA MARACACIÓN </b>
                        </td>
                    </tr>
                </table>--}}
                <table style="margin-top: 10px;">
                    <tr>
                        <td>
                            <b> BILL TO (Facturado a):</b>
                        </td>
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr><td style="font-size:12px">{{$detalleFactura->razon_social_comprador}}</td></tr>
                    <tr><td style="font-size:12px">{{$factura_tercero !== null ? getPais($factura_tercero->codigo_pais)->nombre : getPais($cliente->codigo_pais)->nombre}} -  {{$factura_tercero !== null ? $factura_tercero->provincia : $cliente->provincia }}</td></tr>
                    <tr><td style="font-size:12px">ID:{{$detalleFactura->identificacion_comprador}}</td></tr>
                </table>

            </div>
        </td>
        <td>
            <div style="width:300px">
                <table>
                    <tr>
                        <td style="text-align: center;font-size: 16px;vertical-align: top">
                            <b> FACTURA <br />
                                No. {{$data['secuencial']}}</b>
                        </td>
                    </tr>
                    <tr> <td style="font-size: 12px">RUC: {{$envio->pedido->empresa->ruc}}</td> </tr>
                    <tr> <td style="font-size: 12px">AUT. SRI. No: {{$data['comprobante']->estado === 5 ? $data['comprobante']->clave_acceso : "Sin valor tributario"}}</td></tr>
                </table>
                <table style="width: 100%;" >
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 12px;width:60% ">
                            <b>Farm Code / Código de Finca</b><br /><br />
                            {{$detalleFactura->razon_social_emisor}}
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 12px">
                            <b>Date / Fecha</b> <br /> <br />
                            {{\Carbon\Carbon::parse($data['comprobante']->fecha_emision)->format('d-m-Y')}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 12px;">
                            <b>Country Code / País</b><br /> <br />
                            {{getPais($data['empresa']->codigo_pais)->nombre}}
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 12px">
                            <b>Invoice No.</b><br /> <br />
                            {{$data['secuencial']}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 12px;width: 60% ">
                            <b>AWB No. / Guía No.</b><br /> <br />
                            {{$envio->guia_madre}}
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 12px">
                            <b>HAWB No.</b><br /> <br />
                            {{$envio->guia_hija}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 12px;width: 60% ">
                            <b>Carrier / Transportador</b><br /> <br />
                            {{getAgenciaTransporte($envio->detalles[0]->id_aerolinea)->nombre}}
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 12px">
                            <b>Add Case No. DAE</b><br /> <br />
                            {{$envio->dae}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 12px;width: 60% ">
                            <b>Port of entry / Puerto de ent</b><br /> <br />
                            {{$factura_tercero !== null ? $factura_tercero->puerto_entrada :  $cliente->puerto_entrada}}
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 12px">
                            <b>Final destination</b><br /> <br />
                            {{$factura_tercero !== null ? getPais($factura_tercero->codigo_pais)->nombre : getPais($cliente->codigo_pais)->nombre}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 12px;width: 60% ">
                    @if($envio->pedido->tipo_especificacion === "N")
                        @foreach($envio->pedido->detalles as $x => $det_ped)
                            @php
                                $precio = explode("|", $det_ped->precio);
                                 $dp = getDetallePedido($det_ped->id_detalle_pedido);
                            @endphp
                            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                                @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                                    @php
                                        $total_ramos += number_format(($det_ped->cantidad*$esp_emp->cantidad*$det_esp_emp->cantidad),2,".","");
                                        $peso_neto += (int)$det_esp_emp->clasificacion_ramo->nombre * number_format(($det_ped->cantidad*$det_esp_emp->cantidad),2,".","");
                                        $peso_caja += isset(explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2]) ? explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2] : 0;
                                    @endphp
                                @endforeach
                            @endforeach
                        @endforeach

                    @elseif($envio->pedido->tipo_especificacion === "T")
                        @foreach ($envio->pedido->detalles as $x => $det_ped)
                            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                                  @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                                       @php
                                            $total_ramos += number_format(($det_ped->cantidad*$esp_emp->cantidad*$det_esp_emp->cantidad),2,".","");
                                            $peso_neto += (int)$det_esp_emp->clasificacion_ramo->nombre * number_format(($det_ped->cantidad*$det_esp_emp->cantidad),2,".","");
                                            $peso_caja += isset(explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2]) ? (explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2]*$det_ped->cantidad) : 0;
                                       @endphp
                                  @endforeach
                             @endforeach
                            @foreach($det_ped->coloraciones as $y => $coloracion)
                                @foreach($coloracion->marcaciones_coloraciones as $m_c)
                                    @if($coloracion->precio=="")
                                        @foreach(explode("|", $det_ped->precio) as $p)
                                            @php
                                                if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                                    $precio = explode(";",$p)[0];
                                            @endphp
                                        @endforeach
                                    @else
                                        @php
                                            foreach(explode("|",$coloracion->precio) as $p)
                                                if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                                    $precio = explode(";",$p)[0];
                                        @endphp
                                    @endif
                                    @php
                                        $precio_x_variedad = $m_c->cantidad * $precio * $coloracion->especificacion_empaque->cantidad;
                                        $precio_total_sin_impuestos += $precio_x_variedad;
                                    @endphp
                                @endforeach
                            @endforeach
                        @endforeach
                    @endif
                            <b>Net Weight Kg. {{number_format(($peso_neto/1000),2,".","")}}</b><br />
                            <b>Gross Weight Kg. {{number_format(($peso_neto/1000),2,".","")+($peso_caja/1000),2,".",""}}</b>
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 12px">
                            <b>Fecha embarque</b><br />
                            {{\Carbon\Carbon::parse($data['comprobante']->fecha_emision)->addDay(1)->format('d-m-Y')}}
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<table style="width:100%;font-family:arial, sans-serif;">
    <thead style="border-bottom: 1px solid;border-top: 1px solid">
        <tr >
            <th style="font-size: 11px;vertical-align: top">
                PIECES<br />
                Piezas
            </th>
            <th style="font-size: 11px;vertical-align: top">
                DESCRIPTION<br />
                Descripción
            </th>
            <th style="font-size: 11px;vertical-align: top">
                SGP
            </th>
            <th style="font-size: 11px;vertical-align: top">
                HTS<br />
                Tarifa
            </th>
            <th style="font-size: 11px;vertical-align: top">
                NANDINA
            </th>
            <th style="font-size: 11px;vertical-align: top">
                Bunches/Box<br />
                Ramos/Caja
            </th>
            <th style="font-size: 11px;vertical-align: top">
                ST/BN
            </th>
            <th style="font-size: 11px;vertical-align: top">
                TOTAL ST/BN
            </th>
            <th style="font-size: 11px;vertical-align: top;width:70px">
                PRICE UNIT<br />
                Precio US$
            </th>
            <th style="font-size: 11px;vertical-align: top">
                TOTAL <br />US$
            </th>
        </tr>
    </thead>
    <tbody style="border-bottom: 1px solid">
    @if($envio->pedido->tipo_especificacion === "N")
        @foreach($envio->pedido->detalles as $x => $det_ped)
            @php
                $precio = explode("|", $det_ped->precio);
                $i = 0;
            @endphp
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                    @php
                        $total_tallos += number_format(($det_ped->cantidad*$esp_emp->cantidad*$det_esp_emp->cantidad*$det_esp_emp->tallos_x_ramos),2,".","");
                        $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                        $descripcion = substr($det_esp_emp->variedad->planta->nombre, 0, 3) .", ". $det_esp_emp->variedad->nombre;

                    @endphp
                    <tr>
                        @if($n == 0)
                            <td style="font-size:11px;vertical-align:middle;text-aling:center" rowspan="{{$det_ped->cliente_especificacion->especificacion->especificacionesEmpaque->count()}}">
                                {{number_format($det_ped->cantidad,2,".","")}}
                                @php
                                    $total_piezas += $det_ped->cantidad;
                                    switch (explode("|",$esp_emp->empaque->nombre)[1]) {
                                        case '1':
                                            $full += $det_ped->cantidad;
                                            break;
                                        case '0.5':
                                            $half += $det_ped->cantidad;
                                            break;
                                        case '0.25':
                                            $cuarto +=$det_ped->cantidad;
                                            break;
                                        case '0.17':
                                            $sexto +=$det_ped->cantidad;
                                            break;
                                        case '0.125':
                                            $octavo +=$det_ped->cantidad;
                                            break;
                                     }
                                @endphp

                            </td>
                        @endif
                        <td style="font-size:11px">{{$descripcion}}</td>
                        <td style="font-size:11px"> A</td>
                        <td style="font-size:11px"> {{$det_esp_emp->variedad->planta->tarifa}}</td>
                        <td style="font-size:11px"> {{$det_esp_emp->variedad->planta->nandina}}</td>
                        <td style="font-size:11px"> {{$det_esp_emp->cantidad}}</td>
                        <td style="font-size:11px"> BN </td>
                        <td style="font-size:11px"> {{number_format(($det_ped->cantidad*$det_esp_emp->cantidad),2,".","")}} </td>
                        @php $total_ramos += number_format(($det_ped->cantidad*$det_esp_emp->cantidad),2,".",""); @endphp
                        <td style="font-size:11px;"> {{"$".number_format(explode(";", $precio[$i])[0],2,".","")}} </td>
                        <td style="font-size:11px"> {{"$".number_format(($det_esp_emp->cantidad * ((float)explode(";", $precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad),2,".","")}} </td>
                    </tr>
                    @php $precio_total_sin_impuestos +=  ($det_esp_emp->cantidad * ((float)explode(";", $precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad); @endphp
                    @php  $i++;  @endphp
                @endforeach
            @endforeach
        @endforeach
    @elseif($envio->pedido->tipo_especificacion === "T")
        @php $data_body_table=[]; @endphp
        @foreach ($envio->pedido->detalles as $x => $det_ped)
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                    @php
                        $total_tallos += number_format(($det_ped->cantidad*$esp_emp->cantidad*$det_esp_emp->cantidad*$det_esp_emp->tallos_x_ramos),2,".","");
                        $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                        switch (explode("|",$esp_emp->empaque->nombre)[1]) {
                            case '1':
                                $full += $det_ped->cantidad;
                                break;
                            case '0.5':
                                $half += $det_ped->cantidad;
                                break;
                            case '0.25':
                                $cuarto +=$det_ped->cantidad;
                                break;
                            case '0.17':
                                $sexto +=$det_ped->cantidad;
                                break;
                            case '0.125':
                                $octavo +=$det_ped->cantidad;
                                break;
                        }
                    @endphp
                @endforeach
            @endforeach
            @foreach($det_ped->coloraciones as $y => $coloracion)
                @foreach($coloracion->marcaciones_coloraciones as $m_c)
                    @if($m_c->cantidad > 0)
                        @if($coloracion->precio=="")
                            @foreach (explode("|", $det_ped->precio) as $p)
                                @php
                                    if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                        $precio = explode(";",$p)[0];
                                @endphp
                            @endforeach
                        @else
                            @foreach(explode("|",$coloracion->precio) as $p)
                                @php
                                    if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                        $precio = explode(";",$p)[0];
                                @endphp
                            @endforeach
                        @endif
                        @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                            @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                                @if($det_esp_emp->id_detalle_especificacionempaque === $m_c->id_detalle_especificacionempaque)
                                    @php
                                        $frac_piezas = $m_c->cantidad/$det_esp_emp->cantidad;
                                        $descripcion = substr($det_esp_emp->variedad->planta->nombre, 0, 3) .", ". $det_esp_emp->variedad->nombre;
                                        $pieza += $frac_piezas;
                                    @endphp
                                @endif
                            @endforeach
                            @php
                                $data_body_table[$m_c->detalle_especificacionempaque->variedad->planta->id_planta][$m_c->detalle_especificacionempaque->variedad->id_variedad][$precio][]=[
                                    'ramos' => $m_c->cantidad,
                                    'precio'=> $precio,
                                    'hts' => $m_c->detalle_especificacionempaque->especificacion_empaque->detalles[0]->variedad->planta->tarifa,
                                    'nandina' =>$m_c->detalle_especificacionempaque->especificacion_empaque->detalles[0]->variedad->planta->nandina,
                                    'descripcion' =>substr($m_c->detalle_especificacionempaque->variedad->planta->nombre, 0, 3) .", ". $m_c->detalle_especificacionempaque->variedad->nombre,
                                    'piezas'=> number_format(($m_c->cantidad/$m_c->detalle_especificacionempaque->cantidad),2,".","")
                                ];
                            @endphp
                        @endforeach
                    @endif
                @endforeach
            @endforeach
        @endforeach
        @foreach($data_body_table as $body_table)
            @foreach($body_table as $table)
                @foreach($table as $t)
                    @php
                        $pie=0; //Sumatoria Piezas
                        $ram = 0;//Sumatoria Ramos
                    @endphp
                    @foreach($t as $ta)
                        @php
                            $pie+=$ta['piezas'];
                            $ram+=$ta['ramos'];
                        @endphp
                    @endforeach
                    @php $total_piezas += $pie @endphp
                    <tr>
                        <td style="font-size:12px">{{number_format($pie,2,".","")}}</td>
                        <td style="font-size:12px">{{$t[0]['descripcion']}}</td>
                        <td style="font-size:12px">A</td>
                        <td style="font-size:12px">{{$t[0]['hts']}}</td>
                        <td style="font-size:12px">{{$t[0]['nandina']}}</td>
                        <td style="font-size:12px"> {{$ram/$pie}} </td>
                        <td style="font-size:12px">BN</td>
                        <td style="font-size:12px">{{$ram}}</td>
                        <td style="font-size:12px">${{number_format($t[0]['precio'],2,".","")}}</td>
                        <td style="font-size:12px">${{number_format(($ram*$t[0]['precio']),2,".","")}}</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    @endif
    </tbody>
</table>
<table style="width: 100%">
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{number_format($total_ramos,2,".","")}}</b> </td>
        <td style="font-size:11px;font-family: arial, sans-serif;width:300px"><b>TOTAL BN</b></td>
        <td style="font-size:11px;font-family: arial, sans-serif;text_align:right">SUBTOTAL : ${{number_format($precio_total_sin_impuestos,2,".","")}}</td>
    </tr>
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{$total_tallos}}</b> </td>
        <td style="font-size:11px;font-family: arial, sans-serif;width:300px"><b>TOTAL STEMS</b></td>
        @php $tipoImpuesto = getTipoImpuesto($envio->pedido->cliente->detalle()->codigo_impuesto, $envio->pedido->cliente->detalle()->codigo_porcentaje_impuesto); @endphp
        <td style="font-size:11px;font-family: arial, sans-serif;text_align:right">{{$tipoImpuesto->nombre}} : ${{is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_total_sin_impuestos * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00"}}</td>
    </tr>
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{number_format($total_piezas,2,".","")}}</b> </td>
        <td style="font-size:11px;font-family: arial, sans-serif;width:300px"><b>TOTAL PIECES / TOTAL PIEZAS</b></td>
        @php $tipoImpuesto = $tipoImpuesto = getTipoImpuesto($envio->pedido->cliente->detalle()->codigo_impuesto, $envio->pedido->cliente->detalle()->codigo_porcentaje_impuesto); @endphp
        <td style="font-size:11px;font-family: arial, sans-serif;text_align:right">TOTAL : ${{is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_total_sin_impuestos + ($precio_total_sin_impuestos * ($tipoImpuesto->porcentaje / 100)), 2, ".", "") : number_format($precio_total_sin_impuestos, 2, ".", "")}}</td>
    </tr>
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{$full_equivalente_real}}</b> </td>
        <td colspan="2" style="font-size:11px;font-family: arial, sans-serif"><b>TOTAL FULL BOXES EQUIVALENT / TOTAL CAJAS </b></td>
    </tr>
</table>
<table style="width:100%">
    <tr>
        <td style="font-size:12px;font-family: arial, sans-serif"> <b> FULL BOXES : [{{number_format($full,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif"> <b> HALF BOXES : [{{number_format($half,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif"> <b> 1/4 BOXES : [{{number_format($cuarto,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif"> <b> 1/6 BOXES : [{{number_format($sexto,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif"> <b> 1/8 BOXES : [{{number_format($octavo,2,".","")}}]</b></td>
    </tr>
</table>
<table style="width: 100%;margin-top:30px">
    <tr>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 11px">
            <img src="./images/firma_FABIOLA_SIERRA.jpg">
            <hr style="width: 60%;margin: 0 auto"/>
            FIRMA
        </td>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 12px;width:50%">
            <label >MARKETING NAME</label>
            <div style="border: 1px solid;height: 20px"><b>{{strtoupper($factura_tercero !== null ? $factura_tercero->marca_caja->nombre : $cliente->marca_caja->nombre)}}</b></div>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 11px">
            FABIOLA SIERRA
        </td>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 11px">
            FREIGHT FORWARDER
        </td>
    </tr>
    <tr>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 11px">
            NAME AND TITLE OF PERSON PREPARING INVOICE
        </td>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 11px">
            <div style="border: 1px solid;height: 20px;font-size: 12px"> <b>{{strtoupper(getAgenciaCarga($envio->pedido->detalles[0]->id_agencia_carga)->nombre)}}</b></div>
        </td>
    </tr>
</table>
<table style="margin-top: 20px;width: 100%;">
    <tr>
        <td colspan="2" style="vertical-align: bottom;font-family:arial, sans-serif;font-size: 11px">
           <b>{{isset($cliente->informacion_adicional('Forma de pago')->varchar) ? $cliente->informacion_adicional('Forma de pago')->varchar : ""}}</b>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: bottom;font-family:arial, sans-serif;font-size: 12px">
            The flower and plants on this invoice were who
        </td>
    </tr>
    <tr>
        <td style="text-align: center;vertical-align: middle;font-family:arial, sans-serif;font-size: 11px;width:50%;border: 1px solid;height: 20px">
            CUSTOM USE ONLY
        </td>
        <td style="text-align: center;vertical-align: middle;font-family:arial, sans-serif;font-size: 11px;width:50%;border: 1px solid;height: 20px">
            USDA APHIS P.P.Q USE ONLY
        </td>
    </tr>
</table>
