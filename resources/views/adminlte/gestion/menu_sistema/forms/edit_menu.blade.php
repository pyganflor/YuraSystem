<form id="form_edit_menu">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>Ícono <i class="fa fa-fw fa-{{$menu->icono->nombre}}"></i></label>
                <ul class="list-group" style="overflow: scroll; height: 200px">
                    @foreach($iconos as $item)
                        <a href="javascript:void(0)" onclick="seleccionar_icono('{{$item->id_icono}}')"
                           class="list-group-item list-group-item-action {{$item->id_icono == $menu->id_icono ? 'active' : ''}}">
                            <i class="fa fa-fw fa-{{$item->nombre}}"></i>
                        </a>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="25" autocomplete="off"
                       value="{{$menu->nombre}}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="id_grupo_menu">Grupo</label>
                <select name="id_grupo_menu" id="id_grupo_menu" required class="form-control">
                    <option value="">Seleccione</option>
                    @foreach($grupos as $g)
                        <option value="{{$g->id_grupo_menu}}" {{$menu->id_grupo_menu == $g->id_grupo_menu ? 'selected' : ''}}>
                            {{$g->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_menu" name="id_menu" value="{{$menu->id_menu}}">
    <input type="hidden" id="id_icono" name="id_icono" value="{{$menu->id_icono}}">
</form>