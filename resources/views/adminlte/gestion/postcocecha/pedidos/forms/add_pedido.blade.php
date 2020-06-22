<form id="form_add_pedido" name="form_add_pedido">
    <input type="hidden" id='id_cliente' value="{{$idCliente}}">
    @if($pedido_fijo)
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action active">
                        OPCIONES DE ENTREGA
                    </a>
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                       onclick="div_opcion_pedido_fijo(1)">
                        DÍA SEMANA
                    </a>
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                       onclick="div_opcion_pedido_fijo(2)">
                        DÍA MES
                    </a>
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                       onclick="div_opcion_pedido_fijo(3)">
                        PERSONALIZADO
                    </a>
                </div>
            </div>
            <div class="col-md-9" id="div_opciones_pedido_fijo"></div>
        </div>
    @endif
    <div>
        <div id="table_recepciones">
            <div class="row" style="margin-bottom: 10px">
                @if(!$pedido_fijo)
                    <div class="col-md-3">
                        <label for="Fecha de entrega" style="font-size: 11pt">
                            <i class="fa fa-calendar"></i> Fecha de entrega </label>
                        <input type="date" id="fecha_de_entrega" name="fecha_de_entrega" onchange="buscar_saldos($(this).val(), 3, 3)"
                               value="{{\Carbon\Carbon::now()->toDateString()}}" class="form-control" required>
                    </div>
                @endif
                @if($vista === 'pedidos')
                    <div class="col-md-3">
                        <label for="Cliente" style="font-size: 11pt">
                            <i class="fa fa-user-circle-o"></i> Cliente
                        </label>
                        <select class="form-control" id="id_cliente_venta" name="id_cliente_venta"
                                onchange="cargar_espeicificaciones_cliente(true)" style="background:transparent" required>
                            <option disabled selected> Seleccione</option>
                            @foreach($clientes as $cliente)
                                <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="iva_cliente" name="iva_cliente" value="">
                        <input type="hidden" id="calibre_estandar" name="calibre_estandar" value="{{getCalibreRamoEstandar()->nombre}}">
                        <input type="hidden" id="ramos_x_caja_conf_empresa" name="ramos_x_caja_conf_empresa" value="{{getConfiguracionEmpresa()->ramos_x_caja}}">
                    </div>
                    <div class="col-md-3 hide">
                        <label for="envio" style="font-size: 11pt;margin-top: 30px">Envío automático</label>
                        <button type='button' id="" class='btn btn-xs btn-default'>
                            <input type="checkbox" id="envio_automatico" name="envio_automatico" checked >
                        </button>
                    </div>

                    <div class="col-md-3" >
                        <label for="id_configuracion_empresa" style="font-size: 11pt">
                            <i class="fa fa-building-o"></i> Facturar pedido con:
                        </label>
                        <select class="form-control" id="id_configuracion_empresa" name="id_configuracion_empresa" title="Seleccione un empresa para facturar los pedidos">
                            @foreach(getConfiguracionEmpresa(null,true) as $empresa)
                                @php $lastPedido = getLastPedido(); @endphp
                                <option {{isset($lastPedido) ? (($lastPedido->id_configuracion_empresa === $empresa->id_configuracion_empresa) ? "selected" : "") : ""}}
                                        style=" color: black" value="{{$empresa->id_configuracion_empresa}}">{{$empresa->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                        <div class="col-md-3" title="Usado para crear una factura ficticia">
                            <label for="id_configuracion_empresa" style="font-size: 11pt">
                                <i class="fa fa-pencil"></i> Factura asignada:
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" id="factura_ficticia" name="factura_ficticia"
                                            onclick="input_numero_ficticio(this)">
                                </span>
                                <input type="text" class="form-control" id="numero_ficticio"
                                       placeholder="Escriba el número" disabled name="numero_ficticio">
                                <span class="input-group-btn">
                                    <button type='button' class='btn btn-danger'
                                            title="Eliminar duplicado" onclick='borrar_duplicado()'>
                                        <i class='fa fa-fw fa-trash'></i>
                                    </button>
                                    <button type='button' class='btn btn-success hide' id="cambia_tinturado"
                                            title="Cambiar a pedido tinturado" onclick='cambia_tipo_pedido()'>
                                        <i class='fa fa-exchange'></i>
                                    </button>
                                </span>
                            </div>


                        </div>
                    {{--<div class="col-md-1" style="margin-top: 30px;text-align: right;">

                    </div>--}}
                @else
                    <input type="hidden" value="{{$idCliente}}" id="id_cliente_venta">
                @endif
            </div>
            <div class="row">
                <div class="col-md-12" id="table_campo_pedido"></div>
                <div class="text-danger col-md-12" id="error_codigo_venture"></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table style="width: 100%;text-align: right;margin-top:20px">
                        <tr>
                            <td><b>TOTAL DE PIEZAS:</b></td>
                            <td style="vertical-align: middle;font-size: 14px;text-align: right; width: 8%;" id="total_piezas">0</td>
                        </tr>
                        <tr>
                            <td><b>TOTAL DE RAMOS:</b></td>
                            <td style="vertical-align: middle;font-size: 14px;text-align: right; width: 8%;" id="total_ramos">0</td>
                        </tr>
                        <tr>
                            <td><b>SUBTOTAL:</b></td>
                            <td class="monto_total_pedido" style="font-size: 14px;vertical-align: middle;text-align:right;width: 8%;">$0.00</td>
                        </tr>
                        <tr>
                            <td style="width: 45px"><b>IVA:</b></td>
                            <td class="iva_pedido" style="font-size: 14px;vertical-align: middle;text-align:right;width: 8%;">$0.00</td>
                        </tr>
                        <tr>
                            <td><b>TOTAL:</b></td>
                            <td class="total_pedido" style="font-size: 14px;vertical-align: middle;text-align:right;width: 8%;">$0.00</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table style="width: 100%;">
                        @if((isset($comprobante->estado) && $comprobante->estado != 5) && (isset($comprobante->estado) && $comprobante->estado != 6) && (isset(getPedido($id_pedido)->envios[0]->comprobante->integrado) && !getPedido($id_pedido)->envios[0]->comprobante->integrado) || !isset($comprobante->estado))
                            <tr>
                                <td class="text-center" style="padding: 10px 0px 0px">
                                    <button type="button" class=" btn btn-app btn-xs btn-success store_pedido_normal"
                                            onclick="store_pedido('{{$idCliente}}','@if($pedido_fijo) {{true}} @endif','{{csrf_token()}}','{{$vista}}','{{$id_pedido}}')">
                                        <span class="badge bg-green monto_total_pedido" >$0.00</span>
                                        <i class="fa fa-shopping-cart"></i> Guardar
                                    </button>
                                    <button type="button" class=" btn btn-app btn-xs btn-success"
                                            onclick="reiniciar_orden_pedido()">
                                        <i class="fa fa-refresh" aria-hidden="true"></i> Reiniciar orden
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
@if(!$pedido_fijo)
    <div id="div_content_saldos" style="margin-top: 10px"></div>
    <script>
        function buscar_saldos(fecha, antes, despues) {
            datos = {
                fecha: fecha,
                antes: antes,
                despues: despues
            };

            $("#div_content_saldos").LoadingOverlay('show', {
                background: "rgba(250, 250, 250, 0.5)",
                image: "",
                fontawesome: "fa fa-cog fa-spin"
            });

            $.get('{{url('clientes/buscar_saldos')}}', datos, function (retorno) {
                $('#div_content_saldos').html(retorno);
            }).always(function () {
                $("#div_content_saldos").LoadingOverlay('hide');
            });
        }

    </script>
@endif
<script>
    function input_numero_ficticio(check){
        factura_ficticia= $("#numero_ficticio");
        if($(check).is(':checked')){
            factura_ficticia.removeAttr('disabled');
        }else{
            factura_ficticia.attr('disabled',true);
        }
    }

</script>
