@php
    $total_piezas = 0;
    $ramos_estandar = 0;
    $full = 0;
    $half = 0;
    $cuarto = 0;
    $sexto = 0;
    $octavo = 0;
    $full_equivalente_real = 0;
@endphp
<table style="width:100%;font-family: arial, sans-serif;border-collapse: collapse;">
    <tr>
        <td style="vertical-align: middle;text-align: center">
            <h4 style="color: #464646">
                @if(!$vista_despacho)
                    PACKING LIST / LISTA DE EMPAQUE
                @else
                    ORDEN DE EMPAQUE
                @endif
                @if(!$vista_despacho)
                    {{isset($pedido->envios[0]->comprobante->clave_acceso) ? "# 001-".getDetallesClaveAcceso($pedido->envios[0]->comprobante->clave_acceso, 'PUNTO_ACCESO')."-".getDetallesClaveAcceso($pedido->envios[0]->comprobante->clave_acceso, 'SECUENCIAL') : null}}
                @endif
            </h4>
        </td>
    </tr>
    <tr>
</table>
<table style="width:100%;font-family: arial, sans-serif;border-collapse: collapse;">
    <tr>
        <td>
            <div style="width: 300px;">
                @if(!$vista_despacho)
                    <table>
                        <tr>
                            <td>
                                <b>  SHIPPER (Empresa):</b>
                            </td>
                        </tr>
                    </table>
                    <table style="border: 1px solid black;width: 100%">
                        <tr><td>{{$empresa->razon_social}}</td></tr>
                        <tr><td style="font-size:13px">{{$empresa->direccion_matriz}}</td></tr>
                        <tr><td style="font-size:13px">Quito - {{getPais($empresa->codigo_pais)->nombre}}</td></tr>
                        <tr><td style="font-size:13px">Teléfono: {{$empresa->telefono}}</td></tr>
                        <tr><td style="font-size:13px">Fax: {{$empresa->fax}}</td></tr>
                        <tr><td style="font-size:13px">Email: {{$empresa->correo}}</td></tr>
                    </table>
                @endif
                <table style="margin-top: 10px;">
                    <tr>
                        <td>
                           <b> BUYER (comprador):</b>
                        </td>
                    </tr>
                </table>
                <table style="border: 1px solid black;width: 100%">
                    <tr><td style="font-size:13px">{{$cliente['nombre']}}</td></tr>
                    <tr><td style="font-size:13px">{{$cliente['direccion']." ".$cliente['provincia']}}</td></tr>
                    <tr><td style="font-size:13px">{{$cliente['pais']}} </td></tr>
                    <tr><td style="font-size:13px">{{$cliente['tipo_identificacion'] == "IDENTIFICACIÓN DEL EXTERIOR" ? "ID - EXT" : $cliente['tipo_identificacion']}} : {{$cliente['identificacion']}}</td></tr>
                    <tr><td style="font-size:13px">Tlf: {{$cliente['telefono']}}</td></tr>
                </table>
            </div>
        </td>
        @if(!$vista_despacho)
            <td>
                <div style="width:300px">
                    <table style="width: 100%;"  >
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 13px;width: 60% ">
                            <b>Farm Code / Código de Finca</b><br />
                            {{$empresa->razon_social}}
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 13px">
                            <b>Date / Fecha< br /></b> <br />
                            {{isset($despacho->fecha_despacho) ? $despacho->fecha_despacho : "No despachado" }}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 13px;width: 60% ">
                            <b>AWB No. / Guía No.</b><br />
                            {{$pedido->envios[0]->guia_madre}}
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 13px">
                            <b>HAWB No.</b><br />
                            {{$pedido->envios[0]->guia_hija}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border: 1px solid black;padding: 0;font-size: 13px;width: 60% ">
                            @if($pedido->tipo_especificacion === "N")
                                <b>Carrier / Transportador</b><br />
                                {{isset($pedido->envios[0]->detalles[0]->id_aerolinea) ? getAgenciaTransporte($pedido->envios[0]->detalles[0]->id_aerolinea)->nombre : null}}
                            @elseif($pedido->tipo_especificacion === "T")
                                <b>INOVICE</b><br />
                                {{ isset($pedido->envios[0]->comprobante) ? getDetallesClaveAcceso($pedido->envios[0]->comprobante->clave_acceso,'SERIE').getDetallesClaveAcceso($pedido->envios[0]->comprobante->clave_acceso,'SECUENCIAL') : ""}}
                            @endif
                        </td>
                        <td style="border: 1px solid black;padding: 0;font-size: 13px">
                            OE {{$cliente['dae']}}
                        </td>
                    </tr>
                </table>
                </div>
            </td>
        @endif
    </tr>
</table>
@if($pedido->tipo_especificacion === "N")
    <table style="width:100%;font-family: arial, sans-serif;border-collapse: collapse;" >
        <thead style="border: 1px solid black" >
            <tr>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px;border:1px solid black" > PIECES<br />Piezas</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px;border:1px solid black" >UNITIS / BOX<br />Ramos x Caja</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px;border:1px solid black" > ST / BN</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px;border:1px solid black" >TOTAL UNITS <br />Total Unidades</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px;border:1px solid black" >DETALLE POR CAJA / BOXES CONTENT</td>
            </tr>
        </thead>
        <tbody style="border: 1px solid black">
            @foreach($pedido->detalles as $x => $det_ped)
                @php $dp = getDetallePedido($det_ped->id_detalle_pedido); @endphp
                    @foreach ($dp->cliente_especificacion->especificacion->especificacionesEmpaque as $y => $esp_emp)
                        @php
                            $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$dp->cantidad;
                                    switch (explode("|",$esp_emp->empaque->nombre)[1]) {
                                        case '1':
                                            $full += $dp->cantidad;
                                            break;
                                        case '0.5':
                                            $half += $dp->cantidad;
                                            break;
                                        case '0.25':
                                            $cuarto +=$dp->cantidad;
                                            break;
                                        case '0.17':
                                            $sexto +=$dp->cantidad;
                                            break;
                                        case '0.125':
                                            $octavo +=$dp->cantidad;
                                            break;
                                    }
                        @endphp
                        @foreach($esp_emp->detalles as $z => $det_esp_emp)
                            @php
                                $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                $dato_exportacion = "";
                                foreach($pedido->cliente->cliente_datoexportacion as $cde){
                                    $valor = isset(getDatosExportacion($det_ped->id_detalle_pedido, $cde->datos_exportacion->id_dato_exportacion)->valor) ? getDatosExportacion($det_ped->id_detalle_pedido, $cde->datos_exportacion->id_dato_exportacion)->valor : "";
                                    $dato_exportacion.= "  ".$valor." ";
                                }
                            @endphp
                            <tr>
                                @if($y == 0 && $z == 0)
                                    <td style="padding-left: 5px;font-size:13px;{{($y == 0 && $z == 0) ? "border:2px solid black ":"border:1px solid black"}}"
                                        rowspan="{{getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)}}">
                                        {{$det_ped->cantidad}}
                                        @php
                                            $total_piezas += $det_ped->cantidad
                                        @endphp
                                    </td>
                                @endif
                                <td style="padding-left: 5px;font-size:13px;
                                        {{($y == 0 && $z == 0) ? "border-top:2px solid black":"border:1px solid black"}}
                                        {{(($z+1) == getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)) ? ";border-bottom:2px solid black" : ";border-bottom:1px solid black"}}" >
                                    {{isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad}}
                                </td>
                                <td style="padding-left: 5px;font-size:13px;
                                    {{($y == 0 && $z == 0) ? "border-top:2px solid black;border-left:1px solid black;border-right:1px solid black ":"border:1px solid black"}}
                                    {{(($z+1) == getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)) ? ";border-bottom:2px solid black" : ";border-bottom:1px solid black"}}" >
                                    B/N
                                </td>
                                <td style="padding-left: 5px;font-size:13px;
                                    {{($y == 0 && $z == 0) ? "border-top:2px solid black;border-left:1px solid black;border-right:1px solid black ":"border:1px solid black"}}
                                    {{(($z+1) == getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)) ? ";border-bottom:2px solid black" : ";border-bottom:1px solid black"}}" >
                                    {{$det_ped->cantidad * (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad}}</td>
                                <td style="padding-left: 5px;font-size:13px;
                                    {{($y == 0 && $z == 0) ? "border-top:2px solid black;border-left:1px solid black;border-right:1px solid black ":"border:1px solid black"}}
                                    {{(($z+1) == getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion)) ? ";border-bottom:2px solid black" : ";border-bottom:1px solid black"}}" >
                                    {{getVariedad($det_esp_emp->id_variedad)->siglas." ".getClasificacionRamo($det_esp_emp->id_clasificacion_ramo)->nombre.getClasificacionRamo($det_esp_emp->id_clasificacion_ramo)->unidad_medida->siglas. " " . $dato_exportacion}}</td>
                            </tr>
                        @endforeach
                    @endforeach
            @endforeach
        </tbody>
    </table>
