<table width="100%" class="table-responsive table-bordered" style="font-size: 1em; border-color: #9d9d9d" id="table" >
    <thead>
    <tr style="background-color: #dd4b39; color: white">
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle" colspan="2">
            Filas: <input type="number" id="filas" name="filas" value="10" style="border:none;width: 60px;color:black"
                          onchange="filas('{{$pedido->id_pedido}}')">
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle" colspan="2">
            Siglas: <input type="text" id="siglas" name="siglas" style="border:none;width: 60px;color:black">
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle" colspan="2">
            Factura: {{isset($pedido->envios[0]->comprobante) ? $pedido->envios[0]->comprobante->secuencial :''}}
        </th>
    </tr>
    <tr style="background-color: #dd4b39; color: white">
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
            Tipo empaque
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
            Cajas
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
            Tipo/Peso
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
            Siglas
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
            Et. Inicial
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
            Et. Final
        </th>
    </tr>
    </thead>
    <tbody id="tbody">
    {{--@foreach($facturas as $x => $item)
        @php $comprobante = getComprobante($item->id_comprobante) @endphp
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->secuencial}}</td>
            <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->envio->pedido->cliente->detalle()->nombre}}</td>
            <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->envio->pedido->detalles[0]->agencia_carga->nombre}}</td>
            <td style="border-color: #9d9d9d" class="text-center">
                @php
                    $full_equivalente_real = 0;
                    $full = false;
                    $half = false;
                    $cuarto = false;
                    $sexto = false;
                    $octavo = false;
                    foreach($comprobante->envio->pedido->detalles as $det_ped){
                        foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                            $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                            $caja = explode("|",$esp_emp->empaque->nombre)[1];
                                 if($caja == '1')
                                    $full = true;
                                if($caja == '0.5')
                                    $half = true;
                                if($caja == '0.25')
                                    $cuarto = true;
                                if($caja == '0.17')
                                    $sexto = true;
                                if($caja == '0.125')
                                    $octavo = true;
                        }
                    }
                @endphp
                {{number_format($full_equivalente_real,2,".","")}}
            </td>
            @if(!isset($vista))
                <td style="border-color: #9d9d9d;width:60px" class="text-center">
                    <input type="checkbox" name="doble_{{$x+1}}" class="doble" id="doble_{{$x+1}}">
                </td>
                <td style="border-color: #9d9d9d;vertical-align: middle;width:130px" class="text-center">
                    <table style="width: 100%">
                        <tr id="tr_exportables_{{$x+1}}">
                            @if($full)
                                <td>
                                    <label for="full_{{$x+1}}">Full</label>
                                    <input type="checkbox" checked name="full_{{$x+1}}" value="1" id="full_{{$x+1}}" class="exportar">
                                </td>
                            @endif
                            @if($half)
                                <td>
                                    <label for="half_{{$x+1}}">Half</label>
                                    <input type="checkbox" checked name="half_{{$x+1}}" value="0.5" id="half_{{$x+1}}" class="exportar">
                                </td>
                            @endif
                            @if($cuarto)
                                <td>
                                    <label for="cuarto_{{$x+1}}">Cuarto</label>
                                    <input type="checkbox" checked name="cuarto_{{$x+1}}" value="0.25" id="cuarto_{{$x+1}}" class="exportar">
                                </td>
                            @endif
                            @if($sexto)
                                <td>
                                    <label for="sexto_{{$x+1}}">Sexto</label>
                                    <input type="checkbox" checked name="sexto_{{$x+1}}" value="0.17" id="sexto_{{$x+1}}" class="exportar">
                                </td>
                            @endif
                            @if($octavo)
                                <td>
                                    <label for="octavo_{{$x+1}}">Octavo</label>
                                    <input type="checkbox" checked name="octavo_{{$x+1}}" value="0.125" id="octavo_{{$x+1}}" class="exportar">
                                </td>
                            @endif
                            <td class="hide">
                                <input type="hidden" name="selected_{{$x+1}}" value="{{$item->id_comprobante}}" class="id_comprobante" id="selected_{{$x+1}}">
                            </td>
                        </tr>
                    </table>

                </td>
            @else
                <td style="border-color: #9d9d9d;width:60px" class="text-center">
                    <button type="button" class="btn btn-primary btn-xs" title="Generar etiqueta por factura" onclick="form_etiqueta_factura('{{$item->id_comprobante}}')">
                        <i class="fa fa-file-excel-o"></i>
                    </button>
                </td>
            @endif
        </tr>
    @endforeach--}}
    </tbody>
</table>
@if(!isset($pedido->etiqueta_factura))
<table style="margin-top: 20px;width: 100%">
    <tr>
        <td class="text-center">
            <button type="buttom" class="btn btn-success"
                    onclick="store_etiquetas_factura('{{$pedido->id_pedido}}','{{csrf_token()}}')">
                <i class="fa fa-floppy-o" ></i> Guardar
            </button>
        </td>
    </tr>
</table>
@else
    <table style="margin-top: 20px;width: 100%">
        <tr>
            <td class="text-center">
                <button type="buttom" class="btn btn-danger"
                        onclick="delete_etiquetas_factura('{{$pedido->id_pedido}}','{{csrf_token()}}')">
                    <i class="fa fa-times" ></i> Eliminar etiquetas
                </button>
            </td>
        </tr>
    </table>
@endif
<script>filas('{{$pedido->id_pedido}}')</script>
