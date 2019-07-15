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
                <label for="identificacion">identificación</label>
                <input type="text" id="identificacion" name="identificacion" class="form-control" required maxlength="20" autocomplete="off" value="{!! !empty($dataAgencia->identificacion) ? $dataAgencia->identificacion : '' !!}"  required="" minlength="1">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="Código">Código</label>
                <input type="text" id="codigo_agencia" name="codigo_agencia" class="form-control" required maxlength="10" autocomplete="off" value="{!! !empty($dataAgencia->codigo) ? $dataAgencia->codigo : '' !!}"  required="" minlength="2">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" class="form-control" required autocomplete="off" value="{!! !empty($dataAgencia->correo) ? $dataAgencia->correo : '' !!}"  required="" minlength="2">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="correo2">Correo</label>
                <input type="email" id="correo2" name="correo2" class="form-control" autocomplete="off" value="{!! !empty($dataAgencia->correo2) ? $dataAgencia->correo2 : '' !!}"   minlength="2">
            </div>
        </div><div class="col-md-6">
            <div class="form-group">
                <label for="correo3">Correo</label>
                <input type="email" id="correo3" name="correo3" class="form-control" autocomplete="off" value="{!! !empty($dataAgencia->correo3) ? $dataAgencia->correo3 : '' !!}"  minlength="2">
            </div>
        </div>
    </div>
</form>

