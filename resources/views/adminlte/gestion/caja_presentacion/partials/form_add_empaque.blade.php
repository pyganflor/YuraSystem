<form id="form_add_empaque">
    <input type="hidden" id="id_empaque" value="">
    <div class="row">
        <div class="{{!isset($empaque->id_empaque) ? 'col-md-6' : 'col-md-12' }}">
            <div class="form-group">
                <label for="nombre_empaque">Nombre empaque</label>
                <input type="text" id="nombre_empaque" name="nombre_empaque" class="form-control" required maxlength="250" autocomplete="off" value='{{isset($empaque->nombre) ? $empaque->nombre : ""}}'>
                <input type="hidden" value="{{isset($empaque->id_empaque) ? $empaque->id_empaque : ""}}">
            </div>
        </div>
        @if(!isset($empaque->id_empaque))
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tipo_empaque">Tipo empaque</label>
                    <select id="tipo" name="tipo" class="form-control" >
                        <option value="C">Caja</option>
                        <option value="P">Presentación</option>
                    </select>
                </div>
            </div>
        @endif
    </div>
</form>
