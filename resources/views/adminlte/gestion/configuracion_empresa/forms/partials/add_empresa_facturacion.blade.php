<form name="form_config" id="form_config" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="id_config"
           value="{!! isset($config_empresa->id_configuracion_empresa) ? $config_empresa->id_configuracion_empresa : '' !!}">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Nombre comercial</span>
                <input type="text" id="nombre_empresa" name="nombre_empresa" class="form-control" required maxlength="300" minlength="3"
                       autocomplete="off" placeholder="Nombre de la Empresa"
                       value="{!! isset($config_empresa->nombre) ? $config_empresa->nombre : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Razón social</span>
                <input type="text" id="razon_social" name="razon_social" class="form-control" required maxlength="300" minlength="3"
                       autocomplete="off" placeholder="Razón social"
                       value="{!! isset($config_empresa->razon_social) ? $config_empresa->razon_social : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">País</span>
                <select id="codigo_pais" name="codigo_pais" class="form-control">
                    @foreach($paises as $pais)
                        <option {{ ($config_empresa->codigo_pais == $pais->codigo) ? "selected" : "" }} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Teléfono</span>
                <input type="text" id="telefono" name="telefono" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="1"
                       value="{!! isset($config_empresa->telefono) ? $config_empresa->telefono : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Fax</span>
                <input type="text" id="fax" name="fax" class="form-control" maxlength="300"
                       autocomplete="off" minlength="1"
                       value="{!! isset($config_empresa->fax) ? $config_empresa->fax : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Correo</span>
                <input type="email" id="correo" name="correo" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="1"
                       value="{!! isset($config_empresa->correo) ? $config_empresa->correo : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Ruc</span>
                <input type="text" id="ruc" name="ruc" class="form-control" required maxlength="13"
                       autocomplete="off" minlength="13" required
                       value="{!! isset($config_empresa->ruc) ? $config_empresa->ruc : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Matriz</span>
                <input type="text" id="matriz" name="matriz" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="3"
                       value="{!! isset($config_empresa->direccion_matriz) ? $config_empresa->direccion_matriz : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Establecimiento</span>
                <input type="text" id="establecimiento" name="establecimiento" class="form-control" required maxlength="300"
                       autocomplete="off" minlength="1"
                       value="{!! isset($config_empresa->direccion_establecimiento) ? $config_empresa->direccion_establecimiento : '' !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">N°. Usuarios</span>
                <input type="text" id="cant_usuarios" name="cant_usuarios" class="form-control" maxlength="3"
                       autocomplete="off" placeholder="Ej. 5" onkeypress="return isNumber(event)" minlength="1" pattern="^([0-9])*$"
                       value="{!! isset($config_empresa->cantidad_usuarios) ? $config_empresa->cantidad_usuarios : '' !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Hectareas</span>
                <input type="text" id="hectarea" name="hectarea" class="form-control" maxlength="20"
                       autocomplete="off" placeholder="Ej. 1.00" minlength="1" pattern="^-?\d+(?:.\d+)?$"
                       value="{!! isset($config_empresa->cantidad_hectareas) ? $config_empresa->cantidad_hectareas : '' !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef" id="icono_moneda">
                        {!! (isset($config_empresa->moneda) && !empty($config_empresa->moneda)) ? '<i class="fa fa-'.$config_empresa->moneda.'"" aria-hidden="true"></i>' : '' !!}
                    </span>
                <select class="form-control" id="moneda" onchange="icono_moneda()">
                    <option disabled selected>Seleccione</option>
                    @foreach($iconoMoneda as $moneda)
                        @php
                            $config_empresa->moneda === $moneda->nombre ? $selected = 'selected=selected' : $selected = '';
                        @endphp
                        <option value="{{$moneda->nombre}}" {{$selected}}>
                            @if($moneda->nombre==='usd')  {{'Dolar'}} @endif
                            @if($moneda->nombre==='try')  {{'Lira turca'}} @endif
                            @if($moneda->nombre==='rub')  {{'Rublo ruso'}} @endif
                            @if($moneda->nombre==='krw')  {{'Won surcoreano'}} @endif
                            @if($moneda->nombre==='jpy')  {{'Yen japonés'}} @endif
                            @if($moneda->nombre==='inr')  {{'Rupia india'}} @endif
                            @if($moneda->nombre==='gbp')  {{'Libra esterlina'}} @endif
                            @if($moneda->nombre==='eur')  {{'Euros'}} @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef" >
                        Contabilidad <i class="fa fa-question-circle-o" title="Obligado a llevar contabilidad"></i>
                    </span>
                <select id="obligado_contabilidad" name="obligado_contabilidad" class="form-control">
                    <option value="1" {!! isset($config_empresa->obligado_contabilidad) ? ($config_empresa->obligado_contabilidad == true ? 'selected' : '') : '' !!}>SI</option>
                    <option value="0" {!! isset($config_empresa->obligado_contabilidad) ? ($config_empresa->obligado_contabilidad == false ? 'selected' : '') : '' !!}>NO</option>
                </select>Imagen
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Permiso agrocalidad</span>
                <input type="text" id="permiso_agrocalidad" name="permiso_agrocalidad" class="form-control" required maxlength="50"
                       autocomplete="off" minlength="3" value="{!! isset($config_empresa->permiso_agrocalidad) ? $config_empresa->permiso_agrocalidad : '' !!}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Firma .P12</span>
                <input type="file" id="firma_electronica" name="firma_electronica" accept="application/x-pkcs12" class="form-control" {!! isset($config_empresa->firma_electronica) ? '' : 'required' !!} >
                {!! isset($config_empresa->firma_electronica) ? '<span class="text-info" style="position:absolute;left: 0;top: 33px;"><b>Ya se ha cargado el archivo de la firma electrónica</b></span>' : '' !!}
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Imagen</span>
                <input type="file" id="img_empresa" name="img_empresa" class="form-control" accept="image/*">
                {!! isset($config_empresa->imagen) ? '<span class="text-info" style="position:absolute;left: 0;top: 33px;"><b>Ya se ha cargado la imagen</b></span>' : '' !!}
            </div>
        </div>



        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial factura</span>
                <input type="number" id="cant_usuarios" min="1" name="cant_usuarios" class="form-control"
                       onkeypress="return isNumber(event)" minlength="1" pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa->inicial_factura) ? $config_empresa->inicial_factura : '' !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial guía</span>
                <input type="text" id="inicial_guia" name="inicial_guia" class="form-control"
                       min="1" minlength="1"  pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa->inicial_guia) ? $config_empresa->inicial_guia : '' !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial lote</span>
                <input type="text" id="inicial_lote" name="inicial_lote" class="form-control"
                       min="1" minlength="1"  pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa->inicial_lote) ? $config_empresa->inicial_lote : '' !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Inicial despacho</span>
                <input type="text" id="incial_despacho" name="incial_despacho" class="form-control"
                       min="1" minlength="1"  pattern="^([0-9])*$" required
                       value="{!! isset($config_empresa->inicial_despacho) ? $config_empresa->inicial_despacho : '' !!}">
            </div>
        </div>
    </div>
</form>
