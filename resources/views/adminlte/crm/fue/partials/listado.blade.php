<div id="table_facturas">
    @if(sizeof($facturas)>0)
        <form id="form_actualiar_fue" name="form_actualiar_fue">
            <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"  id="table_content_facturas">
                <thead>
                    <tr style="background-color: #dd4b39; color: white">
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"></th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            FACTURA
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width: 30px">
                            DAE
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            AWB
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            AWB H
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            MANIFIESTO
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            REFRENDO
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width: 65px">
                            PESO Kg.
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            CLIENTE
                        </th>
                    </tr>
                </thead>
                @foreach($facturas as $item)
                    @php
                        $comprobante = getComprobante($item->id_comprobante);
                        $factura_tercero = getFacturaClienteTercero(getComprobante($comprobante->id_comprobante)->id_envio);
                    @endphp
                    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')" id="tr_{{$item->id_comprobante}}">
                        <td  style="border-color: #9d9d9d;width: 30px" class="text-center" style="vertical-align: middle">
                            <input type="checkbox" class="factura_selected" id="factura_selected" value="{{$item->id_comprobante}}" name="factura_selected" checked required>
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle">
                            {{$item->secuencial}}
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle">
                            <input type="text" id="codigo_dae" name="codigo_dae" value="{{$factura_tercero !=null ? $factura_tercero->codigo_dae : (isset($comprobante->envio->codigo_dae) ? $comprobante->envio->codigo_dae : "")}}"
                                   required style="border: none;width:100%;text-align: center;width: 100%;">
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle">
                            <input type="text" name="guia_madre" id="guia_madre" value="{{$comprobante->envio->guia_madre}}"
                                   required style="border: none;width:100%;text-align: center">
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle">
                            <input type="text" name="guia_hija" id="guia_hija" value="{{$comprobante->envio->guia_hija}}"
                                   required style="border: none;width:100%;text-align: center">
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle">
                            <input type="text" value="{{isset($comprobante->manifiesto) ? $comprobante->manifiesto : ""}}" name="manifiesto" id="manifiesto"
                                   style="border: none;width:100%;text-align: center">
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle">
                            <input type="text" value="{{$factura_tercero !=null ? $factura_tercero->dae : (isset($comprobante->envio->dae) ? $comprobante->envio->dae : "")}}"
                                required name="dae_completa" id="dae_completa" style="border: none;width:100%;text-align: center">
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle;width: 65px">
                            <input type="number" id="peso" name="peso" value="{{number_format($comprobante->peso,2,".","")}}"
                                   required style="border:none; width:100%;text-align: center">
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center" style="vertical-align: middle">
                            {{$comprobante->envio->pedido->cliente->detalle()->nombre}}
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="text-center">
                <button type="button" class="btn btn-primary" onclick="actualizar_fue()">
                    <i class="fa fa-floppy-o"></i> Actualizar
                </button>
            </div>
        </form>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
