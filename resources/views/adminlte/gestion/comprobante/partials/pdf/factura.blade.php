<table>
    <tr>
        <td style="width: 350px;">
            <table>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;padding:90px 110px;text-align:center">
                        NO TIENE LOGO
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;">
                        <table>
                            <tr>
                                <td style="font-size:13px;padding:5px">{{(String)$data['obj_xml']->infoTributaria->nombreComercial}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:13px;padding:5px">DIRECCIÓN MATRIZ: {{(String)$data['obj_xml']->infoTributaria->dirMatriz}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:13px;padding:5px">DIRECCIÓN SUCURSAL: {{(String)$data['obj_xml']->infoFactura->dirEstablecimiento}}</td>
                            </tr>
                            <tr>
                                <td style="font-size:13px;padding:5px">OBLIGADO A LLEVAR CONTABILIDAD: {{(String)$data['obj_xml']->infoFactura->obligadoContabilidad}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid black;border-radius:5px;width:350px">
            <table>
                <tr>
                    <td style="font-size:13px;padding:5px">R.U.C.: {{(String)$data['obj_xml']->infoTributaria->ruc}}</td>
                </tr>
                <tr>
                    <td style="font-size:15px;padding:5px">FACTURA</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">No. {{$data['numeroComprobante']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">NÚMERO DE AUTORIZACIÓN</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">{{(String)$data['obj_xml']->infoTributaria->claveAcceso}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">FECHA Y HORA <br/>DE AUTORIZACIÓN: {{(String)$data['autorizacion']->fechaAutorizacion}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">AMBIENTE: {{(String)$data['autorizacion']->ambiente}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">EMISIÓN: {{(String)$data['obj_xml']->infoTributaria->tipoEmision == 1 ? "NORMAL" : "No se encontró tipo de emisión"}}</td>
                </tr>
                <tr>
                    <td style="font-size: 13pt;padding:5px">CLAVE DE ACCESO</td>
                </tr>
                <tr>
                    <td>
                        <img src="data:image/png;base64,{{$data['img_clave_acceso']}}" style="width: 350px;height: 50px"/>
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
                        <td style="font-size:15px;padding:5px">Razon social / Nombre y Apellidos: {{(String)$data['obj_xml']->infoFactura->razonSocialComprador}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12;padding:5px">RUC / CI: {{(String)$data['obj_xml']->infoFactura->identificacionComprador}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:15px;padding:5px">Destinatario: {{(String)$data['obj_xml']->infoFactura->razonSocialComprador}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:15px;padding:5px">Carguera: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[3]}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:15px;padding:5px">Fecha Emisión: {{(String)$data['obj_xml']->infoFactura->fechaEmision}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <table>
                <tr style="border:1px solid black;width: 750px">
                    <td style="border:1px solid black;width: 105px;text-align: center;">Cod. Principal</td>
                    <td style="border:1px solid black;width: 85px;text-align: center;">Cantidad</td>
                    <td style="border:1px solid black;text-align: center;">Descripción</td>
                    <td style="border:1px solid black;width: 100px;text-align: center;">Precio unitario</td>
                    <td style="border:1px solid black;width: 95px;text-align: center;">Descuento</td>
                    <td style="border:1px solid black;width: 95px;text-align: center;">Precio total</td>
                </tr>
                @php
                    $a = (Array)$data['obj_xml']->detalles;
                    $b = $a['detalle'];
                    !is_array($b)
                    ? $b = [$b]
                    : "";
                @endphp
                @foreach($b as $c)
                <tr style="width: 750px">
                        <td style="font-size: 12px;;border:1px solid black;padding-left: 5px">{{(String)$c->codigoPrincipal}}</td>
                        <td style="font-size: 12px;border:1px solid black;padding-left: 5px">{{(String)$c->cantidad}}</td>
                        <td style="font-size: 12px;width: 205px;border:1px solid black;padding-left: 5px">{{(String)$c->descripcion}}</td>
                        <td style="font-size: 12px;border:1px solid black;padding-left: 5px">{{(String)$c->precioUnitario}}</td>
                        <td style="font-size: 12px;border:1px solid black;padding-left: 5px">{{(String)$c->descuento}}</td>
                        <td style="font-size: 12px;border:1px solid black;padding-left: 5px">{{(String)$c->precioTotalSinImpuesto}}</td>
                    </tr>
                @endforeach
            </table>
        </tr>
    </table>
</table>
<table>
    <tr>
        <td style="border:1px solid black;width:420px">
            <table>
                <tr>
                    <td style="font-size:15px;padding:5px;text-align: center;width: 406px;">Información Adicional</td>
                </tr>
                <tr>
                    <td style="font-size:15px;padding:5px">Dirección: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[0]}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">Teléfono: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[2]}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">Correo: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[1]}}</td>
                </tr>
                @if(isset($data['obj_xml']->infoAdicional->campoAdicional[4]))
                    <tr>
                        <td style="font-size:13px;padding:5px">DAE: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[4]}}</td>
                    </tr>
                @endif
                @if(isset($data['obj_xml']->infoAdicional->campoAdicional[5]))
                <tr>
                    <td style="font-size:13px;padding:5px">GUÍA MADRE: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[5]}}</td>
                </tr>
                @endif
                @if(isset($data['obj_xml']->infoAdicional->campoAdicional[6]))
                <tr>
                    <td style="font-size:13px;padding:5px">GUÍA HIJA: {{(String)$data['obj_xml']->infoAdicional->campoAdicional[6]}}</td>
                </tr>
                @endif
                <tr>
                    <td style="font-size:13px;padding:5px">TOTALES</td>
                </tr>
                <tr>
                    <td style="font-size:13px;padding:5px">TALLOS</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL 12%</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->totalConImpuestos->totalImpuesto->codigoPorcentaje == 2 ? (String)$data['obj_xml']->infoFactura->totalSinImpuestos : "0.00"}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL 0%</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->totalConImpuestos->totalImpuesto->codigoPorcentaje == 0 ? (String)$data['obj_xml']->infoFactura->totalSinImpuestos: "0.00" }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL No objeto de IVA</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->totalConImpuestos->totalImpuesto->codigoPorcentaje == 6 ? (String)$data['obj_xml']->infoFactura->totalSinImpuestos: "0.00" }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL SIN IMPUESTO</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->totalSinImpuestos}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL Exento de IVA</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->totalConImpuestos->totalImpuesto->codigoPorcentaje == 7 ? (String)$data['obj_xml']->infoFactura->totalSinImpuestos: "0.00" }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">DESCUENTO</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->totalDescuento}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">ICE</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">0.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">IVA 12%</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->totalConImpuestos->totalImpuesto->valor}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">PROPINA</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">0.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;font-size: 13px;width:200px">VALOR TOTAL</td>
                    <td style="border: 1px solid black;width: 85px;text-align: right">{{(String)$data['obj_xml']->infoFactura->importeTotal}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
