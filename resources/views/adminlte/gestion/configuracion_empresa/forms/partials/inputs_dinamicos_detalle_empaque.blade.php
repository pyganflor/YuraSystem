<tr id="tr_detalles_empaque_{{$id+1}}">
    <td>
        <select id="empaque_id_variedad_{{$id+1}}" name="variedad_empaque_{{$id+1}}" class="form-control" required="">
            <option selected disabled="">Seleccione</option>
            @foreach($dataVariedad as $variedad)
                <option value="{{$variedad->id_variedad}}">{{$variedad->nombre}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select id="empaque_id_clasificacion_por_ramo_{{$id+1}}" name="empaque_name_clasificacion_por_ramo_{{$id+1}}" class="form-control" required="">
            <option selected disabled="">Seleccione</option>
            @foreach($dataClasificacionRamo as $clasificacionRamo)
                <option value="{{$clasificacionRamo->id_clasificacion_ramo}}">{{$clasificacionRamo->nombre}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" id="cantidad_empaque_{{$id+1}}" name="cantidad_empaque_{{$id+1}}" class="form-control" required maxlength="25" autocomplete="off" value="" pattern="^([0-9])*$" aria-required="" minlength="1">
    </td>
    <td class="text-center">
        <button type="button" title="Eliminar" class="btn btn-xs btn-danger" onclick="delete_inputs('tr_detalles_empaque','{{$id+1}}')">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    </td>
</tr>