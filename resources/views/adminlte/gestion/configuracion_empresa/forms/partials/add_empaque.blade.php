<tr id="empaques_{{$cnatInptus+1}}">
    <td>
        <div class="form-inline">
            <div class="input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Nombre</span>
                <input type="text" id="campo_empaque_{{$cnatInptus+1}}" name="campo_empaque_{{$cnatInptus+1}}" required=""
                       class="form-control" minlength="1" maxlength="255" placeholder="nombre|factor|peso caja Gr">
            </div>
            <div class="input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Tipo</span>
                <select class="form-control" id="tipo_empaque_{{$cnatInptus+1}}">
                    <option selected disabled="">Seleccione</option>
                    <option value="C">Caja</option>
                    {{--<option value="E">Envoltura</option>--}}
                    <option value="P">Presentación</option>
                </select>
            </div>
        </div>
    </td>
    <td class="text-center">
        <input type="hidden" id="id_campo_empaque_{{$cnatInptus+1}}" value="">
        <button type="button" class="btn btn-xs btn-danger" title="Eliminar campo" onclick="delete_inputs('empaques','{{$cnatInptus+1}}')">
            <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
        </td>
</tr>
