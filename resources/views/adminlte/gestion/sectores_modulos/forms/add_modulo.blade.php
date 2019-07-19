<form id="form_add_modulo">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="25" autocomplete="off">
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label for="id_sector">Grupo</label>
                <select name="id_sector" id="id_sector" required class="form-control">
                    <option value="">Seleccione</option>
                    @foreach($sectores as $item)
                        <option value="{{$item->id_sector}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Área <em>(m<sup>2</sup>)</em></label>
                <input type="number" id="area" name="area" class="form-control text-center" maxlength="11" min="0">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control text-justify contador" style="width: 100%"
                          maxlength="1000"></textarea>
            </div>
        </div>
    </div>
</form>

<script>
    inicializa('contador');
</script>