<form id="form_add_tipo_identificacion">
    <input type="hidden" id="id_tipo_identificacion" value="{{!empty($data_tipo_identificacion->id_tipo_identificacion) ? $data_tipo_identificacion->id_tipo_identificacion : ''}}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off" value="{{!empty($data_tipo_identificacion->nombre) ? $data_tipo_identificacion->nombre : ''}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="identificacion">Código</label>
                <input type="text" id="codigo" name="codigo" class="form-control" minlength="2" maxlength="2"  required  autocomplete="off" value="{{!empty($data_tipo_identificacion->codigo) ? $data_tipo_identificacion->codigo : ''}}">
            </div>
        </div>
    </div>
</form>
