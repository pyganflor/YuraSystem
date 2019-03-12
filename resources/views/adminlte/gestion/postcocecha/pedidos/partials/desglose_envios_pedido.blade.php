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
                CANTIDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                VARIEDAD
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                CALIBRE
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                CAJA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                RAMO X CAJA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                PRESENTACIÓN
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                TALLOS X RAMO
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                LONGITUD
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
            {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                DESCUENTO $ / EN FACTURA
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                FACTURAR
            </th>--}}
        </tr>
        </thead>
       {{-- @php $x =1; @endphp--}}
        @foreach($data_envios as $key => $item)
            @php $esp = getDetalleEspecificacion($item[0]->id_especificacion);@endphp
            <tr onmouseover="$(this).css('background-color','#add8e6')"
                onmouseleave="$(this).css('background-color','')" class="" id="row_pedidos_">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand">
                    {{str_pad($key,9,"0",STR_PAD_LEFT)}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    {{\Carbon\Carbon::parse($item[0]->fecha_pedido)->format('d-m-Y')}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    @foreach($item as $i)
                        {{$i->cantidad}}
                    @endforeach
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $key => $e)

                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["variedad"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as  $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["calibre"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["caja"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["rxc"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["presentacion"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["txr"] == null ? "-" : $e["txr"] }}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                    <ul style="padding: 0;margin:0">
                        @foreach($esp as $e)
                            <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                {{$e["longitud"] == null ? "-" : $e["longitud"] }} {{($e["unidad_medida_longitud"] == null || $e["longitud"] == null) ? "" : $e["unidad_medida_longitud"]}}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <li style="list-style:none">{{$item[0]->nombre_cl}}</li>
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    <li style="list-style:none"> {{$item[0]->at_nombre}}</li>
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand"  id="popover_pedidos">
                    <li style="list-style:none"> @if($item[0]->tipo_agencia == 'A')
                            AÉREA
                        @elseif($item[0]->tipo_agencia == 'T')
                            TERRESTRE
                        @elseif($item[0]->tipo_agencia == 'M')
                            MARíTIMA
                        @endif
                    </li>
                </td>
               {{-- <td style="border-color: #9d9d9d" class="text-center "  id="popover_pedidos">
                    <input type="number" onkeypress="return isNumber(event)" min="1" id="descuento_{{$x}}" name="descuento_{{$x}}"
                           ondblclick="activar(this)" value="0.00" readonly>
                </td>
                <td class="text-center"  style="border-color: #9d9d9d">
                    @if($i->empaquetado == 1)
                        <input type="checkbox" id="{{$x}}" name="check_envio" value="{{$item[0]->id_envio}}">
                    @else
                        Debe empaquetarse para poder facturar este envío
                    @endif
                </td>--}}
            </tr>
           {{-- @php $x++; @endphp --}}
        @endforeach
    </table>
   {{-- <div class="text-center">
        <button type="button" class="btn btn-success" onclick="genera_comprobante_cliente()">
            <i class="fa fa-file-text-o" aria-hidden="true"></i>
            Generar factura
        </button>
    </div>--}}
</div>
<script>
    /*function genera_comprobante_cliente(){
        arrEnvios = [];
        $.each($('input:checkbox[name=check_envio]:checked'), function (i, j) {
            arrEnvios.push([
                j.value,
                $("#descuento_"+(j.id)).val(),
                //$("#muestra_descuento_"+(i+1)).is(":checked")
            ]);
        });

        if(arrEnvios.length === 0) {
            modal_view('modal_view_msg_factura',
                '<div class="alert text-center  alert-warning"><p>Debe seleccionar al menos un envío para facturar</p></div>',
                '<i class="fa fa-fw fa-table"></i> Estatus facturas', true, false, '{{isPC() ? '50%' : ''}}');
            return false;
        }
        var result = confirm("¿Esta seguro que facturar los envíos seleccionados?");
        if (result) {
            $.LoadingOverlay("show", {
                image       : "",
                progress    : true,
                text        : "Generando factura",
                colorText   : $fff
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
                arrEnvios : arrEnvios
            };
            $.get('{{url('comprobante/generar_factura_cliente')}}', datos, function (retorno) {
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
    }*/

    $(function () {
        $('[data-toggle="popover"]').popover()
    });
</script>
