<form id="form_add_intervalo">
    <input type="hidden" id="id_indicador" value="{{$indicador}}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="desde">Desde</label>
                <input type="number" id="desde" name="desde" class="form-control" required maxlength="250"  min="0" autocomplete="off"
                       value=''>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input type="number" id="hasta" name="hasta" class="form-control" min="1" required
                       autocomplete="off" value="">
            </div>
        </div>
    </div>
</form>
