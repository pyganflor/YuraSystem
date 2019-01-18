<form id="form_add_tipo_iva">
    <input type="hidden" id="id_tipo_iva" value="{{!empty($data_tipo_iva->id_tipo_iva) ? $data_tipo_iva->id_tipo_iva : ''}}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Código</label>
                <input type="text" id="codigo" name="codigo" class="form-control" required minlength="1" autocomplete="off" value="{{isset($data_tipo_iva->codigo) ? $data_tipo_iva->codigo : ''}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="Porcentaje">Porcentaje %</label>
                <input type="text" id="porcentaje" name="porcentaje" class="form-control" minlength="1"  required  autocomplete="off" value="{{isset($data_tipo_iva->porcentaje) ? $data_tipo_iva->porcentaje : ''}}">
            </div>
        </div>
    </div>
</form>
