<form id="form_edit_grupo_menu">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off"
                       value="{{$grupo->nombre}}">
            </div>
        </div>
    </div>
    <input type="hidden" id="id_grupo_menu" name="id_grupo_menu" value="{{$grupo->id_grupo_menu}}">
</form>