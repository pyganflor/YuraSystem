<select name="id_agencia_carga_{{$pos}}" id="id_agencia_carga_{{$pos}}" required>
    @foreach($cliente->cliente_agencia_carga as $agencia)
        <option value="{{$agencia->id_agencia_carga}}">{{$agencia->agencia_carga->nombre}}</option>
    @endforeach
</select>