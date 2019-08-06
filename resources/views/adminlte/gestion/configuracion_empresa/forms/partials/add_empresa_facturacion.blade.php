<form name="form_config_empresa" id="form_config_empresa" enctype="multipart/form-data">
    <div class="row">
        <input type="hidden" id="id_configuracion_empresa_facturacion" name="id_configuracion_empresa_facturacion" value="{{isset($config_empresa_facturacion->id_configuracion_empresa) ? $config_empresa_facturacion->id_configuracion_empresa : '' }}">
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Nombre comercial</span>
                <input type="text" id="nombre_empresa_facturacion" name="nombre_empresa_facturacion" class="form-control" required maxlength="300" minlength="3"
                       autocomplete="off" placeholder="Nombre de la Empresa"
                       value="{!! isset($config_empresa_facturacion->nombre) ? $config_empresa_facturacion->nombre : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Razón social</span>
                <input type="text" id="razon_social_empresa_facturacion" name="razon_social_empresa_facturacion" class="form-control" required maxlength="300" minlength="3"
                       autocomplete="off" placeholder="Razón social"
                       value="{!! isset($config_empresa_facturacion->razon_social) ? $config_empresa_facturacion->razon_social : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">País</span>
                <select id="codigo_pais_empresa_facturacion" name="codigo_pais_empresa_facturacion" class="form-control">
                    @foreach($paises as $pais)
                        <option {{ (isset($config_empresa_facturacion->codigo_pais) && $config_empresa_facturacion->codigo_pais == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Teléfono</span>
                <input type="text" id="telefono_empresa_facturacion" name="telefono_empresa_facturacion" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="1"
                       value="{!! isset($config_empresa_facturacion->telefono) ? $config_empresa_facturacion->telefono : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Imagen empresa</span>
                <input type="file" id="img_empresa_empresa_facturacion" name="img_empresa_empresa_facturacion" class="form-control" accept="image/*">
                {!! isset($config_empresa_facturacion->imagen) ? '<span class="text-info" style="position:absolute;left: 0;top: 33px;"><b>Ya se ha cargado la imagen</b></span>' : '' !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Correo</span>
                <input type="email" id="correo_empresa_facturacion" name="correo_empresa_facturacion" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="1"
                       value="{!! isset($config_empresa_facturacion->correo) ? $config_empresa_facturacion->correo : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Ruc</span>
                <input type="text" id="ruc_empresa_facturacion" name="ruc_empresa_facturacion" class="form-control" required maxlength="13"
                       autocomplete="off" minlength="13" required
                       value="{!! isset($config_empresa_facturacion->ruc) ? $config_empresa_facturacion->ruc : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Matriz</span>
                <input type="text" id="matriz_empresa_facturacion" name="matriz_empresa_facturacion" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="3"
                       value="{!! isset($config_empresa_facturacion->direccion_matriz) ? $config_empresa_facturacion->direccion_matriz : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Establecimiento</span>
                <input type="text" id="establecimiento_empresa_facturacion" name="establecimiento_empresa_facturacion" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="1"
                       value="{!! isset($config_empresa_facturacion->direccion_establecimiento) ? $config_empresa_facturacion->direccion_establecimiento : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">N°. Usuarios</span>
                <input type="text" id="cant_usuarios_empresa_facturacion" name="cant_usuarios_empresa_facturacion" class="form-control" maxlength="3"
                       autocomplete="off" placeholder="Ej. 5" onkeypress="return isNumber(event)" minlength="1" pattern="^([0-9])*$"
                       value="{!! isset($config_empresa_facturacion->cantidad_usuarios) ? $config_empresa_facturacion->cantidad_usuarios : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef" id="icono_moneda_empresa_facturacion">
                    {!! (isset($config_empresa_facturacion->moneda) && !empty($config_empresa_facturacion->moneda)) ? '<i class="fa fa-'.$config_empresa_facturacion->moneda.'"" aria-hidden="true"></i>' : '' !!}
                </span>
                <select class="form-control" id="moneda_empresa_facturacion"  name="moneda_empresa_facturacion" onchange="icono_moneda_empresa_facturacion()">
                    <option disabled selected>Seleccione</option>
                    @foreach($iconoMoneda as $moneda)
                        <option {{($config_empresa_facturacion->moneda === $moneda->nombre) ? 'selected' : ''}}
                            value="{{$moneda->nombre}}">
                            @if($moneda->nombre==='usd') {{'Dolar'}} @endif
                            @if($moneda->nombre==='try') {{'Lira turca'}} @endif
                            @if($moneda->nombre==='rub') {{'Rublo ruso'}} @endif
                            @if($moneda->nombre==='krw') {{'Won surcoreano'}} @endif
                            @if($moneda->nombre==='jpy') {{'Yen japonés'}} @endif
                            @if($moneda->nombre==='inr') {{'Rupia india'}} @endif
                            @if($moneda->nombre==='gbp') {{'Libra esterlina'}} @endif
                            @if($moneda->nombre==='eur') {{'Euros'}} @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Permiso agrocalidad</span>
                <input type="text" id="permiso_agrocalidad_empresa_facturacion" name="permiso_agrocalidad_empresa_facturacion" class="form-control" required maxlength="50"
                       autocomplete="off" minlength="3" value="{!! isset($config_empresa_facturacion->permiso_agrocalidad) ? $config_empresa_facturacion->permiso_agrocalidad : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Firma .P12</span>
                <input type="file" id="firma_electronica_empresa_facturacion" name="firma_electronica_empresa_facturacion" accept="application/x-pkcs12" class="form-control" {!! isset($config_empresa_facturacion->firma_electronica) ? '' : 'required' !!} >
                {!! !empty($config_empresa_facturacion->firma_electronica) ? '<span class="text-info" style="position:absolute;left: 0;top: 33px;"><b>Ya se ha cargado el archivo de la firma electrónica</b></span>' : '' !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Contraseña firma</span>
                <input type="text" id="contrasena_firma_digital_empresa_facturacion" name="contrasena_firma_digital_empresa_facturacion" class="form-control" required
                       value="{!! isset($config_empresa_facturacion->contrasena_firma_digital) ? $config_empresa_facturacion->contrasena_firma_digital : 0 !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef" >
                            Contabilidad <i class="fa fa-question-circle-o" title="Obligado a llevar contabilidad"></i>
                        </span>
                <select id="obligado_contabilidad_empresa_facturacion" name="obligado_contabilidad_empresa_facturacion" class="form-control">
                    <option value="1" {!! isset($config_empresa_facturacion->obligado_contabilidad) ? ($config_empresa_facturacion->obligado_contabilidad == true ? 'selected' : '') : '' !!}>SI</option>
                    <option value="0" {!! isset($config_empresa_facturacion->obligado_contabilidad) ? ($config_empresa_facturacion->obligado_contabilidad == false ? 'selected' : '') : '' !!}>NO</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial factura</span>
                <input type="number" id="inicial_factura_empresa_facturacion" min="0" name="inicial_factura_empresa_facturacion" class="form-control"
                       onkeypress="return isNumber(event)" minlength="1" pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa_facturacion->inicial_factura) ? $config_empresa_facturacion->inicial_factura : 0 !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial guía de remisión</span>
                <input type="number" id="inicial_guia_empresa_facturacion" name="inicial_guia_empresa_facturacion" class="form-control"
                       min="0" minlength="1"  pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa_facturacion->inicial_guia_remision) ? $config_empresa_facturacion->inicial_guia_remision : 0 !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial lote</span>
                <input type="text" id="inicial_lote_empresa_facturacion" name="inicial_lote_empresa_facturacion" class="form-control"
                       min="0" minlength="1"  pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa_facturacion->inicial_lote) ? $config_empresa_facturacion->inicial_lote : 0 !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial despacho</span>
                <input type="number" id="incial_despacho_empresa_facturacion" name="incial_despacho_empresa_facturacion" class="form-control"
                       min="0" minlength="1"  pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa_facturacion->inicial_despacho) ? $config_empresa_facturacion->inicial_despacho : 0 !!}">
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="button" class="btn btn-primary" onclick="store_configuracion_empresa_factura()">
                <i class="fa fa-floppy-o"></i> Guardar
            </button>
        </div>
    </div>
</form>
