@foreach($clasificaciones as $pos_c => $c)
    <tr>
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="text" placeholder="Mesa" id="mesa_{{$pos_c}}"
                   style="width: 100%; background-color: {{explode('|', $c->clasificacion_unitaria->color)[0]}}; color: {{explode('|', $c->clasificacion_unitaria->color)[1]}}">
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: {{explode('|', $c->clasificacion_unitaria->color)[0]}}; color: {{explode('|', $c->clasificacion_unitaria->color)[1]}}">
            {{explode('|', $c->clasificacion_unitaria->nombre)[0]}} {{$c->clasificacion_unitaria->unidad_medida->siglas}}
            <input type="hidden" id="id_unitaria_{{$pos_c}}" value="{{$c->id_clasificacion_unitaria}}">
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="text" class="text-center" id="ramos_{{$pos_c}}"
                   style="width: 100%; background-color: {{explode('|', $c->clasificacion_unitaria->color)[0]}}; color: {{explode('|', $c->clasificacion_unitaria->color)[1]}}">
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="text" class="text-center" id="tallos_x_ramo_{{$pos_c}}"
                   style="width: 100%; background-color: {{explode('|', $c->clasificacion_unitaria->color)[0]}}; color: {{explode('|', $c->clasificacion_unitaria->color)[1]}}">
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: {{explode('|', $c->clasificacion_unitaria->color)[0]}}; color: {{explode('|', $c->clasificacion_unitaria->color)[1]}}"
            colspan="2">
            <span class="badge" id="total_{{$pos_c}}">0</span>
        </th>
    </tr>
@endforeach