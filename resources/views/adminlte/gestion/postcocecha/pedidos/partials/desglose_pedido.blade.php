@php
    $detalleFactura = isset($pedido->envios[0]->comprobante->detalle_factura) ? $pedido->envios[0]->comprobante->detalle_factura : null;
    $cliente = $pedido->cliente->detalle();
    $consignatario = isset($pedido->envios[0]->consignatario) ? $pedido->envios[0]->consignatario : null;
    $envio = $pedido->envio[0];
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
<div style="width:60%;margin:0 auto;border:1px solid">
<table style="width:100%;font-family:arial, sans-serif">
    <tr>
        <td style="vertical-align: middle;text-align: center">
            <h4>INVOICE / FACTURA</h4>
        </td>
    </tr>
    <tr>
</table>
<table style="width:100%;font-family:arial, sans-serif">
    <tr>
        <td>
            <div >
                <table>
                    <tr>
                        <td>
                            <b> SHIPPER (Empresa):</b>
                        </td>
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr><td style="font-size: 18px;">{{isset($detalleFactura->nombre_comercial_emisor) ? $detalleFactura->nombre_comercial_emisor : ""}}</td></tr>
                    <tr><td style="font-size:12px">{{isset($detalleFactura->direccion_matriz_emisor) ? $detalleFactura->direccion_matriz_emisor : ""}}</td></tr>
                    <tr><td style="font-size:12px">Quito - {{getPais($pedido->empresa->codigo_pais)->nombre}}</td></tr>
                    <tr><td style="font-size:12px">Teléfono: {{$pedido->empresa->telefono}}</td></tr>
                    <tr><td style="font-size:12px">Fax: {{$pedido->empresa->fax}}</td></tr>
                    <tr><td style="font-size:12px">Email: {{$pedido->empresa->correo}}</td></tr>
                </table>
                <table style="margin-top: 10px;">
                    <tr>
                        <td>
                            <b> BUYER (comprador):</b>
                        </td>
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr><td style="font-size:12px">{{isset($detalleFactura->razon_social_comprador) ? $detalleFactura->razon_social_comprador : (isset($consignatario->nombre) ? $consignatario->nombre : $cliente->nombre)}}</td></tr>
                    <tr><td style="font-size:12px">{{isset($consignatario->codigo_pais) ? getPais($consignatario->codigo_pais)->nombre : getPais($cliente->codigo_pais)->nombre}} -  {{isset($consignatario->ciudad) ? $consignatario->ciudad : $cliente->provincia }}</td></tr>
                    <tr><td style="font-size:12px">ID:{{isset($detalleFactura->identificacion_comprador) ? $detalleFactura->identificacion_comprador : (isset($consignatario->identificacion) ? $consignatario->identificacion :  $cliente->ruc)}}</td></tr>
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
                    <tr><td style="font-size:12px">{{$cliente->nombre}}</td></tr>
                    <tr><td style="font-size:12px">{{$cliente->direccion." ".$cliente->provincia}}</td></tr>
                    <tr><td style="font-size:12px">{{getPais($cliente->codigo_pais)->nombre ." ". $cliente->provincia}}</td></tr>
                    <tr><td style="font-size:12px">{{"ID: ".$cliente->ruc}}</td></tr>
                </table>
            </div>
        </td>
        <td>
            <div style="">
                <table>
                    <tr>
                        <td style="text-align: center;font-size: 16px;vertical-align: top">
                            <b> FACTURA <br />
                                No. {{isset($pedido->envios[0]->comprobante->secuencial) ? $pedido->envios[0]->comprobante->secuencial : ""}}</b>
                        </td>
                    </tr>
                    <tr> <td style="font-size: 12px">RUC: {{$pedido->empresa->ruc}}</td> </tr>
                    <tr> <td style="font-size: 12px">AUT. SRI. No: {{isset($pedido->envios[0]->comprobante) ? $pedido->envios[0]->comprobante->clave_acceso : ""}}</td></tr>
                </table>
                <table style="width: 100%;" cellspacing="0" cellpadding="0" >
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;font-size: 12px;width:60%;padding-left:5px">
                            <b>Farm Code / Código de Finca</b><br /><br />
                            {{isset($detalleFactura) ? $detalleFactura->razon_social_emisor :""}}
                        </td>
                        <td style="border: 1px solid black;font-size: 12px;padding-left:5px">
                            <b>Date / Fecha</b> <br /> <br />
                            {{isset($pedido->envios[0]->comprobante) ? \Carbon\Carbon::parse($pedido->envios[0]->comprobante->fecha_emision)->format('d-m-Y') : ""}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;font-size: 12px;padding-left:5px">
                            <b>Country Code / País</b><br /> <br />
                            {{getPais($pedido->empresa->codigo_pais)->nombre}}
                        </td>
                        <td style="border: 1px solid black;font-size: 12px;padding-left:5px">
                            <b>Invoice No.</b><br /> <br />
                            {{isset($pedido->envios[0]->comprobante->secuencial) ? $pedido->envios[0]->comprobante->secuencial : ""}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;font-size: 12px;width: 60%;padding-left:5px">
                            <b>AWB No. / Guía No.</b><br /> <br />

                            {{$pedido->envios[0]->guia_madre}}
                        </td>
                        <td style="border: 1px solid black;font-size: 12px;padding-left:5px">
                            <b>HAWB No.</b><br /> <br />
                            {{$pedido->envios[0]->guia_hija}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;font-size: 12px;width: 60%;padding-left:5px ">
                            <b>Carrier / Transportador</b><br /> <br />
                            {{$pedido->detalles[0]->agencia_carga->nombre}}
                        </td>
                        <td style="border: 1px solid black;font-size: 12px;padding-left:5px">
                            <b>Add Case No. DAE</b><br /> <br />
                            {{$pedido->envios[0]->dae}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;font-size: 12px;width: 60%;padding-left:5px ">
                            <b>Port of entry / Puerto de ent</b><br /> <br />
                            {{$cliente->puerto_entrada}}
                        </td>
                        <td style="border: 1px solid black;font-size: 12px;padding-left:5px">
                            <b>Final destination</b><br /> <br />
                            {{getPais($cliente->codigo_pais)->nombre}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;font-size: 12px;width: 60%;padding-left:5px ">
                            @if($pedido->tipo_especificacion === "N")
                                @foreach($pedido->detalles as $x => $det_ped)
                                    @php
                                        $precio = explode("|", $det_ped->precio);
                                         //$dp = getDetallePedido($det_ped->id_detalle_pedido);
                                    @endphp
                                    @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                                        @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                                            @php

                                                if($esp_emp->especificacion->tipo != "O"){
                                                    $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                                    $total_ramos += number_format(($det_ped->cantidad*$esp_emp->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad)),2,".","");
                                                }else{
                                                    $total_ramos += $det_ped->cantidad;
                                                }
                                                $peso_neto += (int)$det_esp_emp->clasificacion_ramo->nombre * number_format(($det_ped->cantidad*$det_esp_emp->cantidad),2,".","");
                                                $peso_caja += isset(explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2]) ? explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2] : 0;
                                            @endphp
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @elseif($pedido->tipo_especificacion === "T")
                                @foreach ($pedido->detalles as $x => $det_ped)
                                    @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                                        @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                                            @php
                                                $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                                $total_ramos += number_format(($det_ped->cantidad*$esp_emp->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad)),2,".","");
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
                        <td style="border: 1px solid black;font-size: 12px;padding-left:5px">
                            <b>Fecha embarque</b><br />
                            {{isset($pedido->envios[0]->comprobante) ? \Carbon\Carbon::parse($pedido->envios[0]->comprobante->fecha_emision)->addDay(1)->format('d-m-Y') : ""}}
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<table style="width:100%;font-family:arial, sans-serif;" cellpadding="0" cellspacing="0">
    <thead style="border-bottom: 1px solid;border-top: 1px solid">
    <tr >
        <th style="font-size: 11px;vertical-align: middle;border: 1px solid">
            @if($pedido->detalles[0]->cliente_especificacion->especificacion->tipo !=="O")
                Piezas
            @else
                Tallos
            @endif
        </th>
        <th style="font-size: 11px;vertical-align: middle;border: 1px solid">
            Ramos/Caja
        </th>
        <th style="font-size: 11px;vertical-align: middle;border: 1px solid">
            Total Unidades
        </th>
        <th style="font-size: 11px;vertical-align: middle;border: 1px solid">
            Descripción
        </th>
        <th style="font-size: 11px;vertical-align: middle;border: 1px solid">
            ST/BN
        </th>
        <th style="font-size: 11px;vertical-align: middle;width:70px;border: 1px solid">
            Precio
        </th>
        <th style="font-size: 11px;vertical-align: middle;border: 1px solid">
            TOTAL
        </th>
    </tr>
    </thead>
    <tbody style="border-bottom: 1px solid">
    @if($pedido->tipo_especificacion === "N")
        @foreach($pedido->detalles as $x => $det_ped)
            @php
                $precio = explode("|", $det_ped->precio);
                $i = 0;
            @endphp
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                    @php
                        $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                        if($esp_emp->especificacion->tipo != "O"){
                            $total_tallos += number_format(($det_ped->cantidad*$esp_emp->cantidad*$det_esp_emp->cantidad*$det_esp_emp->tallos_x_ramos),2,".","");
                        }else{
                            $total_tallos += number_format($det_ped->total_tallos() ,2,".","");
                        }
                        $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                        $descripcion = substr($det_esp_emp->variedad->planta->nombre, 0, 3) .", ". $det_esp_emp->variedad->nombre;
                    @endphp
                    <tr>
                        @if($n == 0)
                            <td style="font-size:11px;vertical-align:middle;text-align:center;border:1px solid" rowspan="{{$esp_emp->detalles->count()}}">
                                @if($esp_emp->especificacion->tipo != "O")
                                    {{number_format($det_ped->cantidad,2,".","")}}
                                @else
                                    {{number_format(($det_ped->total_tallos()),2,".","")}}
                                @endif
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
                            <td style="font-size:11px;vertical-align:middle;border:1px solid;padding-left: 5px"> {{isset($ramos_modificado) ? $ramos_modificado->cantidad :$det_esp_emp->cantidad}}</td>
                            <td style="font-size:11px;vertical-align:middle;border:1px solid;padding-left: 5px">
                                @if($esp_emp->especificacion->tipo != "O")
                                    {{number_format(($det_ped->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad :$det_esp_emp->cantidad)),2,".","")}}
                                @else
                                    {{number_format(($det_ped->total_tallos()),2,".","")}}
                                @endif
                            </td>
                            <td style="font-size:11px;vertical-align:middle;border:1px solid;padding-left: 5px">
                                {{$descripcion}}
                                @if(count(getDatosExportacionCliente($det_ped->id_detalle_pedido))>0)
                                        @foreach(getDatosExportacionCliente($det_ped->id_detalle_pedido) as $de)
                                             {{$de->valor." "}}
                                        @endforeach
                                @endif
                            </td>
                        <td style="font-size:11px;vertical-align:middle;border:1px solid;padding-left: 5px">
                            @if($esp_emp->especificacion->tipo != "O")
                                BN
                            @else
                                ST
                            @endif
                        </td>
                        <td style="font-size:11px;border:1px solid;padding-left: 5px"> {{"$".number_format(explode(";", $precio[$i])[0],2,".","")}} </td>
                        <td style="font-size:11px;border:1px solid;padding-left: 5px">
                            @if($esp_emp->especificacion->tipo != "O")
                                {{"$".number_format(((isset($ramos_modificado) ? $ramos_modificado->cantidad :$det_esp_emp->cantidad) * ((float)explode(";", $precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad),2,".","")}}
                            @else
                                {{number_format(($det_ped->total_tallos()*(float)explode(";", $precio[$i])[0]),2,".","")}}
                            @endif
                        </td>
                    </tr>
                    @php
                        if($esp_emp->especificacion->tipo != "O"){
                            $precio_total_sin_impuestos += ((isset($ramos_modificado) ? $ramos_modificado->cantidad :$det_esp_emp->cantidad) * (float)explode(";", $precio[$i])[0] * $esp_emp->cantidad * $det_ped->cantidad);
                        }else{
                            $precio_total_sin_impuestos += $det_ped->total_tallos()*(float)explode(";", $precio[$i])[0];
                        }
                    @endphp
                    @php  $i++;  @endphp
                @endforeach
            @endforeach
        @endforeach
    @elseif($pedido->tipo_especificacion === "T")
        @php $data_body_table=[]; @endphp
        @foreach ($pedido->detalles as $x => $det_ped)
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                @php
                    foreach($esp_emp->detalles as $det_esp_emp)
                        $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                        $total_tallos += number_format(($det_ped->cantidad*$esp_emp->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad :$det_esp_emp->cantidad)*$det_esp_emp->tallos_x_ramos),2,".","");
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
                {{--@foreach ($esp_emp->detalles as $n => $det_esp_emp)

                @endforeach--}}
            @endforeach
            @foreach($det_ped->coloraciones as $y => $coloracion)
                @foreach($coloracion->marcaciones_coloraciones as $m_c)
                    @if($m_c->cantidad > 0)
                        @if($m_c->precio=!"")
                            @php
                                $precio =$m_c->precio;
                            @endphp
                        @elseif($coloracion->precio=="")
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
                        <tr>
                            <td style="font-size:12px;border:1px solid;padding-left: 5px">{{number_format(($m_c->cantidad/$m_c->detalle_especificacionempaque->cantidad),2,".","")}}</td>
                            <td style="font-size:12px;border:1px solid;padding-left: 5px"> {{$m_c->detalle_especificacionempaque->cantidad}} </td>
                            <td style="font-size:11px;vertical-align:middle;border:1px solid;padding-left: 5px">{{$m_c->cantidad}}</td>
                            <td style="font-size:12px;border:1px solid;padding-left: 5px">
                                {{substr($m_c->detalle_especificacionempaque->variedad->planta->nombre, 0, 3) .", ". $m_c->detalle_especificacionempaque->variedad->nombre." ". ($m_c->marcacion->nombre == 1 ? "" : $m_c->marcacion->nombre) ." - ". $m_c->coloracion->color->nombre}}
                                @if(count(getDatosExportacionCliente($det_ped->id_detalle_pedido))>0)
                                    @foreach(getDatosExportacionCliente($det_ped->id_detalle_pedido) as $de)
                                        {{$de->valor." "}}
                                    @endforeach
                                @endif
                            </td>
                            <td style="font-size:12px;border:1px solid;padding-left: 5px">BOUNCHE</td>
                            <td style="font-size:12px;border:1px solid;padding-left: 5px">${{number_format($precio,2,".","")}}</td>
                            <td style="font-size:12px;borer:1px solid;padding-left: 5px;border:1px solid;padding-left: 5px">${{number_format(($m_c->cantidad*$precio),2,".","")}}</td>
                        </tr>
                        {{--@foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                            @foreach ($esp_emp->detalles as $n => $det_esp_emp)
                                @if($det_esp_emp->id_detalle_especificacionempaque === $m_c->id_detalle_especificacionempaque)
                                    @php
                                        $frac_piezas = $m_c->cantidad/$det_esp_emp->cantidad;
                                        $descripcion = substr($det_esp_emp->variedad->planta->nombre, 0, 3) .", ". $det_esp_emp->variedad->nombre;
                                        $pieza += $frac_piezas;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach--}}
                    @endif
                @endforeach
            @endforeach
        @endforeach
    @endif
    </tbody>
</table>
<table style="width: 100%">
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{number_format($total_ramos,2,".",",")}}</b> </td>
        <td style="font-size:11px;font-family: arial, sans-serif;width:300px"><b>TOTAL BN</b></td>
        <td style="font-size:11px;font-family: arial, sans-serif;text_align:right;text-align:right">SUBTOTAL : ${{number_format($precio_total_sin_impuestos,2,".","")}}</td>
    </tr>
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{number_format($total_tallos,2,".","")}}</b> </td>
        <td style="font-size:11px;font-family: arial, sans-serif;width:300px"><b>TOTAL STEMS</b></td>
        @php $tipoImpuesto = getTipoImpuesto($pedido->cliente->detalle()->codigo_impuesto, $pedido->cliente->detalle()->codigo_porcentaje_impuesto); @endphp
        <td style="font-size:11px;font-family: arial, sans-serif;text-align:right">{{$tipoImpuesto->nombre}} : ${{is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_total_sin_impuestos * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00"}}</td>
    </tr>
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{number_format($total_piezas,2,".","")}}</b> </td>
        <td style="font-size:11px;font-family: arial, sans-serif;width:300px"><b>TOTAL PIECES / TOTAL PIEZAS</b></td>
        @php $tipoImpuesto = $tipoImpuesto = getTipoImpuesto($pedido->cliente->detalle()->codigo_impuesto, $pedido->cliente->detalle()->codigo_porcentaje_impuesto); @endphp
        <td style="font-size:11px;font-family: arial, sans-serif;text-align:right">TOTAL : ${{is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_total_sin_impuestos + ($precio_total_sin_impuestos * ($tipoImpuesto->porcentaje / 100)), 2, ".", "") : number_format($precio_total_sin_impuestos, 2, ".", "")}}</td>
    </tr>
    <tr>
        <td style="font-size:11px;font-family: arial, sans-serif;width:50px"> <b>{{$full_equivalente_real}}</b> </td>
        <td colspan="2" style="font-size:11px;font-family: arial, sans-serif"><b>TOTAL FULL BOXES EQUIVALENT / TOTAL CAJAS </b></td>
    </tr>
