<tr id="tr_select_agencias_carga_{{$cantTr+1}}">
    <td>
        <div class="row">
            <div class="col-md-12">
            <select id="select_agencia_carga_{{$cantTr+1}}" name="select_agencia_carga_{{$cantTr+1}}" class="form-control" required>
                <option disabled selected>Seleccione</option>
                @foreach($dataAgenciaCargo as $agenciaCargo)
                    <option value="{{$agenciaCargo->id_agencia_carga}}">{{$agenciaCargo->nombre}}</option>
                @endforeach
            </select>
            </div>
            <input type="hidden" id="id_select_agencia_carga_{{$cantTr+1}}" value="">
        </div>
        <div class="row contacto_agencia_carga_{{$cantTr+1}}">
            <div class="col-md-12">
            <div class="row">
                <div class="col-md-4">
                    <label> Contacto</label>
                    <input type="text"  class="form-control contacto_cliente_agencia_carga" value="" required>
                </div>
                <div class="col-md-4">
                    <label> Correo</label>
                    <input type="email" class="form-control correo_cliente_agencia_carga" value="" required>
                </div>
                <div class="col-md-4">
                    <label> Dirección</label>
                    <input type="text" class="form-control direccion_cliente_agencia_carga" value="">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label> Contacto</label>
                    <input type="text" class="form-control contacto_cliente_agencia_carga" value="" >
                </div>
                <div class="col-md-4">
                    <label> Correo</label>
                    <input type="email" class="form-control correo_cliente_agencia_carga" value="" >
                </div>
                <div class="col-md-4">
                    <label> Dirección</label>
                    <input type="text" class="form-control direccion_cliente_agencia_carga" value="">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label> Contacto</label>
                    <input type="text"  class="form-control contacto_cliente_agencia_carga" value="" >
                </div>
                <div class="col-md-4">
                    <label> Correo</label>
                    <input type="email"  class="form-control correo_cliente_agencia_carga" value="" >
                </div>
                <div class="col-md-4">
                    <label> Dirección</label>
                    <input type="text"
                           class="form-control direccion_cliente_agencia_carga" value="">
                </div>
            </div>
            </div>
        </div>
    </td>
    <td class="text-center" style="vertical-align:middle">
        <button type="button" id="btn_delete_campo_{{$cantTr+1}}" class="btn btn-xs btn-danger" title="Eliminar campo" onclick="delete_inputs('{{$cantTr+1}}')">
            <i class="fa fa-fw fa-trash"></i>
        </button>
    </td>
</tr>
