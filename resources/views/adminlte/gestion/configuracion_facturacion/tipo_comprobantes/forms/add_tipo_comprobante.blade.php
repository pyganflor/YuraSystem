<form id="form_add_comprobante">
    <input type="hidden" id="id_comprobante" value="{{!empty($data_comprobante->id_tipo_comprobante) ? $data_comprobante->id_tipo_comprobante : ''}}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre_comprobante">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off" value="{{!empty($data_comprobante->nombre) ? $data_comprobante->nombre : ''}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="identificacion">Código</label>
                <input type="text" id="codigo" name="codigo" class="form-control" required minlength="2" maxlength="2"  autocomplete="off" value="{{!empty($data_comprobante->codigo) ? $data_comprobante->codigo : ''}}">
            </div>
        </div>
    </div>
</form>
