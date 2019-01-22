@if(count($cant_especificaciones) >0)
    @for($i=0;$i<count($cant_especificaciones);$i++)
        <tr id="tr_inputs_pedido_{{$i+1}}">
            <input type="hidden" id="id_detalle_pedido_{{$i+1}}" value="">
            <td>
                <input type="number" id="cantidad_{{$i+1}}" name="cantidad_{{$i+1}}" min="1" class="form-control" onkeypress="return isNumber(event)">
            </td>
            <td class="text-center">
                <ul>
                    @foreach($arr_data_cliente_especificacion[$i] as $key => $especificacion)
                    <li style="list-style: none;font-size:10pt">{{$especificacion->nombre_clasificacion_ramo." ". $especificacion->siglas_unidad_medida_longitud ." ".$especificacion->nombre_variedad." ".$especificacion->envoltura." ". $especificacion->presentacion ." ". $especificacion->tallos_x_ramos." tallos por ramo ". $especificacion->longitud_ramo." ". $especificacion->siglas_unidad_medida_longitud}} </li>
                        <input type="hidden" id="id_variedad_{{$i+1}}_{{$key+1}}" name="id_variedad" value="{{$especificacion->id_variedad}}">
                    @endforeach
                    <input type="hidden" id="id_especificacion_{{$i+1}}" name="id_especificacion_{{$i+1}}"  value="{{$arr_data_cliente_especificacion[$i][0]->id_cliente_pedido_especificacion}}">
                </ul>
               {{--<select id="id_especificacion_{{$i+1}}" name="id_especificacion_{{$i+1}}" class="form-control" {{$i+1 == 1 ? "required" : ""}}>
                    @foreach ($especificaciones as $especificacion)
                        <option value="{{$especificacion->id_cliente_pedido_especificacion}}">{{$especificacion->nombre}}</option>
                    @endforeach
                </select>--}}
            </td>
            <td>
                <select id="id_agencia_carga_{{$i+1}}" name="id_agencia_carga_{{$i+1}}" class="form-control" {{$i+1 == 1 ? "required" : ""}}>
                    @foreach($agenciasCarga as $agencias)
                        <option value="{{$agencias->id_agencia_carga}}">{{$agencias->nombre}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
    @endfor
@else
    <tr id="">
        <td colspan="3">
            <div class="alert alert-warning text-center">
                <p> Este usuario no posee especificaciones asignadas </p>
            </div>
        </td>
    </tr>
@endif
