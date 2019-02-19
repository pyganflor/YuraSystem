<div class="row">
    <div class="col-md-3">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Ramos</span>
            <input type="number" onkeypress="return isNumber(event)" id="cantidad_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                   name="cantidad_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                   class="form-control"
                   maxlength="3" max="999" min="1" value="1" required placeholder="Escriba la cantidad">
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Variedad</span>
            <select name="id_variedad_{{$pos_form_detalles}}_{{$cant_desgloses}}" id="id_variedad_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    class="form-control" required>
                <option value="">Seleccione</option>
                @foreach($variedades as $variedad)
                    <option value="{{$variedad->id_variedad}}">{{$variedad->planta->nombre}}
                        - {{$variedad->siglas}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Clasificación</span>
            <select name="id_clasificacion_ramo_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    id="id_clasificacion_ramo_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    class="form-control" required>
                <option value="">Seleccione</option>
                @foreach($pesajes as $pesaje)
                    <option value="{{$pesaje->id_clasificacion_ramo}}">
                        {{$pesaje->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        {{--<div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Envoltura</span>
            <select name="id_empaque_e_{{$pos_form_detalles}}_{{$cant_desgloses}}" id="id_empaque_e_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    class="form-control"
                    required>
                <option value="">Seleccione</option>
                @foreach($envolturas as $envoltura)
                    <option value="{{$envoltura->id_empaque}}">{{$envoltura->nombre}}</option>
                @endforeach
            </select>
        </div>--}}
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Presentación</span>
            <select name="id_empaque_p_{{$pos_form_detalles}}_{{$cant_desgloses}}" id="id_empaque_p_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    class="form-control"
                    required>
                <option value="">Seleccione</option>
                @foreach($presentaciones as $presentacion)
                    <option value="{{$presentacion->id_empaque}}">{{$presentacion->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>