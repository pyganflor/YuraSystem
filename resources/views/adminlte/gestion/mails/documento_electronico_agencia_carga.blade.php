<p>Dear {{$comprobante->envio->pedido->detalles[0]->agencia_carga->nombre}}:</p>

<p>We would like to confirm your shipment of total {{$comprobante->envio->pedido->getCajasFull()}} full boxes, that will leave with the following details:</p>

<p>Ship date: {{$comprobante->fecha_integrado}}</p>

<p>AWB No.: {{$comprobante->envio->guia_madre}}</p>

<p>Invoice number: {{$comprobante->secuencial}}</p>

<p>Amount: $</p>

<table cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th style="background: silver;border: 1px solid;text-align: center;padding: 1px 3px">PIECES</th>
        <th style="background: silver;border: 1px solid;text-align: center;padding: 1px 3px">UNITS PER BOX</th>
        <th style="background: silver;border: 1px solid;text-align: center;padding: 1px 3px">TOTAL UNITS</th>
        <th style="background: silver;border: 1px solid;text-align: center;padding: 1px 3px">BOXES CONTENT</th>
    </tr>
    </thead>
    <tbody>
    @php $totalPiezas = 0; $totalRamos =0; @endphp
    @if($comprobante->envio->pedido->tipo_especificacion === "N")
        @foreach($comprobante->envio->pedido->detalles as $x => $det_ped)
            @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                @php
                    $rXc = 0;
                    $description = "";
                    foreach ($esp_emp->detalles as $n => $det_esp_emp){
                        $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                        $description .= substr($det_esp_emp->variedad->planta->nombre,0,3)." ".$det_esp_emp->variedad->siglas." ". $det_esp_emp->clasificacion_ramo->nombre.", ";
                        $rXc += (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad);
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid; text-align: center" >{{number_format($det_ped->cantidad,2,".","")}}</td>
                    <td style="border: 1px solid; text-align: center">{{number_format($rXc,2,".","")}} </td>
                    <td style="border: 1px solid; text-align: center">{{number_format(($rXc*$det_ped->cantidad),2,".","")}}</td>
                    <td style="border: 1px solid; text-align: center">
                        {{substr($description,0,-2)}}
                        @foreach(getDatosExportacionCliente($det_ped->id_detalle_pedido) as $dato_exp)
                            {{$dato_exp->valor}}
                        @endforeach
                    </td>
                </tr>
                @php $totalPiezas+= $det_ped->cantidad; $totalRamos += ($rXc*$det_ped->cantidad); @endphp
            @endforeach
        @endforeach
    @elseif($comprobante->envio->pedido->tipo_especificacion === "T")
        @php $data_body_table=[] @endphp
        @foreach ($comprobante->envio->pedido->detalles as $x => $det_ped)
            @foreach($det_ped->coloraciones as $y => $coloracion)
                @foreach($coloracion->marcaciones_coloraciones as $m_c)
                    @php
                        if($m_c->cantidad > 0){
                            if($m_c->precio!=''){
                                $precio= $m_c->precio;
                            }else if($coloracion->precio==""){
                                foreach (explode("|", $det_ped->precio) as $p)
                                    if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                            $precio = explode(";",$p)[0];
                            }else{
                                foreach(explode("|",$coloracion->precio) as $p)
                                        if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                            $precio = explode(";",$p)[0];
                            }
                            $data_body_table[$m_c->detalle_especificacionempaque->variedad->planta->id_planta][$m_c->detalle_especificacionempaque->variedad->id_variedad][(String)$precio][]=[
                                'ramos' => $m_c->cantidad,
                                'descripcion' =>substr($m_c->detalle_especificacionempaque->variedad->planta->nombre, 0, 3) .", ". $m_c->detalle_especificacionempaque->variedad->siglas." ".$m_c->detalle_especificacionempaque->clasificacion_ramo->nombre,
                                'piezas'=> number_format(($m_c->cantidad/$m_c->detalle_especificacionempaque->cantidad),2,".",""),
                                'color' => $m_c->coloracion->color->nombre
                            ];
                        }
                    @endphp
                @endforeach
            @endforeach
        @endforeach
        @foreach($data_body_table as $body_table)
            @foreach($body_table as $table)
                @foreach($table as $t)
                    @php
                        $pie=0; //Sumatoria Piezas
                        $ram=0; //Sumatoria Ramos
                    @endphp
                    @foreach($t as $ta)
                        @if($ta['ramos'] > 0)
                            <tr>
                                <td style="border: 1px solid; text-align: center" >{{number_format($ta['piezas'],2,".","")}}</td>
                                <td style="border: 1px solid; text-align: center">{{$ta['ramos']}}</td>
                                <td style="border: 1px solid; text-align: center">{{number_format(($ta['piezas']*$ta['ramos']),2,".","")}}</td>
                                <td style="border: 1px solid; text-align: center">
                                    {{$ta['descripcion']}}  {{$ta['color']}}
                                </td>
                            </tr>
                        @endif
                        @php
                            $totalPiezas +=$ta['piezas'];
                            $totalRamos +=($ta['piezas']*$ta['ramos']);
                        @endphp
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    @endif
    <tr>
        <td style="border: 1px solid;background: silver;text-align: center">{{number_format($totalPiezas,2,".","")}}</td>
        <td style="border: 1px solid;background: silver"></td>
        <td style="border: 1px solid;background: silver;text-align: center">{{number_format($totalRamos,2,".","")}}</td>
        <td style="border: 1px solid;background: silver"></td>
    </tr>
    </tbody>
</table>

<p>NOTE: Our bunches may vary in weight +/- 5%</p>

<p>
    Best Regards,<br />
    {{$comprobante->envio->pedido->empresa->nombre}}
</p>

<p>
    Jefe de Ventas,<br />
    Fabiola Sierra<br />
    0994991048
</p>
