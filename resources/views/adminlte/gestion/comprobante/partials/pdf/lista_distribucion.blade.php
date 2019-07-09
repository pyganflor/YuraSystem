<table style="width:100%;font-family: arial, sans-serif;border-collapse: collapse;">
    <tr>
        <td style="vertical-align: middle;text-align: center">
            <h4> DISTRIBUTION LIST </h4>
        </td>
    </tr>
    <tr>
</table>
<table style="width:100%;font-family: arial, sans-serif;border-collapse: collapse;">
    <tr>
        <td>
            <div style="width: 300px;">
                <table>
                    <tr>
                        <td>
                            <b>  FROM: </b>
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
                            <b> SHIP TO:</b>
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
        <td>
            <div style="width:300px">
                <table style="border: 1px solid black;width: 100%">
                    <tr>
                        <td style="font-size:13px"><b>DATE:</b></td>
                        <td style="font-size:13px">{{\Carbon\Carbon::parse($comprobante->fecha_emision)->format('d-m-Y')}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px">INOVICE:</td>
                        <td style="font-size:13px">{{$comprobante->secuencial}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px">AWB No:</td>
                        <td style="font-size:13px">{{$comprobante->envio->guia_madre}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:13px">HAWB No:</td>
                        <td style="font-size:13px">{{$comprobante->envio->guia_hija}}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>

</table>
<table style="width:100%;font-family: arial, sans-serif;border-collapse: collapse;" >
    <thead style="border: 1px solid black" >
        <tr>
            <td style="padding-left: 5px;border: 1px solid black;font-size:12px" > DESCRIPCIÓN </td>
            <td style="padding-left: 5px;font-size:12px;border: 1px solid black">BOUNCHES <br /> BOX</td>
            <td style="padding-left: 5px;font-size:12px;border: 1px solid black"> INITIAL <br /> BOX</td>
            <td style="padding-left: 5px;font-size:12px;border: 1px solid black">FINAL <br /> BOX</td>
            <td style="padding-left: 5px;font-size:12px;border: 1px solid black">TOTAL <br /> BOXES</td>
            <td style="padding-left: 5px;border: 1px solid black;font-size:12px;width: 250px;">COLOR</td>
        </tr>
    </thead>
    <tbody style="border: 1px solid black">
        @foreach($comprobante->envio->pedido->pedidoMarcacionesOrderAsc as $w => $distribucion)
            <tr>
                <td style="font-size:12px;border:1px solid black">
                    @foreach(getEspecificacionEmpaque($distribucion->id_especificacion_empaque)->detalles as $det_esp_emp)
                        {{substr($det_esp_emp->variedad->planta->nombre,0,3)}}
                        {{$det_esp_emp->variedad->siglas}}
                    @endforeach
                </td>
                <td style=";font-size:12px;border:1px solid black">{{$distribucion->ramos}}</td>
                <td style="font-size:12px;border:1px solid black">{{$distribucion->pos_pieza}}</td>
                <td style="font-size:12px;border:1px solid black">
                    @if ($distribucion->piezas === 1 )
                        {{$distribucion->pos_pieza}}
                    @else
                        {{($distribucion->pos_pieza-1)+$distribucion->piezas}}
                    @endif
                </td>
                <td style="font-size:12px;border:1px solid black">{{$distribucion->piezas}}</td>
                <td style="font-size:12px;border:1px solid black">
                    @foreach (getDistribucion($distribucion->id_distribucion)->distribuciones_coloraciones as $z => $distribucion_coloracion)
                        @if($distribucion_coloracion->cantidad !== 0)
                            {{$distribucion_coloracion->cantidad ." ". $distribucion_coloracion->marcacion_coloracion->coloracion->color->nombre. ","}}
                        @endif
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
