<tr id="tr_nueva_especificacion_{{$cant_row+1}}">
    <td style="padding: 5px 0px;border-color: #9d9d9d">
        <select id="id_variedad_{{$cant_row+1}}" style="width: 100%;height: 25.8px;" name="id_variedad">
            {{--<option selected disabled>Seleccione</option>--}}
            @foreach($variedades as $v)
                <option value="{{$v->id_variedad}}">{{$v->nombre}}</option>
            @endforeach
        </select>
    </td>
    <td style="padding: 5px 0px;border-color: #9d9d9d">
        <select id="id_clasificacion_ramo_{{$cant_row+1}}" style="width: 100%;height: 25.8px;" name="id_clasificacion_ramo">
            {{--<option selected disabled>Seleccione</option>--}}
            @foreach($clasificacion_ramo as $c)
                <option value="{{$c->id_clasificacion_ramo}}">{{$c->nombre}}</option>
            @endforeach
        </select>
    </td>
    <td style="padding: 5px 0px;border-color: #9d9d9d">
        <select id="id_empaque_{{$cant_row+1}}" style="width: 100%;height: 25.8px;" name="id_empaque">
            {{--<option selected disabled>Seleccione</option>--}}
            @foreach($empaque as $e)
                <option value="{{$e->id_empaque}}">{{explode("|",$e->nombre)[0]}}</option>
            @endforeach
        </select>
    </td>
    <td style="padding: 5px 0px;border-color: #9d9d9d">
        <input type="text" placeholder="Cantidad" id="ramo_x_caja_{{$cant_row+1}}" style="width: 100%"  value="1" name="ramo_x_caja">
    </td>
    <td style="padding: 5px 0px;border-color: #9d9d9d">
        <select id="id_presentacion_{{$cant_row+1}}" style="width: 100%;height: 25.8px;" name="id_presentacion_{{$cant_row+1}}">
            {{--<option selected disabled>Seleccione</option>--}}
            @foreach($presentacion as $p)
                <option value="{{$p->id_empaque}}">{{$p->nombre}}</option>
            @endforeach
        </select>
    </td>
    <td style="padding: 5px 0px;border-color: #9d9d9d">
        <input type="text" placeholder="Cantidad" id="tallos_x_ramo_{{$cant_row+1}}" style="width: 100%" name="tallos_x_ramo">
    </td>
    <td style="padding: 5px 0px;border-color: #9d9d9d">
        <input type="text"  id="longitud_{{$cant_row+1}}" style="width: 50%" name="longitud">
        <select id="id_unidad_medida_{{$cant_row+1}}" name="id_unidad_medida" style="width: 48%;height: 25.8px;">
            {{--<option value="">Seleccione</option>--}}
            @foreach($unidad_medida as $u)
                <option value="{{$u->id_unidad_medida}}">{{$u->siglas}}</option>
            @endforeach
        </select>
    </td>
    <td id="td_btn_add_store_{{$cant_row+1}}" style="padding: 5px 0px;border-color: #9d9d9d" class="text-center">
    </td>
</tr>
