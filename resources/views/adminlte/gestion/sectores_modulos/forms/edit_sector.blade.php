<form id="form_edit_sector">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off"
                       value="{{$sector->nombre}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="nombre">Área <em>(hectáreas)</em></label>
                <input type="number" id="area" name="area" class="form-control text-center" maxlength="11" min="1" max="500"
                       value="{{$sector->area}}">
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <label for="nombre">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control text-justify contador" style="width: 100%"
                          maxlength="1000">{{$sector->descripcion}}</textarea>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_sector" name="id_sector" value="{{$sector->id_sector}}">
</form>

<script>
    inicializa('contador');
</script>