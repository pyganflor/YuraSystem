<form id="form_edit_sector">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group input-group">
                <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Nombre
                </span>
                <input type="text" id="nombre" name="nombre" class="form-control input-yura_default" required maxlength="250" autocomplete="off"
                       value="{{$sector->nombre}}" placeholder="Nombre">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group input-group">
                <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Interno
                </span>
                <select name="interno" id="interno" class="form-control input-yura_default">
                    <option value="1" {{$sector->interno == 1 ? 'selected' : ''}}>Sí</option>
                    <option value="0" {{$sector->interno == 0 ? 'selected' : ''}}>No</option>
                </select>
            </div>
        </div>
        <div class="col-md-10">
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control text-justify contador input-yura_default"
                          style="width: 100%" maxlength="1000" placeholder="Descripción">{{$sector->descripcion}}</textarea>
        </div>
    </div>

    <input type="hidden" id="id_sector" name="id_sector" value="{{$sector->id_sector}}">
</form>

<script>
    inicializa('contador');
</script>