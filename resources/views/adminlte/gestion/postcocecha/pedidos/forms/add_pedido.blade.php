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
                       title="Editar detalles de usuario" onclick="div_opcion_pedido_fijo(1)">
                        DÍA SEMANA
                    </a>
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                       title="Añadir nueva información" onclick="div_opcion_pedido_fijo(2)">
                        DÍA MES
                    </a>
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action">
                        PERSONALIZADO
                    </a>
                </div>
            </div>
            <div class="col-md-9" id="div_opciones_pedido_fijo"></div>
        </div>
    @endif
    <div>
        <div id="table_recepciones">
            <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: white"
                   id="table_content_recepciones">
                <thead>
                <tr style="border: none;border-color: white">
                    <th colspan="2" style="border: none;padding: 0 0 5px ;">
                        @if(!$pedido_fijo)
                            <div class="col-md-3">
                                <label for="Fecha de entrega" style="font-size: 11pt">Fecha de entrega</label>
                                <input type="date" id="fecha_de_entrega" name="fecha_de_entrega" onchange="buscar_saldos($(this).val(), 3, 3)"
                                       value="" class="form-control" required>
                            </div>
                        @endif
                        @if($vista === 'pedidos')
                            <div class="col-md-6">
                                <label for="Cliente" style="font-size: 11pt">Cliente</label>
                                <select class="form-control" id="id_cliente_venta" name="id_cliente_venta"
                                        onchange="cargar_espeicificaciones_cliente(true)" required>
                                    <option disabled selected> Seleccione</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" value="{{$idCliente}}" id="id_cliente_venta">
                        @endif
                    </th>
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
                        style="border-color: #9d9d9d;">
                        CANTIDAD
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                       DESCRIPCIÓN ESPECIFICIACIONES
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        AGENCIA DE CARGA
                    </th>
                </tr>
                </thead>
                <tbody id="tbody_inputs_pedidos"></tbody>
                <table>
                    <tr>
                        <td>
                            <label for="Descripcion">Descripcion</label>
                            <textarea name="descripcion" id="descripcion" cols="200" rows="5" class="form-control"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" style="padding: 10px 0px 0px">
                            <button class="btn btn-success" type="button" id="btn_guardar_modal_add_cliente"
                                    onclick="store_pedido('{{$idCliente}}','@if($pedido_fijo) {{true}} @endif','{{csrf_token()}}','{{$vista}}')">
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
                image       : "",
                fontawesome : "fa fa-cog fa-spin"
            });

            $.get('{{url('clientes/buscar_saldos')}}', datos, function (retorno) {
                $('#div_content_saldos').html(retorno);
            }).always(function () {
                $("#div_content_saldos").LoadingOverlay('hide');
            });
        }
    </script>
@endif
