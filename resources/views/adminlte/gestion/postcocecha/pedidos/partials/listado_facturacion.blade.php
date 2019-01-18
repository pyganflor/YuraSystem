<div id="table_envios" style="margin-top: 20px">
    <table width="100%" class="table table-responsive table-bordered"
           style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_content_envios_facturar">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                ENVIO N#
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                FECHA DE ENVíO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                CANTIDAD / ESPECIFICIACIONES
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                CLIENTE
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                AGENCIA DE TRANSPORTE
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                TIPO AGENCIA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                DESCUENTO $ / EN FACTURA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                OPCIONES
            </th>
        </tr>
        </thead>
        @foreach($data_envios as $key => $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')"
                onmouseleave="$(this).css('background-color','')" class="" id="row_pedidos_">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand">
                    {{str_pad($item->id_envio,9,"0",STR_PAD_LEFT)}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    {{\Carbon\Carbon::parse($item->fecha_pedido)->format('d-m-Y')}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand">
                {{$item->cantidad}} {{$item->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$item->nombre_cl}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    {{$item->at_nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    @if($item->tipo_agencia == 'A')
                        AÉREA
                    @elseif($item->tipo_agencia == 'T')
                        TERRESTRE
                    @elseif($item->tipo_agencia == 'M')
                        MARíTIMA
                    @endif
                </td>
                <td style="border-color: #9d9d9d" class="text-center "  id="popover_pedidos">
                    <input type="number" onkeypress="return isNumber(event)" min="1" id="descuento_{{$key+1}}" name="descuento_{{$key+1}}" ondblclick="activar(this)" readonly>
                    <input type="checkbox" id="muestra_descuento_{{$key+1}}" name="muestra_descuento" disabled>
                </td>
                <td class="text-center"  style="border-color: #9d9d9d">
                    @if($item->empaquetado == 1)
                        <input type="checkbox" id="check_envio" name="check_envio" value="{{$item->id_envio}}">
                        {{--<button class="btn  btn-default btn-xs" type="button" title="Facturar envío" id="facturar_envio"
                                   onclick="facturar_envio('{{$item->id_envio}}')">
                               <i class="fa fa-file-text-o" aria-hidden="true"></i>
                           </button>--}}
                    @else
                        Debe empaquetarse para poder facturar este envío
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <div class="text-center">
        <button type="button" class="btn btn-success" onclick="genera_comprobante_cliente()">
            <i class="fa fa-file-text-o" aria-hidden="true"></i>
            Generar factura
        </button>
    </div>
</div>
<script>
    function genera_comprobante_cliente(){
        var result = confirm("¿Esta seguro que facturar los envíos seleccionados?");
        if (result) {
            arrEnvios = [];
            $.each($('input:checkbox[name=check_envio]:checked'), function (i, j) {
                arrEnvios.push([
                    j.value,
                    $("#descuento_"+(i+1)).val(),
                    $("#muestra_descuento_"+(i+1)).is(":checked")
                ]);
            });
            if(arrEnvios.length === 0){
                modal_view('modal_view_msg_factura',
                    '<div class="alert text-center  alert-warning"><p>Debe seleccionar al menos un envío para facturar</p></div>',
                    '<i class="fa fa-fw fa-table"></i> Estatus facturas', true, false,'{{isPC() ? '50%' : ''}}');
                return false;
            }

            $.LoadingOverlay("show", {
                image       : "",
                progress    : true,
                text        : "Generando factura"
            });
            var count     = 0;
            var cantidad_envios = arrEnvios.length;
            var tiempo = cantidad_envios*2300;
            var interval  = setInterval(function(){
                if (count >= 100) {
                    clearInterval(interval);
                    return;
                }
                count += 100/cantidad_envios;
                $.LoadingOverlay("progress", count);
            }, tiempo);
            datos = {
                _token: '{{csrf_token()}}',
                arrEnvios : arrEnvios,
            };
            $.get('{{url('comprobante/generar_comprobante_cliente')}}', datos, function (retorno) {
                $.LoadingOverlay("hide");
                modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus facturas', true, false,
                    '{{isPC() ? '50%' : ''}}');
            });
        }
    }

    function activar(input_descuento,id_check){
        var id = input_descuento.id.split("_")[1];
        $("#"+input_descuento.id,).removeAttr("readonly");
        $("#muestra_descuento_"+id).removeAttr("disabled");
    }

    $(function () {
        $('[data-toggle="popover"]').popover()
    });
</script>
