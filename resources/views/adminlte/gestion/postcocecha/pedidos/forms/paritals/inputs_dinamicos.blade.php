@if(count($cant_especificaciones) >0)
    @for($i=0;$i<count($cant_especificaciones);$i++)
        @foreach($arr_data_cliente_especificacion[$i] as $key => $especificacion){
            @php $esp = getDetalleEspecificacion($especificacion->id_especificacion); @endphp
        @endforeach
        <tr id="tr_inputs_pedido_{{$i+1}}" onmouseover="$(this).css('background-color','#add8e6')"
            onmouseleave="$(this).css('background-color','')">
            <input type="hidden" id="id_detalle_pedido_{{$i+1}}" value="">
            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle" id="td_input_cantidad_{{$especificaciones[$i]->id_especificacion}}">
                <input type="number" id="cantidad_{{$i+1}}" name="cantidad_{{$i+1}}" min="1" class="form-control" style="border:none"
                      onchange="calcular_precio_pedido()" onkeypress="return isNumber(event)">
            </td>
            <td id="td_cajas_full_{{$i+1}}" style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
            </td>
            <td id="td_variedad_{{$i+1}}" style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                <ul style="padding: 0;margin:0">
                    @foreach($esp as $key => $e)
                        <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                            {{$e["variedad"]}}
                        </li>
                        <input type="hidden" id="id_variedad_{{$i+1}}_{{$key+1}}" name="id_variedad" value="{{$e['id_variedad']}}">
                    @endforeach
                </ul>
            </td>
            <td id="td_calibre_{{$i+1}}" style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                <ul style="padding: 0;margin:0">
                    @foreach($esp as $cal =>  $e)
                        <li id="li_calibre_{{$cal+1}}" style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
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
                            {{$e["presentacion"]}}
                        </li>
                    @endforeach
                </ul>
            </td>
            <td id="td_ramos_x_caja_{{$i+1}}" style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                <ul style="padding: 0;margin:0">
                    @foreach($esp as $rxc => $e)
                        <li id="li_rxc_{{$rxc+1}}" style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                            {{$e["rxc"]}}
                        </li>
                    @endforeach
                </ul>
            </td>
            <td id="td_total_ramos_especificacion{{$i+1}}" style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
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
            <td id="td_precio_{{$i+1}}" style="border-color: #9d9d9d;padding: 0px;vertical-align: middle" class="text-center">
                <select id="precio_{{$i+1}}" name="precio_{{$i+1}}" class="form-control"  style="border:none" ondblclick="cambiar_input(this,)">
                    @foreach(getOptionsPrecios($especificacion->id_cliente,$especificaciones[$i]->id_especificacion) as $precio)
                        <option value="{{$precio}}">{{$precio}}</option>
                    @endforeach
                </select>
            </td>
            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle" id="td_select_agencia_carga_{{$especificaciones[$i]->id_especificacion}}">
                <select id="id_agencia_carga_{{$i+1}}" style="border:none" name="id_agencia_carga_{{$i+1}}" class="form-control" {{$i+1 == 1 ? "required" : ""}}>
                    @foreach($agenciasCarga as $agencias)
                        <option value="{{$agencias->id_agencia_carga}}">{{$agencias->nombre}}</option>
                    @endforeach
                </select>
            </td>
            <td class="text-center" style="border-color: #9d9d9d;">
                <button type="button" class="btn btn-primary btn-xs" title="duplicar">
                    <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
            </td>
            <input type="hidden" id="id_especificacion_{{$i+1}}" name="id_especificacion_{{$i+1}}"  value="{{$arr_data_cliente_especificacion[$i][0]->id_cliente_pedido_especificacion}}">
        </tr>
    @endfor
    <tr>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="border:none"></td>
        <td style="vertical-align: middle"> <b>TOTALES:</b></td>
        <td style="vertical-align: middle;font-size: 14px;text-align:center" id="total_ramos"></td>
        <td style="vertical-align: middle"></td>
        <td style="vertical-align: middle"></td>
        <td id="total_pedido" style="font-size: 14px;vertical-align: middle"></td>
    </tr>
@else
    <tr id="">
        <td colspan="13">
            <div class="alert alert-warning text-center">
                <p style="font-size: 11pt;"> Este usuario no posee especificaciones asignadas </p>
            </div>
        </td>
    </tr>
@endif
