<form id="form_add_agencia_carga" name="form_add_agencia_carga">
    <input type="hidden" value="{!! isset($dataAgencia->id_agencia_carga) ? $dataAgencia->id_agencia_carga : '' !!}" id="id_agencia_carga">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Nombre de la agencia de carga</label>
                <input type="text" id="nombre_agencia" name="nombre_agencia" class="form-control" required maxlength="25" autocomplete="off" value="{!! !empty($dataAgencia->nombre) ? $dataAgencia->nombre : '' !!}"  required="" minlength="3">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="Código">Código de la agencia de carga</label>
                <input type="text" id="codigo_agencia" name="codigo_agencia" class="form-control" required maxlength="25" autocomplete="off" value="{!! !empty($dataAgencia->codigo) ? $dataAgencia->codigo : '' !!}"  required="" minlength="3">
            </div>
        </div>
    </div>
</form>

