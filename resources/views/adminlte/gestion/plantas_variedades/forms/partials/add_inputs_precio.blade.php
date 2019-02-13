<tr id="precios_{{$cntTr+1}}">
    <td>
        <select id="id_clasificacion_por_ramo_{{$cntTr+1}}" class="form-control" required>
            <option disabled selected> Seleccione </option>
            @foreach($dataClasificacionRamos as $clasificacionRamos)
                <option value="{{$clasificacionRamos->id_clasificacion_ramo}}">{{$clasificacionRamos->nombre." ".$clasificacionRamos->siglas}}</option>@endforeach
        </select>
    </td>
    <td>
        <input type="text" id="precio_{{$cntTr+1}}" name="precio_{{$cntTr+1}}" onkeypress="return isNumber(event)" required minlength="1" class="form-control">
    </td>
    <td>
        <input type="hidden" id="id_precio_{{$cntTr+1}}" value="">
        <button type="button" class="btn btn-xs btn-danger" title="Eliminar campo" onclick="delete_inputs('{{$cntTr+1}}')">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    </td>
</tr>
