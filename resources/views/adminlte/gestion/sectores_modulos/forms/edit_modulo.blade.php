<form id="form_edit_modulo">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="25" autocomplete="off"
                       value="{{$modulo->nombre}}">
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label for="id_sector">Sector</label>
                <select name="id_sector" id="id_sector" required class="form-control">
                    <option value="">Seleccione</option>
                    @foreach($sectores as $item)
                        <option value="{{$item->id_sector}}" {{$modulo->id_sector == $item->id_sector ? 'selected' : ''}}>
                            {{$item->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="nombre">Área <em>(m<sup>2</sup>)</em></label>
                <input type="number" id="area" name="area" class="form-control text-center" maxlength="11" min="1"
                       value="{{$modulo->area}}">
            </div>
        </div>
        <div class="col-md-10">
            <div class="form-group">
                <label for="nombre">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control text-justify contador" style="width: 100%"
                          maxlength="1000">{{$modulo->descripcion}}</textarea>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_modulo" name="id_modulo" value="{{$modulo->id_modulo}}">
</form>