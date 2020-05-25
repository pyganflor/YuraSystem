<form id="form_edit_modulo">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Nombre
                </span>
                <input type="text" id="nombre" name="nombre" class="form-control input-yura_default" required maxlength="25" autocomplete="off"
                       value="{{$modulo->nombre}}" placeholder="Nombre">
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group input-group">
                <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Sector
                </span>
                <select name="id_sector" id="id_sector" required class="form-control input-yura_default">
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
        <div class="col-md-6">
            <div class="form-group input-group">
                <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Área <em>(m<sup>2</sup>)</em>
                </span>
                <input type="number" id="area" name="area" class="form-control text-center input-yura_default" maxlength="11" min="0"
                       value="{{$modulo->area}}" placeholder="Área">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control text-justify contador input-yura_default"
                          style="width: 100%" maxlength="1000" placeholder="Descripción">{{$modulo->descripcion}}</textarea>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_modulo" name="id_modulo" value="{{$modulo->id_modulo}}">
</form>