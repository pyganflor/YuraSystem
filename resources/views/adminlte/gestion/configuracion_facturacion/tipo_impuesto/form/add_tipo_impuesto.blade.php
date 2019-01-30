<form id="form_add_tipo_impuesto">
    <input type="hidden" id="id_tipo_impuesto" value="{{!empty($data_tipo_impuesto->id_tipo_impuesto) ? $data_tipo_impuesto->id_tipo_impuesto : ''}}">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="Porcentaje">Impuesto</label>
                <select id="impuesto" name="impuesto"class="form-control" required>
                    <option disabled selected>Seleccione</option>
                    @foreach($impuestos as $impuesto)
                        <option value="{{$impuesto->codigo}}">{{$impuesto->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="nombre">Código</label>
                <input type="text" id="codigo" name="codigo" class="form-control" required minlength="1" autocomplete="off"
                       value="{{isset($data_tipo_impuesto->codigo) ? $data_tipo_impuesto->codigo : ''}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Porcentaje">Porcentaje %</label>
                <input type="text" id="porcentaje" name="porcentaje" class="form-control" minlength="1"  required
                       autocomplete="off" value="{{isset($data_tipo_impuesto->porcentaje) ? $data_tipo_impuesto->porcentaje : ''}}">
            </div>
        </div>
        <div class="col-md-12">
            <label for="Porcentaje">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
        </div>
    </div>
</form>
