<form id="form_add_planta">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off"
                       value="{{$planta->nombre}}">
            </div>
        </div>
    </div>
    <input type="hidden" id="id_planta" name="id_planta" value="{{$planta->id_planta}}">
</form>