<form id="form_add_transportista" name="form_add_transportista">
    <input type="hidden" value="{!! isset($dataTransportista->id_transportista) ? $dataTransportista->id_transportista : '' !!}"
           id="id_transportista">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre_empresa" name="nombre_empresa" class="form-control" required autocomplete="off"
                       value="{!! !empty($dataTransportista->nombre_empresa) ? $dataTransportista->nombre_empresa : '' !!}" required minlength="2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="ruc">RUC</label>
                <input type="text" id="ruc" name="ruc" class="form-control" required autocomplete="off"
                       value="{!! !empty($dataTransportista->ruc) ? $dataTransportista->ruc : '' !!}" required minlength="2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="encargado">Encargado</label>
                <input type="text" id="encargado" name="encargado" class="form-control" required autocomplete="off"
                       value="{!! !empty($dataTransportista->encargado) ? $dataTransportista->encargado : '' !!}" required minlength="2">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="ruc_encargado">RUC encargado</label>
                <input type="text" id="ruc_encargado" name="ruc_encargado" class="form-control" required autocomplete="off"
                       value="{!! !empty($dataTransportista->nombre_empresa) ? $dataTransportista->nombre_empresa : '' !!}" required minlength="2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="telefono_encargado">Telenfono encargado</label>
                <input type="text" id="telefono_encargado" name="telefono_encargado" class="form-control" required autocomplete="off"
                       value="{!! !empty($dataTransportista->telefono_encargado) ? $dataTransportista->telefono_encargado : '' !!}" required minlength="2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="direccion_empresa">Direccion empresa</label>
                <input type="text" id="direccion_empresa" name="direccion_empresa" class="form-control" required autocomplete="off"
                       value="{!! !empty($dataTransportista->direccion_empresa) ? $dataTransportista->direccion_empresa : '' !!}" required minlength="2">
            </div>
        </div>
    </div>
</form>

