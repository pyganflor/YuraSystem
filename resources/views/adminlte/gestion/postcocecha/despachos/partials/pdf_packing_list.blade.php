<table style="width:100%">
    <tr>
        <td style="vertical-align: middle;text-align: center">
            <h4 style="color: #464646">PACKING LIST / LISTA DE EMPAQUE #{{"001-".getDetallesClaveAcceso($comprobante->clave_acceso, 'PUNTO_ACCESO')."-".getDetallesClaveAcceso($comprobante->clave_acceso, 'SECUENCIAL')}}</h4>
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
                        {{$despacho->fecha_despacho}}
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
                <td style="border: 1px solid black;font-size:13px" > PIECES<br />Piezas</td>
                <td style="border: 1px solid black;font-size:13px" >UNITIS / BOX<br />Por Caja</td>
                <td style="border: 1px solid black;font-size:13px" > ST / BN</td>
                <td style="border: 1px solid black;font-size:13px" >TOTAL UNITS <br />Total Unidades</td>
                <td style="border: 1px solid black;font-size:13px" >DETALLE POR CAJA / BOXES CONTENT</td>
            </tr>
        </thead>
        <tbody style="border: 1px solid black">
            @foreach($detallePedido as $det_ped)
                <tr>
                    <td style="border: 1px solid black;font-size:13px" >{{$det_ped['piezas']}}</td>
                    <td style="border: 1px solid black;font-size:13px" >{{$det_ped['ramos_x_caja']}}</td>
                    <td style="border: 1px solid black;font-size:13px" >B/N</td>
                    <td style="border: 1px solid black;font-size:13px" >{{$det_ped['ramos_totales']}}</td>
                    <td style="border: 1px solid black;font-size:13px" >{{$det_ped['presentacion']}}</td>
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
    </table>
@endif
