@if(is_array($data))
    @php  $detalleFactura = $data['comprobante']->detalle_factura; @endphp
    <table>
        <tr>
            <td style="width: 350px;">
                <table>
                    <tr>
                        <td style="border:1px solid black;border-radius:5px;text-align:center;{{(!isset($data['comprobante']->empresa->imagen)) ? "padding:90px 110px" : ""}}">
                            @if(isset($data['comprobante']->empresa->imagen))
                                <img src="{{"./images/".$data['comprobante']->empresa->imagen}}" style="width:320px;height: 200px">
                            @else
                                NO TIENE LOGO
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black;border-radius:5px;">
                            <table>
                                <tr>
                                    <td style="font-size:13px;padding:5px">{{$detalleFactura->razon_social_emisor}}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:13px;padding:5px">DIRECCIÓN MATRIZ: {{$detalleFactura->direccion_matriz_emisor}}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:13px;padding:5px">DIRECCIÓN SUCURSAL: {{$detalleFactura->direccion_establecimiento_emisor}}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:13px;padding:5px">OBLIGADO A LLEVAR CONTABILIDAD: {{$detalleFactura->obligado_contabilidad == 1 ? "SI" : "NO"}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="border:1px solid black;border-radius:5px;width:350px">
                 <table>
                     <tr>
                         <td style="font-size:13px;padding:5px">R.U.C.: {{$data['empresa']->ruc}}</td>
                     </tr>
                     <tr>
                         <td style="font-size:15px;padding:5px">FACTURA</td>
                     </tr>
                     <tr>
                         <td style="font-size:13px;padding:5px">No. {{$data['secuencial']}}</td>
                     </tr>
                     <tr>
                         <td style="font-size:13px;padding:5px">NÚMERO DE AUTORIZACIÓN</td>
                     </tr>
                     <tr>
                         <td style="font-size:13px;padding:5px">
                             @if($data['comprobante']->clave_acceso != null)
                                {{$data['comprobante']->clave_acceso}}
                             @else
                                 SIN VALOR TRIBUTARIO
                             @endif
                         </td>
                     </tr>
                     <tr>
                         <td style="font-size:13px;padding:5px">FECHA Y HORA <br/>DE AUTORIZACIÓN:
                             {{$data['comprobante']->fecha_autorizacion}}
                         </td>
                     </tr>
                     <tr>
                         <td style="font-size:13px;padding:5px">AMBIENTE:
                             @if(env('ENTORNO') == 1)
                                PRUEBAS
                             @elseif(env('ENTORNO') == 2)
                                 PRODUCCIÓN
                             @endif
                         </td>
                     </tr>
                     <tr>
                         <td style="font-size:13px;padding:5px">EMISIÓN: NORMAL </td>
                     </tr>
                     <tr>
                         <td style="font-size: 12pt;padding:5px">CLAVE DE ACCESO</td>
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
                    @php
                        $direccionComprador = $data['comprobante']->envio->pedido->cliente->detalle()->direccion;
                        $telefonoComproador = $data['comprobante']->envio->pedido->cliente->detalle()->telefono;
                        $correoComprador = $data['comprobante']->envio->pedido->cliente->detalle()->correo;
                        $dae = $data['comprobante']->envio->dae;
                        $andenComprador =  $data['comprobante']->envio->almacen;
                        $codigoPorcentajeImpuesto = $data['comprobante']->envio->pedido->cliente->detalle()->codigo_porcentaje_impuesto;
                        $impuesto =  getTipoImpuesto($data['comprobante']->envio->pedido->cliente->detalle()->codigo_impuesto, $data['comprobante']->envio->pedido->cliente->detalle()->codigo_porcentaje_impuesto);
                    @endphp
                    <table>
                        <tr>
                            <td style="font-size:15px;padding:5px">Razon social / Nombre y Apellidos: {{$data['comprobante']->envio->pedido->cliente->detalle()->nombre}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:12;padding:5px">RUC / CI: {{$data['comprobante']->envio->pedido->cliente->detalle()->ruc}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:15px;padding:5px">Destinatario: {{$data['comprobante']->envio->pedido->cliente->detalle()->nombre}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:15px;padding:5px">Carguera: {{$data['comprobante']->envio->pedido->detalles[0]->agencia_carga->nombre}}</td>
                        </tr>
                        <tr>
                            <td style="font-size:15px;padding:5px">Fecha Emisión: {{$data['comprobante']->fecha_emision}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <table style="width: 100%;border-collapse: collapse;">
                    <tr style="border:1px solid black;width: 750px">
                        <td style="border:1px solid black;width: 105px;text-align: center;">Cod. Principal</td>
                        <td style="border:1px solid black;width: 85px;text-align: center;">Cantidad</td>
                        <td style="border:1px solid black;text-align: center;">Descripción</td>
                        <td style="border:1px solid black;width: 100px;text-align: center;">Precio unitario</td>
                        <td style="border:1px solid black;width: 95px;text-align: center;">Descuento</td>
                        <td style="border:1px solid black;width: 95px;text-align: center;">Precio total</td>
                    </tr>
                    @foreach($data['comprobante']->desglose_envio_factura as $desglose)
                        <tr style="width: 750px">
                            <td style="font-size: 13px;;border:1px solid black;padding-left: 5px">{{$desglose->codigo_principal}}</td>
                            <td style="font-size: 13px;border:1px solid black;padding-left: 5px">
                                {{$desglose->cantidad}}
                            </td>
                            <td style="font-size: 12px;width: 205px;border:1px solid black;padding-left: 5px">{{$desglose->descripcion}}</td>
                            <td style="font-size: 13px;border:1px solid black;padding-left: 5px">{{number_format($desglose->precio_unitario,2,".","")}}</td>
                            <td style="font-size: 13px;border:1px solid black;padding-left: 5px">{{number_format($desglose->descuento,2,".","")}}</td>
                            <td style="font-size: 13px;border:1px solid black;padding-left: 5px">{{number_format($desglose->precio_total_sin_impuesto,2,".","")}}</td>
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
                        <td style="font-size:15px;padding:5px">Dirección: {{$direccionComprador}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;padding:5px">Teléfono: {{$telefonoComproador}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;padding:5px">Correo: {{$correoComprador}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;padding:5px">DAE: {{$dae}}</td>
                    </tr>
                    <tr>
                         <td style="font-size:13px;padding:5px">GUÍA MADRE: {{$data['comprobante']->envio->guia_madre}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;padding:5px">GUÍA HIJA: {{$data['comprobante']->envio->guia_hija}}</td>
                    </tr>
                    @if($andenComprador!=null)
                        <tr>
                             <td style="font-size:13px;padding:5px">ANDEN: {{$andenComprador}}</td>
                        </tr>
                    @endif
                   <tr>
                        <td style="font-size:13px;padding:5px">TOTALES:
                        @php
                            $piezas = 0;
                               foreach($data['comprobante']->envio->pedido->detalles as $det_ped){
                                   $piezas += $det_ped->cantidad;
                               }
                        @endphp
                            {{$piezas." Piezas"}}
                        </td>
                    </tr>
                   <tr>
                       <td style="font-size:13px;padding:5px">TALLOS TOTALES:
                           @php
                               $total_tallos = 0;
                               if($data['comprobante']->envio->pedido->tipo != "O"){
                                    foreach($data['comprobante']->envio->detalles as $det_env){
                                    $i = 0;
                                    foreach ($det_env->especificacion->especificacionesEmpaque as $esp_emp) {
                                        foreach ($esp_emp->detalles as $det_esp_emp){
                                            $total_tallos += ((int)$data['comprobante']->desglose_envio_factura[$i]->cantidad*$det_esp_emp->tallos_x_ramos);
                                            $i++;
                                        }
                                    }
                                }
                               }else{
                                    foreach ($data['comprobante']->envio->pedido->detalles as $det_ped) {
                                        $total_tallos+= $det_ped->total_tallos();
                                    }
                               }
                           @endphp
                           {{$total_tallos}}
                        </td>
                   </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>

                        <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL 12%</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{$codigoPorcentajeImpuesto == 2 ? $detalleFactura->total_sin_impuestos : "0.00"}}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL 0%</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{$codigoPorcentajeImpuesto == 0 ? $detalleFactura->total_sin_impuestos: "0.00" }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL No objeto de IVA</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{$codigoPorcentajeImpuesto == 6 ? $detalleFactura->total_sin_impuestos: "0.00" }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL SIN IMPUESTO</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{number_format($detalleFactura->total_sin_impuestos,2,".","")}}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">SUBTOTAL Exento de IVA</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{$codigoPorcentajeImpuesto == 7 ? $detalleFactura->total_sin_impuestos: "0.00" }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">DESCUENTO</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{number_format($detalleFactura->total_descuento,2,".","")}}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">ICE</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">0.00</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">IVA 12%</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{number_format(($detalleFactura->importe_total - $detalleFactura->total_sin_impuestos),2,".","")}}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">PROPINA</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{number_format($detalleFactura->propina,2,".","")}}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;font-size: 13px;width:200px">VALOR TOTAL</td>
                        <td style="border: 1px solid black;width: 85px;text-align: right">{{number_format($detalleFactura->importe_total,2,".","")}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@else
    <table>
        <tr>
            <td style="text-align: center">
                <h3>LA FACTURA NO EXISTE</h3>
            </td>
        </tr>
    </table>
@endif
