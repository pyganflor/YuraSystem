<form id="form_add_agencia_transporte" name="form_add_agencia_transporte">
    <input type="hidden" value="{!! isset($dataAgencia->id_agencia_transporte) ? $dataAgencia->id_agencia_transporte : '' !!}" id="id_agencia_transporte">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Nombre de la agencia de transporte</label>
                <input type="text" id="nombre_agencia" name="nombre_agencia" class="form-control" required maxlength="25" autocomplete="off" value="{!! !empty($dataAgencia->nombre) ? $dataAgencia->nombre : '' !!}"  required="" minlength="3">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="Código">Agencia de transporte</label>
                <select class="form-control" id="agencia_transporte" name="agencia_transporte">
                    <option selected disabled>Seleccione</option>
                    <option value="A" {!! $dataAgencia->tipo_agencia == 'A' ? "selected='selected'" : '' !!}>Aérea</option>
                    <option value="M" {!! $dataAgencia->tipo_agencia == 'M' ? "selected='selected'" : '' !!}>Maritima</option>
                    <option value="T" {!! $dataAgencia->tipo_agencia == 'T' ? "selected='selected'" : '' !!}>Terrestre</option>
                </select>
            </div>
        </div>
    </div>
</form>

