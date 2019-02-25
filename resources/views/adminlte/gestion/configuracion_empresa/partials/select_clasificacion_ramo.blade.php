<select name="id_clasificacion_ramo_{{$campo}}_{{$unitaria->id_clasificacion_unitaria}}" style="width: 100%" class="text-center"
        id="id_clasificacion_ramo_{{$campo}}_{{$unitaria->id_clasificacion_unitaria}}" required>
    @foreach($ramos as $ramo)
        @if($campo == 'real')
            <option value="{{$ramo->id_clasificacion_ramo}}"
                    {{$unitaria->id_clasificacion_ramo_real == $ramo->id_clasificacion_ramo ? 'selected' : ''}}>
                {{$ramo->nombre}} {{$ramo->unidad_medida->siglas}}
            </option>
        @else
            <option value="{{$ramo->id_clasificacion_ramo}}"
                    {{$unitaria->id_clasificacion_ramo_estandar == $ramo->id_clasificacion_ramo ? 'selected' : ''}}>
                {{$ramo->nombre}} {{$ramo->unidad_medida->siglas}}
            </option>
        @endif
    @endforeach
</select>