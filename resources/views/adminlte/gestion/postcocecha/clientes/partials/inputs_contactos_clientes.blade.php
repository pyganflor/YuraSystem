<tr id="tr_select_agencias_carga_{{$cantTr+1}}">
    <td>
        <div class="form-group col-md-4">
            <div class="input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Nombre</span>
                <input type="text" id="nombre_contacto_{{$cantTr+1}}" name="nombre_contacto_{{$cantTr+1}}" required="" class="form-control" minlength="1" maxlength="100">
            </div>
        </div>
        <div class="form-group col-md-4">
            <div class="input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Correo</span>
                <input type="email" id="correo_{{$cantTr+1}}" name="correo_{{$cantTr+1}}" required="" class="form-control" minlength="1" maxlength="100">
            </div>
        </div>
        <div class="form-group col-md-4">
            <div class="input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Teléfono</span>
                <input type="text" id="telefono_{{$cantTr+1}}" name="telefono_{{$cantTr+1}}" required="" class="form-control" minlength="1" maxlength="15">
            </div>
        </div>
        <div class="form-group col-md-12">
            <div class="input-group" >
                <span class="input-group-addon" style="background-color: #e9ecef">Dirección</span>
                <textarea id="direccion_{{$cantTr+1}}" name="direccion_{{$cantTr+1}}" required="" class="form-control" cols="3" minlength="1" maxlength="500"></textarea>
            </div>
        </div>
        <input type="hidden" id="id_inputs_contacto_{{$cantTr+1}}" value="">
    </td>
    <td class="text-center">
        <button type="button" id="btn_delete_campo_{{$cantTr+1}}" class="btn btn-xs btn-danger" title="Añadir campo" onclick="delete_inputs('{{$cantTr+1}}')">
            <i class="fa fa-fw fa-trash"></i>
        </button>
    </td>
</tr>