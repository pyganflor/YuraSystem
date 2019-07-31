<table>
    <tr>
        <td style="width: 350px;">
            <table>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;text-align:center;{{!isset($data['pedido']->envios[0]->comprobante->empresa->imagen)  ? "padding:90px 110px;" : ""}}">
                        @if(isset($data['pedido']->envios[0]->comprobante->empresa->imagen))
                            <img src="{{public_path('imagen')."/".$data['pedido']->envios[0]->comprobante->empresa->imagen}}" style="width:320px;height: 200px">
                        @else
                            NO TIENE LOGO
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;">
                        <table>
                            <tr>
                                <td style="font-size:12px;padding:5px">{{$data['comprobante']->detalle_guia_remision->razon_social_emisor}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:12px;padding:5px">DIRECCIÓN MATRIZ: {{$data['comprobante']->detalle_guia_remision->direccion_matriz_emisor}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:12px;padding:5px">DIRECCIÓN SUCURSAL: {{$data['comprobante']->detalle_guia_remision->direccion_establecimiento_emisor}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:12px;padding:5px">OBLIGADO A LLEVAR CONTABILIDAD: {{$data['comprobante']->detalle_guia_remision->obligado_contabilidad == 1 ? "SI" : "NO"}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid black;border-radius:5px;width:350px">
            <table>
                <tr>
                    <td style="font-size:12px;padding:5px">R.U.C.: {{$data['comprobante']->detalle_guia_remision->identificacion_emisor}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">GUÍA DE REMISIÓN</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">No. {{$data['comprobante']->secuencial}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">NÚMERO DE AUTORIZACIÓN</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">
                        @if($data['comprobante']->clave_acceso != null)
                            {{$data['comprobante']->clave_acceso}}
                        @else
                            SIN VALOR TRIBUTARIO
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">FECHA Y HORA <br/>DE AUTORIZACIÓN: {{$data['comprobante']->fecha_autorizacion != null ? $data['comprobante']->fecha_autorizacion : "" }}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">AMBIENTE: {{$data['comprobante']->ambiente == 1 ? "PRUEBAS" : "PRODUCCIÓN"}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px;padding:5px">EMISIÓN: NORMAL</td>
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
                        <td style="font-size:12px;padding:5px">Idetificación (Transportista): {{$data['detalle_despacho']->despacho->conductor->ruc}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Razon social / Nombre y apellidos: {{$data['detalle_despacho']->despacho->conductor->nombre}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Placa: {{$data['detalle_despacho']->despacho->camion->placa}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Puerto de partida: {{$data['detalle_despacho']->pedidos[0]->envios[0]->comprobante->empresapresa->direccion_establecimiento}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Fecha inicio transporte: {{$data['detalle_despacho']->despacho->fecha_despacho}}</td>
                        <td style="font-size:12px;padding:5px">Fecha fin transporte: {{$data['detalle_despacho']->despacho->fecha_despacho}}</td>
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
                        <td style="font-size:12px;padding:5px;width:200px">{{getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->secuencial}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px;" >  Número de Autorización: </td>
                        <td  style="font-size:12px;padding:5px;" colspan="2"> {{getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->clave_acceso != null ? getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->clave_acceso : ""}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px;width:240px">Motivo traslado: </td>
                        <td style="font-size:12px;padding:5px" colspan="2" >Egreso por venta</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Destino:</td>
                        <td style="font-size:12px;padding:5px" colspan="2">{{strtoupper(getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->pedido->detalles[0]->agencia_carga->nombre)}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">RUC / C.I (Destinatario): </td>
                        <td style="font-size:12px;padding:5px" colspan="2">{{getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->pedido->detalles[0]->agencia_carga->identificacion}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Razon social / Nombre y apellidos:: </td>
                        <td style="font-size:12px;padding:5px" colspan="2">{{strtoupper(getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->detalle_factura->razon_social_comprador)}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;padding:5px">Ruta: </td>
                        <td style="font-size:12px;padding:5px" colspan="2">{{$data['comprobante']->detalle_guia_remision->destino}}</td>
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
    @foreach(getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->desglose_envio_factura as $desglose)
        <tr style="border:1px solid black;">
            <td style="font-size:12px;text-align: center;">{{number_format((int)$desglose->cantidad,2,".","")}}</td>
            <td style="font-size:12px;text-align: center;">{{$desglose->descripcion}}</td>
            <td style="font-size:12px;text-align: center;">{{$desglose->codigo_principal}}</td>
        </tr>
    @endforeach
</table>
<div style="width: 80%;margin: 5px auto;">
    @php

        if(getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->fatura_cliente_tercero == null){
            $direccionComprador = getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->pedido->cliente->detalle()->direccion;
            $telefonoComproador = getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->pedido->cliente->detalle()->telefono;
            $correoComprador = getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->pedido->cliente->detalle()->correo;
        }else{
             $direccionComprador = getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->fatura_cliente_tercero->direccion;
             $telefonoComproador = getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->fatura_cliente_tercero->telefono;
             $correoComprador = getComprobante($data['dataComprobanteRelacionado']->id_comprobante_relacionado)->envio->fatura_cliente_tercero->correo;
        }

    @endphp
    <table style="border:1px solid black;width: 100%">
        <tr>
            <td style="font-size:12px;padding:5px;text-align: center;width: 406px;">Información Adicional</td>
        </tr>
        <tr>
            <td style="font-size:12px;padding:5px">Dirección: {{$direccionComprador}}</td>
        </tr>
        <tr>
            <td style="font-size:12px;padding:5px">Teléfono: {{$telefonoComproador}} </td>
        </tr>
        <tr>
            <td style="font-size:12px;padding:5px">Correo: {{$correoComprador}}</td>
        </tr>
    </table>
</div>
