<tr id="tr_inputs_pedido_{{$cantTr}}">
    <input type="hidden" id="id_detalle_pedido_{{$cantTr}}" value="">
    <td>
        <input type="number" id="cantidad_{{$cantTr}}" name="cantidad_{{$cantTr}}" class="form-control" required onkeypress="return isNumber(event)">
    </td>
    <td>
        <select id="id_especificacion_{{$cantTr}}" name="id_especificacion_{{$cantTr}}" class="form-control" required>
            <option disabled selected>Seleccione</option>
            @foreach ($especificaciones as $especificacion)
                <option value="{{$especificacion->id_cliente_pedido_especificacion}}">{{$especificacion->nombre}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select id="id_agencia_carga_{{$cantTr}}" name="id_agencia_carga_{{$cantTr}}" class="form-control" required>
            <option disabled selected>Seleccione</option>
            @foreach($agenciasCarga as $agencias)
                <option value="{{$agencias->id_agencia_carga}}">{{$agencias->nombre}}</option>
            @endforeach
        </select>
    </td>
</tr>
