<div class="row">
    <div class="col-md-3">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Variedad</span>
            <select name="id_variedad_{{$pos_form_detalles}}_{{$cant_desgloses}}" id="id_variedad_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    class="form-control" required>
                <option value="">Seleccione</option>
                @foreach($variedades as $variedad)
                    <option value="{{$variedad->id_variedad}}">{{$variedad->planta->nombre}} - {{$variedad->siglas}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Calibre</span>
            <select name="id_clasificacion_ramo_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    id="id_clasificacion_ramo_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    class="form-control" required>
                <option value="">Seleccione</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Ramos por caja</span>
            <input type="number" onkeypress="return isNumber(event)" id="cantidad_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                   name="cantidad_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                   class="form-control"
                   maxlength="3" max="999" min="1" value="1" required placeholder="Escriba la cantidad">
        </div>
    </div>
    <div class="col-md-3">
        {{--<div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">T. Ud. medida</span>
            <select name="tipo_unidad_medida_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    id="unidad_medida_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                    class="form-control" onchange="tipo_unidad_medida(this.id,'{{csrf_token()}}')" required>
                <option value="P">Peso</option>
                <option value="L">Longitud</option>

            </select>
        </div>
        <div class="form-group input-group">
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
<div class="row" id="input_tallo_x_ramo">
    <div class="col-md-4">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Tallos por ramos </span>
            <input type="number" onkeypress="return isNumber(event)" id="tallos_x_ramo_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                   name="tallos_x_ramos_{{$pos_form_detalles}}_{{$cant_desgloses}}"
                   class="form-control" maxlength="3" max="999" min="1" placeholder="Cantidad" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef"> longitud ramo </span>
            <input type="number" min="1" onkeypress="return isNumber(event)" class="form-control"
                   id="long_ramo_{{$pos_form_detalles}}_{{$cant_desgloses}}" name="long_ramo_{{$pos_form_detalles}}_{{$cant_desgloses}}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Ud. medida </span>
            <select class="form-control" name="id_ud_medida_{{$pos_form_detalles}}_{{$cant_desgloses}}" id="id_ud_medida_{{$pos_form_detalles}}_{{$cant_desgloses}}">
                {{--<option disabled selected>Seleccione</option>--}}
                @foreach ($unidad_medida as $um)
                    <option value="{{$um->id_unidad_medida}}">{{$um->siglas}}</option>
                @endforeach
            </select>
        </div>
    </div>
    {{--<div class="col-md-4">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef"> Grosor </span>
            <select class="form-control" name="id_grosor_{{$pos_form_detalles}}_{{$cant_desgloses}}" id="id_grosor_{{$pos_form_detalles}}_{{$cant_desgloses}}">
                <option disabled selected>Seleccione</option>
                @foreach ($grosor as $g)
                    <option value="{{$g->id_grosor_ramo}}">{{$g->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>--}}
</div>
<script>tipo_unidad_medida('unidad_medida_{{$pos_form_detalles}}_{{$cant_desgloses}}','{{csrf_token()}}');</script>
