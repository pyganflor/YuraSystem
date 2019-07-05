@for ($x=1;$x<=$filas;$x++)
    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
        <td style="border-color: #9d9d9d;width: 100px;" class="text-center">
            <select id="empaque_{{$x}}" name="empaque_{{$x}}" style="width: 100%;border:none">
                @foreach ($empaque as $e)
                    <option>{{explode("|",$e->nombre)[0]}}</option>
                @endforeach
            </select>
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="number" id="cajas_{{$x}}" name="cajas_{{$x}}" style="width: 100%;border:none">
        </td>
        <td style="border-color: #9d9d9d;width: 200px;" class="text-center">
            <select id="descripcion_{{$x}}" name="descripcion_{{$x}}" style="width: 100%;border:none">
                <option>Seleccione</option>
            </select>
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="siglas_{{$x}}" name="siglas_{{$x}}" style="width: 100%;border:none">
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="et_inicial_{{$x}}" name="et_inicial_{{$x}}" style="width: 100%;border:none">
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <input type="text" id="et_final_{{$x}}" name="et_final_{{$x}}" style="width: 100%;border:none">
        </td>
    </tr>
@endfor
