<tr>
    <td style="border:none">
        <input type="hidden" value="{{isset($dato_exportacion->id_dato_exportacion) ? $dato_exportacion->id_dato_exportacion : ""}}" id="id_dato_exportacion_{{$cant_rows+1}}">
        <label for="nombre">Nombre para el dato de exportación</label>
        <input type="text" id="nombre" name="nombre" class="form-control nombre_dato_exportacion"
               onkeypress="return guion_bajo_string(this,event)" value="{{isset($dato_exportacion->nombre) ? $dato_exportacion->nombre : ""}}"  minlength="2" required>
    </td>
</tr>
