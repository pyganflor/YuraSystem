<div class="form-group">
    <label for="id_submenu">Submenú</label>
    <select name="id_submenu" id="id_submenu" required class="form-control">
        @if(count($submenus) > 0)
            <option value="">Seleccione</option>
            @foreach($submenus as $s)
                <option value="{{$s->id_submenu}}" class="options_submenu">{{$s->nombre}}</option>
            @endforeach
        @else
            <option value="">Nada que mostrar</option>
        @endif
    </select>
</div>
