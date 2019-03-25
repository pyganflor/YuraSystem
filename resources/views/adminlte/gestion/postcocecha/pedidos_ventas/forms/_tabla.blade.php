@foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
    <legend style="font-size: 1em; margin-bottom: 0">
        <strong>Distribución EMP-{{$pos_esp_emp + 1}}</strong>
    </legend>
    <div style="overflow-x: scroll">
        <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d">
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="150px">
                    Color
                </th>
                @foreach($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                    <th class="text-center elemento_color_{{$color->id_color}}"
                        style="border-color: #9d9d9d; background-color: {{getColor($color->id_color)->fondo}}"
                        width="100px">
                        <select name="color_{{$color->id_color}}" id="color_{{$color->id_color}}">
                            @foreach(getColores() as $c)
                                <option value="{{$c->id_color}}" {{$c->id_color == $color->id_color ? 'selected' : ''}}>
                                    {{$c->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id_color_{{$pos_color}}" name="id_color_{{$pos_color}}" value="{{$color->id_color}}">
                    </th>
                @endforeach
                @if(count($esp_emp->detalles) > 1)
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="100px">
                        Parcial
                    </th>
                @endif
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                    Total
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white" width="60px">
                    Piezas
                </th>
            </tr>
            @foreach($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['marcaciones'] as $pos_marca => $marca)
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="nombre_marcacion_{{$marca->id_marcacion}}" name="nombre_marcacion_{{$marca->id_marcacion}}"
                               value="{{getMarcacion($marca->id_marcacion)->nombre}}" width="150px" style="border: none" class="text-center">
                        <input type="hidden" id="nombre_marcacion_{{$pos_marca}}" name="nombre_marcacion_{{$pos_marca}}"
                               value="{{$marca->id_marcacion}}">
                    </td>
                    @foreach($det_ped->getDistinctMarcacionesColoracionesByEspEmp($esp_emp->id_especificacion_empaque)['coloraciones'] as $pos_color => $color)
                        <th class="text-center elemento_color_{{$color->id_color}}"
                            style="border-color: #9d9d9d; background-color: {{getColor($color->id_color)->fondo}}"
                            width="100px">

                        </th>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
@endforeach