<tr id="tr_select_agencias_carga_{{$cantTr+1}}">
    <td>
        <div class="">
            <select id="select_agencia_carga_{{$cantTr+1}}" name="select_agencia_carga_{{$cantTr+1}}" class="form-control" required>
                <option disabled selected>Seleccione</option>
                @foreach($dataAgenciaCargo as $agenciaCargo)
                    <option value="{{$agenciaCargo->id_agencia_carga}}">{{$agenciaCargo->nombre}}</option>
                @endforeach
            </select>
            <input type="hidden" id="id_select_agencia_carga_{{$cantTr+1}}" value="">
        </div>
    </td>
    <td class="text-center">
        <button type="button" id="btn_delete_campo_{{$cantTr+1}}" class="btn btn-xs btn-danger" title="Añadir campo" onclick="delete_inputs('{{$cantTr+1}}')">
            <i class="fa fa-fw fa-trash"></i>
        </button>
    </td>
</tr>
