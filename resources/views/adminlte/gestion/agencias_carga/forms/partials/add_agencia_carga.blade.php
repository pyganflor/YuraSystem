<form id="form_add_agencia_carga" name="form_add_agencia_carga">
    <input type="hidden" value="{!! isset($dataAgencia->id_agencia_carga) ? $dataAgencia->id_agencia_carga : '' !!}" id="id_agencia_carga">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Nombre de la agencia de carga</label>
                <input type="text" id="nombre_agencia" name="nombre_agencia" class="form-control" required maxlength="25" autocomplete="off" value="{!! isset($dataAgencia->nombre) ? $dataAgencia->nombre : '' !!}"  required="" minlength="3">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="identificacion">identificación</label>
                <input type="text" id="identificacion" name="identificacion" class="form-control" required maxlength="20" autocomplete="off" value="{!! isset($dataAgencia->identificacion) ? $dataAgencia->identificacion : '' !!}"  required="" minlength="1">
            </div>
        </div>
        {{--<div class="col-md-6">
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" class="form-control" required autocomplete="off" value="{!! isset($dataAgencia->correo) ? $dataAgencia->correo : '' !!}"  required="" minlength="2">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="correo2">Correo</label>
                <input type="email" id="correo2" name="correo2" class="form-control" autocomplete="off" value="{!! isset($dataAgencia->correo2) ? $dataAgencia->correo2 : '' !!}"   minlength="2">
            </div>
        </div>--}}
    </div>
    <div class="row" >
        <div class="col-md-12">
            <i class="fa fa-plus" ></i> Agregar códigos venture
            <hr />
        </div>
        @for($x=0;$x<count($empresas); $x++)
            <div class="codigos_venture">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="codigo">Empresa</label>
                        <select id="id_configuracion_empresa" name="id_configuracion_empresa" class="form-control">
                            <option value="" >Seleccione</option>
                            @foreach($empresas as $empresa)
                                <option {{ isset($dataAgencia->codigo_venture_agencia_carga[$x]) ? ($dataAgencia->codigo_venture_agencia_carga[$x]->id_configuracion_empresa == $empresa->id_configuracion_empresa) ? 'Selected' : ''  : ""}}
                                        value="{{$empresa->id_configuracion_empresa}}">{{$empresa->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="codigo">Código venture</label>
                        <input type="text" id="codigo" name="codigo"
                               value="{{isset($dataAgencia->codigo_venture_agencia_carga[$x]->codigo) ? $dataAgencia->codigo_venture_agencia_carga[$x]->codigo : ""}}"
                               class="form-control"  autocomplete="off" value="">
                    </div>
                </div>
            </div>
        @endfor
    </div>
</form>

