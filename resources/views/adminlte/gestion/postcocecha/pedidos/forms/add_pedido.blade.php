<form id="form_add_pedido" name="form_add_pedido">
    <input type="hidden" id='id_cliente' value="{{$idCliente}}">
    @if($pedido_fijo)
        <div class="">
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
            <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: white"
                   id="table_content_recepciones">
                <thead>
                <tr style="border: none; border-color: white">
                    <tr style="border: none;padding: 0 0 5px ;">
                        @if(!$pedido_fijo)
                            <td colspan="4" style="border: none;">
                                <label for="Fecha de entrega" style="font-size: 11pt">Fecha de entrega</label>
                                <input type="date" id="fecha_de_entrega" name="fecha_de_entrega" onchange="buscar_saldos($(this).val(), 3, 3)"
                                       value="{{\Carbon\Carbon::now()->toDateString()}}" class="form-control" required>
                            </td>
                        @endif
                        @if($vista === 'pedidos')
                            <td colspan="4  " style="border: none;">
                                <label for="Cliente" style="font-size: 11pt">Cliente</label>
                                <select class="form-control" id="id_cliente_venta" name="id_cliente_venta"
                                        onchange="cargar_espeicificaciones_cliente(true)" required>
                                    <option disabled selected> Seleccione</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                                    @endforeach
                                </select>
                            </td>
                            <td colspan="4" style="border:none"></td>
                            <td style="border:none;vertical-align: bottom;" class="hide text-center" id="btn_remove_especificacicon">
                                <button type='button' class='btn btn-xs btn-danger' onclick='borrar_duplicado()'>
                                    <i class='fa fa-fw fa-trash'></i>
                                </button>
                            </td>
                        @else
                            <input type="hidden" value="{{$idCliente}}" id="id_cliente_venta">
                        @endif
                    </tr>
                    <th style="border: none"></th>
                    {{-- <th style="border: none;text-align: right">
                       <button type="button" id="btn_add_campos" onclick="add_campos(1,'{{$idCliente}}')" class="btn btn-success btn-xs"><i
                                    class="fa fa-plus" aria-hidden="true"></i></button>
                        <button type="button" onclick="delete_campos(1)" id="btn_delete_inputs" class="btn btn-danger btn-xs hide"><i
                                    class="fa fa-trash " aria-hidden="true"></i></button>
                    </th>--}}
                </tr>
                <tr style="background-color: #dd4b39; color: white">
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d;width: 80px">
                        PIEZAS
                    </th>
                   {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d;width: 50px">
                        CAJAS FULL
                    </th>--}}
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        VARIEDAD
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        PESO
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        CAJA
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        PRESENTACIÓN
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        RAMO X CAJA
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        TOTAL RAMOS
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        TALLOS X RAMO
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        LONGITUD
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d;width:100px">
                        PRECIO X VARIEDAD
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d;width:100px">
                        PRECIO X ESPECIFICACIÓN
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        AGENCIA DE CARGA
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d;width:100px;width: 20px;">
                        OPCIONES
                    </th>
                </tr>
                </thead>
                <tbody id="tbody_inputs_pedidos"></tbody>
                <table style="width: 100%;text-align: right;margin-top:20px">
                    <tr>
                        <td><b>TOTAL DE RAMOS:</b></td>
                        <td style="vertical-align: middle;font-size: 14px;text-align: right; width: 80px;" id="total_ramos">0</td>
                    </tr>
                    <tr>
                        <td><b>MONTO DEL PEDIDO:</b></td>
                        <td id="monto_total_pedido" style="font-size: 14px;vertical-align: middle;text-align:rigth">$0.00</td>
                    </tr>
                </table>
                <table style="width: 100%;">
                    {{--<tr>
                        <td>
                            <label for="Descripcion">Descripcion</label>
                            <textarea name="descripcion" id="descripcion" cols="200" rows="5" class="form-control"></textarea>
                        </td>
                    </tr>--}}
                    <tr>
                        <td class="text-center" style="padding: 10px 0px 0px">
                            <button class="btn btn-success" type="button" id="btn_guardar_modal_add_cliente"
                                    onclick="store_pedido('{{$idCliente}}','@if($pedido_fijo) {{true}} @endif','{{csrf_token()}}','{{$vista}}',{{$id_pedido}})">
                                <span class="bootstrap-dialog-button-icon fa fa-fw fa-save"></span>
                                Guardar
                            </button>
                        </td>
                    </tr>
                </table>
            </table>
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

        function cambiar_input_precio(idDetEmp,id_precio,posicon_variedad) {
            $('#td_precio_variedad_'+ idDetEmp+'_'+id_precio).html('<input type="number" id="precio_' + id_precio + '_' + posicon_variedad + '" ' +
                'name="precio_' + idDetEmp + '" min="0" onchange="calcular_precio_pedido()" class="form-control text-center precio_'+id_precio+'" style="background-color: beige; width: 100%">');
        }
        
        function borrar_duplicado() {
            $(".tr_remove_"+$("#cant_esp").val()).remove();
            if(parseInt($("#cant_esp_fijas").val()) != parseInt($("#cant_esp").val()))
                $("#cant_esp").val(parseInt($("#cant_esp").val())-1);
            if(parseInt($("#cant_esp_fijas").val()) == parseInt($("#cant_esp").val()))
                $("#btn_remove_especificacicon").addClass('hide');
            calcular_precio_pedido();
        }

    </script>
@endif
