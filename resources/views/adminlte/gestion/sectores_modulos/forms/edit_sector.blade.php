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
        <div class="col-md-2">
            <div class="form-group">
                <label for="interno">Interno</label>
                <select name="interno" id="interno" class="form-control">
                    <option value="1" {{$sector->interno == 1 ? 'selected' : ''}}>Sí</option>
                    <option value="0" {{$sector->interno == 0 ? 'selected' : ''}}>No</option>
                </select>
            </div>
        </div>
        <div class="col-md-10">
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