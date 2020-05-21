<tr id="tr_pedido_piezas" class="tr_pedido_piezas">
    <td class="text-center" style="vertical-align: middle; background-color: #357CA5; color: white">
        <select id="pedido_{{$cant_form}}_{{$sec}}" name="pedido_{{$cant_form}}_{{$sec}}"
                class="pedido" style="color: black" onchange="obetener_piezas('{{$cant_form}}','{{$sec}}',this)">
            <option value="">Seleccione</option>
            @foreach($arr_pedidos as $x => $p)
                <option value="{{$p}}">Pedido #{{$x+1}}</option>
            @endforeach
        </select>
    </td>
    <td class="text-center" style="vertical-align: middle; background-color: #357CA5; color: white">
        <label class="control-label col-sm-2" style="padding:0"> FULL:</label>
        <div class="col-sm-10">
            <input type="number" min="0" class="caja caja_{{$cant_form}} caja_{{$cant_form}}_{{$sec}}"
                   value="0" id="full_{{$cant_form}}_{{$sec}}"
                   onchange="calcular_piezas('table_despacho_{{$cant_form}}')"
                   onkeyup="calcular_piezas('table_despacho_{{$cant_form}}')"
                   style="border: none;width: 84px;height: 16px;color:black" required>
        </div>
    </td>
    <td class="text-center" style="vertical-align: middle; background-color: #357CA5; color: white">
        <label class="control-label col-sm-2" style="padding:0"> HALF:</label>
        <div class="col-sm-10">
            <input type="number" min="0" class="caja caja_{{$cant_form}} caja_{{$cant_form}}_{{$sec}}"
                   value="0" id="half_{{$cant_form}}_{{$sec}}"
                   onchange="calcular_piezas('table_despacho_{{$cant_form}}')"
                   onkeyup="calcular_piezas('table_despacho_{{$cant_form}}')"style="border: none;width: 84px;height: 16px;color:black" required>
        </div>
    </td>
    <td class="text-center" style="vertical-align: middle; background-color: #357CA5; color: white">
        <label class="control-label col-sm-2" style="padding:0"> 1/4:</label>
        <div class="col-sm-10">
            <input type="number"min="0" class="caja caja_{{$cant_form}} caja_{{$cant_form}}_{{$sec}}"
                   value="0" id="cuarto_{{$cant_form}}_{{$sec}}"
                   onchange="calcular_piezas('table_despacho_{{$cant_form}}')"
                   onkeyup="calcular_piezas('table_despacho_{{$cant_form}}')"style="border: none;width: 84px;height: 16px;color:black" required>
        </div>
    </td>
    <td class="text-center" style="vertical-align: middle; background-color: #357CA5; color: white">
        <label class="control-label col-sm-2" style="padding:0"> 1/6:</label>
        <div class="col-sm-10">
            <input type=number" min="0"  class="caja caja_{{$cant_form}} caja_{{$cant_form}}_{{$sec}}"
                   value="0" id="sexto_{{$cant_form}}_{{$sec}}"
                   onchange="calcular_piezas('table_despacho_{{$cant_form}}')"
                   onkeyup="calcular_piezas('table_despacho_{{$cant_form}}')"
                   style="border: none;width: 84px;height: 16px;color:black" required>
        </div>
    </td>
    <td class="text-center" style="vertical-align: middle; background-color: #357CA5; color: white">
        <label class="control-label col-sm-2" style="padding:0"> 1/8:</label>
        <div class="col-sm-10">
            <input type="number" min="0"  class="caja caja_{{$cant_form}} caja_{{$cant_form}}_{{$sec}}"
                   value="0" id="octavo_{{$cant_form}}_{{$sec}}"
                   onchange="calcular_piezas('table_despacho_{{$cant_form}}')"
                   onkeyup="calcular_piezas('table_despacho_{{$cant_form}}')"
                   style="border: none;width: 84px;height: 16px;color:black" required>
        </div>
    </td>
    @if($sec == 1)
        <td class="text-center" id="td_piezas" style="vertical-align: middle; background-color: #357CA5; color: white">
            <b> Piezas: <span id="piezas_x_camion_{{$cant_form}}" class="piezas_x_camion"></span></b><br />
            <b> Cajas fules: <span id="cajas_fules_{{$cant_form}}" class="cajas_fules"></span></b>
        </td>
        <td class="text-center" id="td_btn" style="vertical-align: middle; background-color: #357CA5; color: white" >
            <button type="button" class="btn btn-success btn-xs" title="Agregar pedido"
                    onclick="add_pedido_piezas('table_despacho_{{$cant_form}}')">
                    <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
            <button type="button" class="btn btn-danger btn-xs" title="Quitar pedido"
                    onclick="delete_pedido_piezas('table_despacho_{{$cant_form}}')">
                    <i class="fa fa-minus" aria-hidden="true"></i>
            </button>
        </td>
    @endif
</tr>
