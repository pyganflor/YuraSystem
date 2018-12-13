<form id="form_edit_lote">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="id_sector">Sector</label>
                <select name="id_sector" id="id_sector" required class="form-control"
                        onchange="listar_modulos_x_sector($(this).val())">
                    <option value="">Seleccione</option>
                    @foreach($sectores as $item)
                        <option value="{{$item->id_sector}}" {{$lote->modulo->id_sector == $item->id_sector ? 'selected' : ''}}>
                            {{$item->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6" id="div_input_modulos">
            <div class="form-group">
                <label for="id_modulo">Menú</label>
                <select name="id_modulo" id="id_modulo" required class="form-control">
                    <option value="{{$lote->id_modulo}}">{{$lote->modulo->nombre}}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="50" autocomplete="off"
                       value="{{$lote->nombre}}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="nombre">Área <em>(hectáreas)</em></label>
                <input type="number" id="area" name="area" class="form-control text-center" maxlength="11" min="1" max="500"
                       value="{{$lote->area}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="nombre">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control text-justify contador" style="width: 100%"
                          maxlength="1000">{{$lote->descripcion}}</textarea>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_lote" name="id_lote" value="{{$lote->id_lote}}">
</form>

<script>
    inicializa('contador');
</script>