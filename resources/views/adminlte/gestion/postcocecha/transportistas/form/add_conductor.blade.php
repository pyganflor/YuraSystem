<form id="form_add_camion">
    <input type="hidden" id="id_conductor" value="{{isset($data_conductor->id_conductor) ? $data_conductor->id_conductor : ""}}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required autocomplete="off" value='{{isset($data_conductor->nombre) ? $data_conductor->nombre : ""}}'>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="ruc">ruc</label>
                <input type="text" id="ruc" name="ruc" class="form-control" required autocomplete="off" value="{{isset($data_conductor->ruc) ? $data_conductor->ruc : ""}}">
            </div>
        </div>
    </div>
</form>

