<form id="form_add_consignatario">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="nombre_completo">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off" value="{!! isset($dataConsignatario) ? $dataConsignatario->nombre : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="identificacion">Identificación</label>
                <input type="text" id="identificacion" name="identificacion" class="form-control"
                required autocomplete="off" value="{!! isset($dataConsignatario->identificacion) ? $dataConsignatario->identificacion : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="number" onkeypress="return isNumber()" id="telefono" name="telefono" class="form-control" required  maxlength="25" autocomplete="off" value="{!! isset($dataConsignatario->telefono) ? $dataConsignatario->telefono : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="pais">País</label>
                <select id="pais" name="pais" class="form-control" required>
                    <option selected disabled>Seleccione</option>
                    @foreach($dataPais as $pais)
                        <option {!! (isset($dataConsignatario->codigo_pais) ? ($dataConsignatario->codigo_pais == $pais->codigo) ? "selected" : "" : "") !!} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="provincia">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad" class="form-control" required  maxlength="255" autocomplete="off" value="{!! isset($dataConsignatario->ciudad) ? $dataConsignatario->ciudad : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" class="form-control" required  maxlength="255" autocomplete="off" value="{!! isset($dataConsignatario->correo) ? $dataConsignatario->correo : "" !!}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-control" value="{{ isset($dataConsignatario->direccion) ? $dataConsignatario->direccion : "" }}" required >
            </div>
        </div>
    </div>
    <hr style='margin-top:0; margin-bottom: 5px;' />
    <div class="row">
        <div class="col-md-6">
            <i class="fa fa-user-circle" ></i> Contacto del consignatario
        </div>
        <div class="col-md-6 text-right">
            <button type="button" class="btn btn-xs btn-primary" onclick="contacto_consignatario()" title="Contacto">
                <i class="fa fa-1x fa-address-card-o"></i>
            </button>
        </div>
    </div>
        <hr style='margin-top:5px'/>
    <div class="row">
        <div class="{{!isset($dataConsignatario->contacto_consignatario) ? 'hide' : ''}}" id="div_datos_consignatario">
            <input type="hidden" id="id_contacto_consignatario" name="id_contacto_consignatario" value="{{isset($dataConsignatario->contacto_consignatario->id_contacto_consignatario) ? $dataConsignatario->contacto_consignatario->id_contacto_consignatario : ''}}">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nombre_contacto">Nombre</label>
                    <input type="text" id="nombre_contacto" name="nombre_contacto" class="form-control" required maxlength="250" autocomplete="off" value="{!! isset($dataConsignatario->contacto_consignatario->nombre) ? $dataConsignatario->contacto_consignatario->nombre : "" !!}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="identificacion_contacto">Identificación</label>
                    <input type="text" id="identificacion_contacto" name="identificacion_contacto" class="form-control"
                           required autocomplete="off" value="{!! isset($dataConsignatario->contacto_consignatario->identificacion) ? $dataConsignatario->contacto_consignatario->identificacion : "" !!}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="telefono_contacto">Teléfono</label>
                    <input type="number" onkeypress="return isNumber()" id="telefono_contacto" name="telefono_contacto" class="form-control" required  maxlength="25" autocomplete="off" value="{!! isset($dataConsignatario->contacto_consignatario->telefono) ? $dataConsignatario->contacto_consignatario->telefono : "" !!}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="pais_contacto">País</label>
                    <select id="pais_contacto" name="pais_contacto" class="form-control" required>
                        <option selected disabled>Seleccione</option>
                        @foreach($dataPais as $pais)
                            <option {!! (isset($dataConsignatario->contacto_consignatario->codigo_pais) ? ($dataConsignatario->contacto_consignatario->codigo_pais == $pais->codigo) ? "selected" : "" : "") !!} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="ciudad_contacto">Ciudad</label>
                    <input type="text" id="ciudad_contacto" name="ciudad_contacto" class="form-control" required  maxlength="255" autocomplete="off" value="{!! isset($dataConsignatario->contacto_consignatario->ciudad) ? $dataConsignatario->contacto_consignatario->ciudad : "" !!}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="correo_contacto">Correo</label>
                    <input type="email" id="correo_contacto" name="correo_contacto" class="form-control" required  maxlength="255" autocomplete="off" value="{!! isset($dataConsignatario->contacto_consignatario->correo) ? $dataConsignatario->contacto_consignatario->correo : "" !!}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="direccion_contacto">Dirección</label>
                    <input type="text" id="direccion_contacto" name="direccion_contacto" class="form-control" value="{{ isset($dataConsignatario->contacto_consignatario->direccion) ? $dataConsignatario->contacto_consignatario->direccion : "" }}" required >
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button class="btn btn-success" type="button" id="btn_guardar_modal_add_consignatario" onclick="store_consignatario('{{isset($dataConsignatario->id_consignatario) ? $dataConsignatario->id_consignatario : ''}}')">
        <span class="bootstrap-dialog-button-icon fa fa-fw fa-save">
        </span>Guardar</button>
    </div>
</form>
