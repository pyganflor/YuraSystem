<form id="form_add_cliente">
    <input type="hidden" id="id_cliente" value="{!! !empty($dataCliente) ? $dataCliente->id_cliente : "" !!}">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="nombre_completo">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->nombre : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="tipo_identificacion">Tipo de identificación</label>
                <select class="form-control" id="tipo_identificacion" name="tipo_identificacion" onchange="valida_identificacion()" required>
                    <option disabled selected>Seleccione</option>
                    @foreach($dataTipoIdentificacion as $dTI)
                        @php
                            $selected ='';
                            if(!empty($dataCliente)){
                                if($dataCliente->codigo_identificacion == $dTI->codigo){
                                    $selected = 'selected=selected';
                                }
                            }
                        @endphp
                        <option {{$selected}} value="{{$dTI->codigo}}">{{$dTI->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="identificacion">Número de identificación</label>
                <input type="text" id="identificacion" name="identificacion" class="form-control" {{(!empty($dataCliente) && $dataCliente->codigo_identificacion == 07) ? "disabled" : ""}}
                       required autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->ruc : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="number" onkeypress="return isNumber()" id="telefono" name="telefono" class="form-control" required  maxlength="25" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->telefono : "" !!}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="pais">Impuesto</label>
                <select id="codigo_impuesto" name="codigo_impuesto" class="form-control" required onchange="porcentaje_impuesto()">
                    <option selected disabled>Seleccione</option>
                    @foreach($impuestos as $impuesto)
                        @php
                            $selected ='';
                            if(!empty($dataCliente)){
                                if($dataCliente->codigo_impuesto == $impuesto->codigo){
                                    $selected = 'selected=selected';
                                }
                            }
                        @endphp
                        <option {{$selected}} value="{{$impuesto->codigo}}">{{$impuesto->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="pais">Tipo de impuesto</label>
                <select id="tipo_impuesto" name="tipo_impuesto" class="form-control" required>
                    <option selected disabled>Seleccione</option>
                    @if(isset($tipoImpuestos) && !empty($tipoImpuestos))
                        @foreach($tipoImpuestos as $tipoImpuesto)
                            @php
                                $selected ='';
                                if(!empty($dataCliente)){
                                    if($dataCliente->codigo_impuesto == $tipoImpuesto->codigo){
                                        $selected = 'selected=selected';
                                    }
                                }
                            @endphp
                            <option {{$selected}} value="{{$tipoImpuesto->codigo}}">{{$tipoImpuesto->descripcion}}</option>
                        @endforeach
                    @endif
                </select>
                <input type="hidden" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="pais">País</label>
                <select id="pais" name="pais" class="form-control" required>
                    <option selected disabled>Seleccione</option>
                    @foreach($dataPais as $pais)
                        @php
                            $selected ='';
                            if(!empty($dataCliente)){
                                if($dataCliente->codigo_pais === $pais->codigo){
                                    $selected = 'selected=selected';
                                }
                            }
                        @endphp
                        <option {{$selected}} value="{{$pais->codigo}}">{{$pais->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="provincia">Ciudad</label>
                <input type="text" id="provincia" name="provincia" class="form-control" required  maxlength="255" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->provincia : "" !!}">
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" class="form-control" required  maxlength="255" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->correo : "" !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="puerto_entrada">Puerto de entrada</label>
                <input type="text" id="puerto_entrada" name="puerto_entrada" class="form-control" value="{!! !empty($dataCliente) ? $dataCliente->puerto_entrada : "" !!}" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="tipo_credito">Tiempo de crédito</label>
                <input type="text" id="tipo_credito" name="tipo_credito" class="form-control" value="{!! !empty($dataCliente) ? $dataCliente->tipo_credito : "" !!}" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="marca">Marca de caja</label>
                <select name="marca" id="marca" class="form-control">
                    @foreach($marcas as $marca)
                        <option {!! !empty($dataCliente) ? ($dataCliente->id_marca=== $marca->id_marca ? "selected" : "") : "" !!} value="{{$marca->id_marca}}">{{$marca->nombre}} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="almacen">Anden</label>
                <input type="text" id="almacen" name="almacen" class="form-control"   maxlength="100" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->almacen : "" !!}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea  rows="5"  id="direccion" name="direccion" class="form-control" required  maxlength="500" autocomplete="off" >{!! !empty($dataCliente) ? $dataCliente->direccion : "" !!}</textarea>
            </div>
        </div>
    </div>
    <div class="text-center">
    <button class="btn btn-success" type="button" id="btn_guardar_modal_add_cliente" onclick="store_cliente('{{csrf_token()}}')">
        <span class="bootstrap-dialog-button-icon fa fa-fw fa-save">
        </span>Guardar</button>
    </div>
</form>