</table>
<table style="width:100%">
    <tr>
        <td style="font-size:12px;font-family: arial, sans-serif;border: 1px solid;padding-left: 5px"> <b> FULL BOXES : [{{number_format($full,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif;border: 1px solid;padding-left: 5px"> <b> HALF BOXES : [{{number_format($half,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif;border: 1px solid;padding-left: 5px"> <b> 1/4 BOXES : [{{number_format($cuarto,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif;border: 1px solid;padding-left: 5px"> <b> 1/6 BOXES : [{{number_format($sexto,2,".","")}}]</b></td>
        <td style="font-size:12px;font-family: arial, sans-serif;border: 1px solid;padding-left: 5px"> <b> 1/8 BOXES : [{{number_format($octavo,2,".","")}}]</b></td>
    </tr>
</table>
<table style="width: 100%;margin-top:30px">
    <tr>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 11px">
            <img src="/images/firma_FABIOLA_SIERRA.jpg">
            <hr style="width: 60%;margin: 0 auto"/>
            FIRMA
        </td>
        <td style="text-align: center;vertical-align: bottom;font-family:arial, sans-serif;font-size: 12px;width:50%">
            <label >MARKETING NAME</label>
            <div style="border: 1px solid;height: 20px"><b>{{strtoupper($cliente->marca_caja->nombre)}}</b></div>
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
            <div style="border: 1px solid;height: 20px;font-size: 12px"> <b>{{strtoupper(getAgenciaCarga($pedido->detalles[0]->id_agencia_carga)->nombre)}}</b></div>
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
</div>
