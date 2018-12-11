<div class="form-group">
    <label for="id_modulo">Módulo</label>
    <select name="id_modulo" id="id_modulo" required class="form-control">
        @if(count($modulos) > 0)
            <option value="">Seleccione</option>
            @foreach($modulos as $item)
                <option value="{{$item->id_modulo}}">{{$item->nombre}}</option>
            @endforeach
        @else
            <option value="">Seleccione un sector</option>
        @endif
    </select>
</div>