<table style="width:100%">
    <tr>
        <td style="vertical-align: middle;text-align: center">
            <h4 style="color: #464646">PACKING LIST / LISTA DE EMPAQUE #{{isset($comprobante->clave_acceso) ? "001-".getDetallesClaveAcceso($comprobante->clave_acceso, 'PUNTO_ACCESO')."-".getDetallesClaveAcceso($comprobante->clave_acceso, 'SECUENCIAL') : null}}</h4>
        </td>
    </tr>
    <tr>
</table>
<table style="width:100%">
    <tr>
        <td>
            <div style="width: 300px;">
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
                <tr><td style="font-size:13px">{{$cliente['pais']}} - {{$cliente['tipo_identificacion']}} : {{$cliente['identificacion']}}</td></tr>
                <tr><td style="font-size:13px">{{$cliente['telefono']}}</td></tr>
            </table>
            </div>
        </td>
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
                        {{isset($despacho->fecha_despacho) ? $despacho->fecha_despacho : null }}
                    </td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;padding: 0;font-size: 13px;width: 60% ">
                        <b>AWB No. / Guía No.</b><br />
                        {{$envio['guia_madre']}}
                    </td>
                    <td style="border: 1px solid black;padding: 0;font-size: 13px">
                        <b>HAWB No.</b><br />
                        {{$envio['guia_hija']}}
                    </td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;padding: 0;font-size: 13px;width: 60% ">
                        <b>Carrier / Transportador</b><br />
                        {{$envio['aerolinea']}}
                    </td>
                    <td style="border: 1px solid black;padding: 0;font-size: 13px">
                        <b>ADD CASE No</b><br />
                        OE {{$cliente['dae']}}
                    </td>
                </tr>
            </table>
            </div>
        </td>
    </tr>
</table>
@if($pedido->tipo_especificacion==="N")
    <table style="width:100%" >
        <thead style="border: 1px solid black" >
            <tr>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px" > PIECES<br />Piezas</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px" >UNITIS / BOX<br />Por Caja</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px" > ST / BN</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px" >TOTAL UNITS <br />Total Unidades</td>
                <td style="padding-left: 5px;border: 1px solid black;font-size:13px" >DETALLE POR CAJA / BOXES CONTENT</td>
            </tr>
        </thead>
        <tbody style="border: 1px solid black">
            @php
                $total_piezas = 0;
                $ramos_estandar = 0;
            @endphp
            @foreach($detallePedido as $det_ped)
                @php $re = convertToEstandar($det_ped['ramos_x_caja'],$det_ped['calibre']) @endphp
                <tr>
                    <td style="padding-left: 5px;font-size:13px" >
                        {{$det_ped['piezas']}}
                        @php
                            $ramos_estandar += $re;
                            $total_piezas += $det_ped['piezas']
                        @endphp
                    </td>
                    <td style="padding-left: 5px;font-size:13px" >{{$det_ped['ramos_x_caja']}}</td>
                    <td style="padding-left: 5px;font-size:13px" >B/N</td>
                    <td style="padding-left: 5px;font-size:13px" >{{$det_ped['ramos_totales']}}</td>
                    <td style="padding-left: 5px;font-size:13px" >{{$det_ped['presentacion']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($pedido->tipo_especificacion==="T")
    <table style="width:100%">
        <tr>
            <td>

            </td>
        </tr>
        <tr>
            <td>

            </td>
        </tr>
        <tr>
            <td>

            </td>
        </tr>
    </table>
@endif
<table style="width:100%;margin-top: 20px;">
    <tr>
        <td style="font-size:15px" colspan="2">
            {{$total_piezas}}  TOTAL PIECES / TOTAL PIEZAS TOTAL UNITS
        </td>
    </tr>
    <tr>
        <td colspan="2">
            {{$ramos_estandar / getConfiguracionEmpresa()->ramos_x_caja}} TOTAL FULL BOXES EQUIVALENT / TOTAL CAJAS
        </td>
    </tr>
    <tr>
        <td style="text-align: center;width: 50%;vertical-align: bottom">
            <div>
                FIRMA
                <div style="height:20px ;border: 1px solid black; ">
                    {{isset($despacho->resp_ofi_despacho) ? isset($despacho->resp_ofi_despacho) : ""}}
                </div>
                <label  style="font-size: 14px">NOMBRE PERSONA DESPACHO</label>
            </div>
        </td>
        <td style="text-align: center;width: 50%;vertical-align: bottom;">
            <div style="margin-top:60px">
                <div style="border: 1px solid black;height:20px ">
                    {{getConfiguracionEmpresa()->razon_social}}
                </div>
                <label style="font-size: 14px">MARKETIN NAME / MARCA CAJA</label>
            </div>
            <div>
                <div style="height:20px ;border: 1px solid black; ">
                    {{getAgenciaCarga($detallePedido[0]['id_agencia_carga'])->nombre}}
                </div>
                <label style="font-size: 14px">AGENCIA DE CARGA / FREIGHT FORWARDER</label>
            </div>
        </td>
    </tr>
</table>
