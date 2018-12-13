<div class="form-group">
    <label for="id_menu">Menú</label>
    <select name="id_menu" id="id_menu" required class="form-control">
        @if(count($menus) > 0)
            <option value="">Seleccione</option>
            @foreach($menus as $m)
                <option value="{{$m->id_menu}}">{{$m->nombre}}</option>
            @endforeach
        @else
            <option value="">Seleccione un grupo</option>
        @endif
    </select>
</div>