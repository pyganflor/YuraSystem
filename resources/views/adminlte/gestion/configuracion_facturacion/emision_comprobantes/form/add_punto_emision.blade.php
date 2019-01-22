<form id="form_comprobante_emision" name="form_comprobante_emision">
    @for($i=1;$i<=$cant_punto_emision;$i++)
    <div class="col-md-4" style="padding: 10px 5px">
        <div class="input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Punto de emisión</span>
            <input type="text" class="form-control" name="punto_emision" id="punto_emision{{$i}}"
                   value="{{str_pad($i,3,"0",STR_PAD_LEFT)}}" required disabled>
        </div>
    </div>
    <div class="col-md-8" style="padding: 10px 0">
        <div class="input-group" style="margin-bottom: 10px;">
            <span class="input-group-addon" style="background-color: #e9ecef">Usuario</span>
            <select class="form-control" id="id_usuario_{{$i}}" name="id_usuario_{{$i}}" required>
                <option disabled selected>Seleccione</option>
                @foreach($usuario as $u)
                    <option value="{{$u->id_usuario}}">{{$u->nombre_completo}}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endfor
</form>
