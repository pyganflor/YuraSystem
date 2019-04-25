<form id="form_add_conductor">
    <input type="hidden" id="id_conductor" value="{{isset($data_conductor->id_conductor) ? $data_conductor->id_conductor : ""}}">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required autocomplete="off" value='{{isset($data_conductor->nombre) ? $data_conductor->nombre : ""}}'>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="tipo_identificacion">Tipo de identificación</label>
                <select class="form-control" id="tipo_identificacion" name="tipo_identificacion" onchange="valida_identificacion()" required>
                    <option disabled selected>Seleccione</option>
                    @foreach($dataTipoIdentificacion as $dTI)
                        @php
                            $selected ='';
                            if(!empty($data_conductor)){
                                if($data_conductor->tipo_identificacion == $dTI->codigo){
                                    $selected = 'selected=selected';
                                }
                            }
                        @endphp
                        <option {{$selected}} value="{{$dTI->codigo}}">{{$dTI->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="identificacion">Identificación</label>
                <input type="number" id="identificacion" name="identificacion" class="form-control" required autocomplete="off" value="{{isset($data_conductor->ruc) ? $data_conductor->ruc : ""}}">
            </div>
        </div>
    </div>
</form>

