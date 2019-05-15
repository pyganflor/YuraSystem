<form id="form_add_planta">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off"
                           value="{{$planta->nombre}}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="tarifa">HTS (Tarifa)</label>
                    <input type="text" id="tarifa" name="tarifa" class="form-control" required maxlength="50" autocomplete="off"
                           value="{{$planta->tarifa}}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nandina">Nandina</label>
                    <input type="text" id="nandina" name="nandina" class="form-control" required maxlength="50" autocomplete="off"
                           value="{{$planta->nandina}}" required>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="id_planta" name="id_planta" value="{{$planta->id_planta}}">
</form>
