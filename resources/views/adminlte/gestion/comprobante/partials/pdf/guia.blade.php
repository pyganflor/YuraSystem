<table>
    <tr>
        <td style="width: 350px;">
            <table>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;text-align:center;{{!isset($data['pedido']->envios[0]->comprobante->empresa->imagen) ? "padding:90px 110px" : ""}}">
                        @if(isset($data['pedido']->envios[0]->comprobante->empresa->imagen))
                            <img src="{{public_path('imagen')."/".$pedido->envios[0]->comprobante->empresa->imagen}}" style="width:320px;height: 200px">
                        @else
                            NO TIENE LOGO
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;">
                        <table>
                            <tr>
                                <td style="font-size:12px;padding:5px">{{(String)$data['obj_xml']->infoTributaria->nombreComercial}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:12px;padding:5px">DIRECCIÓN MATRIZ: {{(String)$data['obj_xml']->infoTributaria->dirMatriz}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:12px;padding:5px">DIRECCIÓN SUCURSAL: {{(String)$data['obj_xml']->infoGuiaRemision->dirEstablecimiento}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:12px;padding:5px">OBLIGADO A LLEVAR CONTABILIDAD: {{(String)$data['obj_xml']->infoGuiaRemision->obligadoContabilidad}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid black;border-radius:5px;width:350px">
            <table>
                <tr>
                    <td style="font-size:12px;padding:5px">R.U.C.: {{(String)$data['obj_xml']->infoTributaria->ruc}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">GUÍA DE REMISIÓN</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">No. {{"001-".getDetallesClaveAcceso((String)$data['obj_xml']->infoTributaria->claveAcceso, 'PUNTO_ACCESO')."-".getDetallesClaveAcceso((String)$data['obj_xml']->infoTributaria->claveAcceso, 'SECUENCIAL')}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">NÚMERO DE AUTORIZACIÓN</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">
                        @if($data['img_clave_acceso'] != null)
                            {{(String)$data['obj_xml']->infoTributaria->claveAcceso}}
                        @else
                            SIN VALOR TRIBUTARIO
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">FECHA Y HORA <br/>DE AUTORIZACIÓN: {{(String)$data['autorizacion']->fechaAutorizacion}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">AMBIENTE: {{(String)$data['autorizacion']->ambiente}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">EMISIÓN: {{(String)$data['obj_xml']->infoTributaria->tipoEmision == 1 ? "NORMAL" : "No se encontró tipo de emisión"}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;padding:5px">CLAVE DE ACCESO</td>
                </tr>
                <tr>
                    <td>
                        @if($data['img_clave_acceso'] != null)
                            <img src="data:image/png;base64,{{$data['img_clave_acceso']}}" style="width: 350px;height: 50px"/>
                        @else
                            SIN VALOR TRIBUTARIO
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <table>
        <tr>
            <td style="border:1px solid black;border-radius:5px;width:720px">
                <table>
                    <tr>
                        <td style="font-size:12px;padding:5px">Idetificación (Transportista): {{(String)$data['obj_xml']->infoGuiaRemision->rucTransportista}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Razon social / Nombre y apellidos: {{(String)$data['obj_xml']->infoGuiaRemision->razonSocialTransportista}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Placa: {{(String)$data['obj_xml']->infoGuiaRemision->placa}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Puerto de partida: {{(String)$data['obj_xml']->infoGuiaRemision->dirPartida}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Fecha inicio transporte: {{(String)$data['obj_xml']->infoGuiaRemision->fechaIniTransporte}}</td>
                        <td style="font-size:12px;padding:5px">Fecha fin transporte: {{(String)$data['obj_xml']->infoGuiaRemision->fechaFinTransporte}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border:1px solid black;border-radius:5px;width:720px">
                <table>
                    <tr>
                        <td style="font-size:12px;padding:5px;width:240px">Comprobante de venta: </td>
                        <td style="font-size:12px;padding:5px;width:200px">Factura:</td>
                        <td style="font-size:12px;padding:5px;width:200px">{{(String)$data['obj_xml']->destinatarios->destinatario->numDocSustento}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px;" >  Número de Autorización: </td>
                        <td  style="font-size:12px;padding:5px;" colspan="2"> {{(String)$data['obj_xml']->destinatarios->destinatario->numAutDocSustento}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px;width:240px">Motivo traslado: </td>
                        <td style="font-size:12px;padding:5px" colspan="2" >{{(String)$data['obj_xml']->destinatarios->destinatario->motivoTraslado}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Destino:</td>
                        <td style="font-size:12px;padding:5px" colspan="2"> {{strtoupper((String)$data['obj_xml']->destinatarios->destinatario->dirDestinatario)}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">RUC / C.I (Destinatario): </td>
                        <td style="font-size:12px;padding:5px" colspan="2">{{$data['pedido']->detalles[0]->agencia_carga->identificacion}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Razon social / Nombre y apellidos:: </td>
                        @php
                            $cliente="";
                            foreach ($data['pedido']->cliente->detalles as $c)
                                if($c->estado==1) $cliente = $c->nombre;
                        @endphp
                        <td style="font-size:12px;padding:5px" colspan="2">{{strtoupper($cliente)}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Ruta: </td>
                        <td style="font-size:12px;padding:5px" colspan="2">{{(String)$data['obj_xml']->destinatarios->destinatario->ruta}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</table>
<table style="border:1px solid black;width: 100%">
    <tr style="text-align: center">
        <th style="border:1px solid black;font-size: 12px"> Cantidad </th>
        <th style="border:1px solid black;font-size: 12px">Descripcion</th>
        <th style="border:1px solid black;font-size: 12px">Cod. Proncipal</th>
    </tr>
    @foreach($data['obj_xml']->destinatarios->destinatario->detalles->detalle as $det)
        <tr style="border:1px solid black;">
            <td style="font-size:12px;text-align: center;">{{number_format((int)$det->cantidad,2,".","")}}</td>
            <td style="font-size:12px;text-align: center;">{{$det->descripcion}}</td>
            <td style="font-size:12px;text-align: center;">{{$det->codigoInterno}}</td>
        </tr>
    @endforeach
</table>
<div style="width: 80%;margin: 5px auto;">
    <table style="border:1px solid black;width: 100%">
         <tr>
             <td style="font-size:12px;padding:5px;text-align: center;width: 406px;">Información Adicional</td>
         </tr>
        <tr>
            <td style="font-size:12px;padding:5px">Dirección: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[0]}}</td>
        </tr>
        <tr>
            <td style="font-size:12px;padding:5px">Teléfono: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[1]}} </td>
        </tr>
        <tr>
            <td style="font-size:12px;padding:5px">Correo: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[2]}}</td>
        </tr>
    </table>
</div>
