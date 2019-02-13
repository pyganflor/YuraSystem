<div class="form-group">
    <label for="id_menu">Menú</label>
    <select name="id_menu" id="id_menu" required class="form-control" onchange="listar_submenus_x_menu($(this).val())">
        @if(count($menus) > 0)
            <option value="">Seleccione</option>
            @foreach($menus as $m)
                @if($m->estado == 'A')
                <option value="{{$m->id_menu}}">{{$m->nombre}}</option>
                @endif
            @endforeach
        @else
            <option value="">Seleccione un grupo</option>
        @endif
    </select>
</div>
