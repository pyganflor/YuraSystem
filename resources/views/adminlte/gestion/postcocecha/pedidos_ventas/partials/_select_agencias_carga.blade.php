@if(count($listado) > 0)
    <select name="id_agencia_carga" id="id_agencia_carga" class="form-control" required>
        @foreach($listado as $item)
            <option value="{{$item->id_agencia_carga}}">{{$item->agencia_carga->nombre}}</option>
        @endforeach
    </select>
@endif