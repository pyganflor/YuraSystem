<form id="form_add_cliente_factura_tercero">
    @php  $disabled=""; $facturado == true ? $disabled="disabled" : "" @endphp
    <input type="hidden" id="id_factura_cliente_tercero" value="{{isset($dataCliente->id_factura_cliente_tercero) ? $dataCliente->id_factura_cliente_tercero : "" }}">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="nombre_completo">Nombre</label>
                <input type="text" id="nombre_cliente_tercero" name="nombre_cliente_tercero" class="form-control" required maxlength="250" {{$disabled}}
                       autocomplete="off" value="{!! isset($dataCliente->nombre_cliente_tercero) ? $dataCliente->nombre_cliente_tercero : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="identificacion">Tipo de identificación</label>
                <select class="form-control" id="tipo_identificacion" name="tipo_identificacion" onchange="valida_identificacion()" {{$disabled}} required>
                    <option disabled selected>Seleccione</option>
                    @foreach($dataTipoIdentificacion as $dTI)
                        <option {{isset($dataCliente->codigo_identificacion) ? ($dataCliente->codigo_identificacion == $dTI->codigo ? "selected" : "") : ""}}
                                value="{{$dTI->codigo}}">{{$dTI->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="identificacion">Número de identificación</label>
                <input type="text" id="identificacion" name="identificacion" class="form-control" {{$disabled}}
                    {{(!empty($dataCliente) && $dataCliente->identificacion == 07) ? "disabled" : ""}}
                    required autocomplete="off" value="{{isset($dataCliente->identificacion) ? $dataCliente->identificacion : ""}}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="provincia">Ciudad</label>
                <input type="text" id="provincia_cliente_tercero" name="provincia_cliente_tercero" class="form-control" required  maxlength="255"
                       {{$disabled}} autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->provincia : "" !!}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="codigo_impuesto">Impuesto</label>
                <select id="codigo_impuesto" name="codigo_impuesto" class="form-control" required {{$disabled}} onchange="porcentaje_impuesto()">
                    <option selected disabled>Seleccione</option>
                    @foreach($impuestos as $impuesto)
                        <option {{ isset($dataCliente->codigo_impuesto) ? ($dataCliente->codigo_impuesto == $impuesto->codigo ? "selected" : "") : ""}}
                            value="{{$impuesto->codigo}}">{{$impuesto->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="tipo_impuesto">Tipo de impuesto</label>
                <select id="tipo_impuesto" name="tipo_impuesto" class="form-control" {{$disabled}} required>
                    <option selected disabled>Seleccione</option>
                        @foreach($tipoImpuestos as $tipoImpuesto)
                            <option {{ isset($dataCliente->codigo_impuesto_porcentaje) ? ($dataCliente->codigo_impuesto_porcentaje == $tipoImpuesto->codigo ? "selected" : "") : ""}}
                                     value="{{$tipoImpuesto->codigo}}" id="dinamic">{{$tipoImpuesto->descripcion}}
                            </option>
                        @endforeach
                </select>
                <input type="hidden" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="pais_cliente_tercero">País</label>
                <select id="pais_cliente_tercero" name="pais_cliente_tercero" class="form-control"
                         onchange="buscar_codigo_dae(this,'form_add_cliente_factura_tercero',true)" {{$disabled}} required>
                    <option selected disabled>Seleccione</option>
                    @foreach($dataPais as $pais)
                        <option {{ isset($dataCliente->codigo_pais) ? ($dataCliente->codigo_pais == $pais->codigo ? "selected" : "") : ""}}
                            value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" id="correo_cliente_tercero" name="correo_cliente_tercero" class="form-control" required  maxlength="255"
                       {{$disabled}} autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->correo : "" !!}">
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="dae">CÓDIGO DAE</label>
                <input type="text"  id="codigo_dae_cliente_tercero" name="codigo_dae_cliente_tercero" class="form-control" {{$disabled}}
                required autocomplete="off" value="{{isset($dataCliente->codigo_dae) ? $dataCliente->codigo_dae : ""}}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="dae">DAE</label>
                <input type="text" id="dae_cliente_tercero" name="dae_cliente_tercero" class="form-control" {{$disabled}}
                required autocomplete="off" value="{{isset($dataCliente->dae) ? $dataCliente->dae : ""}}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="number" onkeypress="return isNumber()" id="telefono_cliente_tercero" name="telefono_cliente_tercero" class="form-control"
                       {{$disabled}} required  maxlength="25" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->telefono : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="puerto_entrada">Puerto de entrada</label>
                <input type="text" id="puerto_entrada" name="puerto_entrada" class="form-control"   maxlength="100" autocomplete="off"
                       {{$disabled}} value="{!! !empty($dataCliente) ? $dataCliente->puerto_entrada : "" !!}" required>
            </div>
        </div>
    </div>
    <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="almacen_cliente_tercero">Anden</label>
                    <input type="text" id="almacen_cliente_tercero" name="almacen_cliente_tercero" class="form-control"   maxlength="100" autocomplete="off"
                           {{$disabled}} value="{!! !empty($dataCliente) ? $dataCliente->almacen : "" !!}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo_credito">Tiempo de crédito</label>
                    <input type="text" id="tipo_credito" name="tipo_credito" class="form-control"   maxlength="100" autocomplete="off"
                           {{$disabled}} value="{!! !empty($dataCliente) ? $dataCliente->tipo_credito : "" !!}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="direccion_cliente_tercero">Direccion</label>
                    <input type="text" id="direccion_cliente_tercero" name="direccion_cliente_terceroireccion" class="form-control"   maxlength="100" autocomplete="off"
                           {{$disabled}} value="{!! !empty($dataCliente) ? $dataCliente->direccion : "" !!}" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="marca">Marca de caja</label>
                    <select name="marca" id="marca" class="form-control">
                        @foreach($marcas as $marca)
                            <option {!! !empty($dataCliente) ? ($dataCliente->id_marca === $marca->id_marca ? "selected" : "") : "" !!} value="{{$marca->id_marca}}">{{$marca->nombre}} </option>
                        @endforeach
                    </select>
                </div>
            </div>
    </div>

</form>
<script>
    valida_identificacion();
    if('{{$facturado}}' == true)
        $("#btn_guardar_modal_factura_cliente_tercero").remove();
</script>