@elseif($pedido->tipo_especificacion === "T")
    @php $env = getEnvio($pedido->envios[0]->id_envio); $data = []; @endphp
    <table style="width:100%;font-family: arial, sans-serif;border-collapse: collapse;" >
        <thead style="border: 1px solid black" >
            <tr>
                <td style="padding-left: 5px;border: 1px solid black;font-size:12px" > DESCRIPCIÓN <br />
                    {{substr($env->pedido->detalles[0]->cliente_especificacion->especificacion->especificacionesEmpaque[0]->detalles[0]->variedad->planta->nombre,0,3)}}
                </td>
                <td style="padding-left: 5px;font-size:12px;border: 1px solid black">BOUNCHES <br /> BOX</td>
                <td style="padding-left: 5px;font-size:12px;border: 1px solid black"> INITIAL <br /> BOX</td>
                <td style="padding-left: 5px;font-size:12px;border: 1px solid black">FINAL <br /> BOX</td>
                <td style="padding-left: 5px;font-size:12px;border: 1px solid black">TOTAL <br /> BOXES</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:12px;width: 250px;">COLOR</td>
            </tr>
        </thead>
        <tbody style="border: 1px solid black">
            @foreach($pedido->detalles as $x => $det_ped)
                @php $dp = getDetallePedido($det_ped->id_detalle_pedido); @endphp
                @foreach ($dp->cliente_especificacion->especificacion->especificacionesEmpaque as $y => $esp_emp)
                    @php
                        $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$dp->cantidad;
                                switch (explode("|",$esp_emp->empaque->nombre)[1]) {
                                    case '1':
                                        $full += $dp->cantidad;
                                        break;
                                    case '0.5':
                                        $half += $dp->cantidad;
                                        break;
                                    case '0.25':
                                        $cuarto +=$dp->cantidad;
                                        break;
                                    case '0.17':
                                        $sexto +=$dp->cantidad;
                                        break;
                                    case '0.125':
                                        $octavo +=$dp->cantidad;
                                        break;
                                }
                    @endphp
                @endforeach
            @endforeach
            @foreach($env->pedido->pedidoMarcacionesOrderAsc as $w => $distribucion)
                <tr>
                    <td style="font-size:12px;border:1px solid black">{{$distribucion->nombre}}</td>
                    <td style=";font-size:12px;border:1px solid black">{{$distribucion->ramos}}</td>
                    <td style="font-size:12px;border:1px solid black">{{$distribucion->pos_pieza}}</td>
                    <td style="font-size:12px;border:1px solid black">
                        @if ($distribucion->piezas === 1 )
                            {{$distribucion->pos_pieza}}
                        @else
                            {{($distribucion->pos_pieza-1)+$distribucion->piezas}}
                        @endif
                    </td>
                    <td style="font-size:12px;border:1px solid black">
                        {{$distribucion->piezas}}
                        @php
                            $total_piezas += $distribucion->piezas
                        @endphp
                    </td>
                    <td style="font-size:12px;border:1px solid black">
                        @php $dist_col = json_decode($distribucion->dist_col) @endphp
                        @foreach($dist_col as $distCol)
                            @foreach($distCol as $dc)
                                @if($dc->cantidad>0)
                                    {{$dc->cantidad}} {{$dc->variedad}} {{$dc->color}},
                                @endif
                            @endforeach
                        @endforeach
                        {{--@foreach (getDistribucion($distribucion->id_distribucion)->distribuciones_coloraciones as $z => $distribucion_coloracion)
                            @if($distribucion_coloracion->cantidad > 0)
                                {{ $distribucion_coloracion->marcacion_coloracion->detalle_especificacionempaque->variedad->siglas." ".$distribucion_coloracion->cantidad ." ". $distribucion_coloracion->marcacion_coloracion->coloracion->color->nombre. ","}}
                            @endif
                        @endforeach--}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
<table style="width:100%;margin-top: 20px;font-family: arial, sans-serif">
    <tr>
        <td style="font-size:15px" colspan="2">
            {{$total_piezas}}  TOTAL PIECES / TOTAL PIEZAS TOTAL UNITS
        </td>
    </tr>
    <tr>
        <td colspan="2">
            {{round($full_equivalente_real,2)}} TOTAL FULL BOXES EQUIVALENT / TOTAL CAJAS
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <table style="width: 100%;margin-top: 15px;font-size: 13px">
                <tr>
                    <td>FULL BOXES : [{{number_format($full,2,".","")}}]</td>
                    <td>HALF BOXES : [{{number_format($half,2,".","")}}]</td>
                    <td>1/4 BOXES : [{{number_format($cuarto,2,".","")}}]</td>
                    <td>1/6 BOXES : [{{number_format($sexto,2,".","")}}]</td>
                    <td>1/8 BOXES : [{{number_format($octavo,2,".","")}}]</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@if(!$vista_despacho)
<table style="width:100%;font-family: arial, sans-serif">
    <tr>
        <td style="text-align: center;width: 50%;vertical-align: bottom">
            <div>
                FIRMA
                <div style="height:20px ;border: 1px solid black; ">
                    {{isset($despacho->resp_ofi_despacho) ? isset($despacho->resp_ofi_despacho) : ""}}
                </div>
                <label  style="font-size: 14px;padding: 15px 0">NOMBRE PERSONA DESPACHO</label>
            </div>
        </td>
        <td style="text-align: center;width: 50%;vertical-align: bottom;">
            <div style="margin-top:60px">
                <div style="border: 1px solid black;height:20px ">
                    {{getConfiguracionEmpresa()->razon_social}}
                </div>
                <label style="font-size: 14px;padding: 15px 0">MARKETIN NAME / MARCA CAJA</label>
            </div>
            <div>
                <div style="height:20px ;border: 1px solid black; ">
                    {{getAgenciaCarga($pedido->detalles[0]->id_agencia_carga)->nombre}}
                </div>
                <label style="font-size: 14px;padding: 15px 0">AGENCIA DE CARGA / FREIGHT FORWARDER</label>
            </div>
        </td>
    </tr>
</table>
@endif
