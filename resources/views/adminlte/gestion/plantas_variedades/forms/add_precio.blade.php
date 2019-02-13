<form id="form_add_precio" name="form_add_precio" class="">
    <input id="id_variedad" value="" type="hidden">
    <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
        <thead>
        <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
            <th class="text-center" style="border-color: #9d9d9d">Clasificacion por ramo</th>
            <th class="text-center" style="border-color: #9d9d9d">
                Precio {!! (isset($moneda) && !empty($moneda)) ? '<i class="fa fa-'.$moneda->moneda.'"" aria-hidden="true"></i>' : '' !!}</th>
            <th style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-default" title="Añadir precio" onclick="add_inptus_precio()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </th>
        </tr>
        <tbody id="precios">
        @if(isset($dataPrecio) && !empty($dataPrecio))
            @foreach($dataPrecio as $key => $precio)
                <tr id="precios_{{$key+1}}">
                    <td>
                        <select id="id_clasificacion_por_ramo_{{$key+1}}" class="form-control" required
                                onchange="comprabar('{{$key+1}}')" {!! $precio->estado == 0 ? 'disabled' : '' !!}>
                            <option disabled selected> Seleccione</option>
                            @foreach($dataClasificacionRamos as $clasificacionRamos)
                                @php
                                    $selected='';
                                        if($clasificacionRamos->id_clasificacion_ramo === $precio->id_clasificacion_ramo){
                                        $selected='selected=selected';
                                        }
                                    @endphp
                                <option {{$selected}} value="{{$clasificacionRamos->id_clasificacion_ramo}}">{{$clasificacionRamos->nombre." ".$clasificacionRamos->siglas}}</option>
                                @endforeach
                        </select>
                    </td>
                    <td>
                        <input {!! $precio->estado == 0 ? 'disabled' : '' !!} type="text" id="precio_{{$key+1}}" name="precio_{{$key+1}}"
                               value="{{$precio->cantidad}}" onkeypress="return isNumber(event)" required minlength="1"
                               onkeyup="comprabar('{{$key+1}}')" class="form-control">
                    </td>
                    <td>
                        <input type="hidden" id="id_precio_{{$key+1}}" value="{{$precio->id_precio}}">
                        <button type="button" class="btn btn-xs btn-{!! $precio->estado == 0 ? 'success' : 'warning' !!}"
                                title="Desactivar campo" onclick="actualizar_status_precio('{{$precio->id_precio}}','{{$precio->estado}}')">
                            <i class="fa fa-{!! $precio->estado == 0 ? 'check' : 'ban' !!}" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        @endif()
        </tbody>
    </table>
</form>






