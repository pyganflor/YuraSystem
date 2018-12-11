<form id="form_add_cliente">
    <input type="hidden" id="id_cliente" value="{!! !empty($dataCliente) ? $dataCliente->id_cliente : $dataCliente !!}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre_completo">Nombre</label>
                <input type="text" id="nombre" name="nombreo" class="form-control" required maxlength="250" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->nombre : $dataCliente !!}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="identificacion">Identificación</label>
                <input type="text" id="identificacion" name="identificacion" class="form-control" required maxlength="25" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->ruc : $dataCliente !!}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
            <div class="form-group">
                <label for="provincia">Provincia</label>
                <input type="text" id="provincia" name="provincia" class="form-control" required  maxlength="255" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->provincia : $dataCliente !!}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" class="form-control" required  maxlength="255" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->correo : $dataCliente !!}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="number" onkeypress="isNumber()" id="telefono" name="telefono" class="form-control" required  maxlength="25" autocomplete="off" value="{!! !empty($dataCliente) ? $dataCliente->telefono : $dataCliente !!}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea  rows="5"  id="direccion" name="direccion" class="form-control" required  maxlength="500" autocomplete="off" >{!! !empty($dataCliente) ? $dataCliente->direccion : $dataCliente !!}</textarea>
            </div>
        </div>
    </div>
    <button class="btn btn-success" type="button" id="btn_guardar_modal_add_cliente" onclick="store_cliente()"><span class="bootstrap-dialog-button-icon fa fa-fw fa-save"></span>Guardar</button>
</form>