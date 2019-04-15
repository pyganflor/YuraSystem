<form id="form_add_camion">
    <input type="hidden" id="id_camion" value="{{isset($data_camion->id_camion) ? $data_camion->id_camion : ""}}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" class="form-control" required autocomplete="off" value='{{isset($data_camion->modelo) ? $data_camion->modelo : ""}}'>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="placa">Placa</label>
                <input type="text" id="placa" name="placa" class="form-control" required autocomplete="off" value="{{isset($data_camion->placa) ? $data_camion->placa : ""}}">
            </div>
        </div>
    </div>
</form>
