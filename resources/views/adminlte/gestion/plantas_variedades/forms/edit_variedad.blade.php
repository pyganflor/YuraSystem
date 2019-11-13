<form id="form_edit_variedad">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="250" autocomplete="off"
                       value="{{$variedad->nombre}}">
            </div>
        </div>
    <!--  <div class="col-md-3">
            <div class="form-group">
                <label for="id_planta">Unidad de medida</label>
                <select id="unidad_medida" name="unidad_medida" class="form-control" required>
                    <option value="pl" {!! $variedad->unidad_de_medida == 'pl' ? 'selected= "selected"' : ''!!}>Pulgadas</option>
                    <option value="cm" {!! $variedad->unidad_de_medida == 'cm' ? 'selected= "selected"' : ''!!}>Centímetros</option>
                    <option value="m" {!! $variedad->unidad_de_medida ==  'm' ? 'selected= "selected"' : ''!!}>Metros</option>
                    <option value="km" {!! $variedad->unidad_de_medida == 'km' ? 'selected= "selected"' : ''!!}>Kilómetros</option>
                    <option value="gr" {!! $variedad->unidad_de_medida == 'gr' ? 'selected= "selected"' : ''!!}>Gramos</option>
                    <option value="kg" {!! $variedad->unidad_de_medida == 'kg' ? 'selected= "selected"' : ''!!}>Kilogramos</option>
                </select>
            </div>
        </div>-->
        <div class="col-md-3">
            <div class="form-group">
                <label for="id_planta">Planta</label>
                <select name="id_planta" id="id_planta" required class="form-control">
                    <option value="">Seleccione</option>
                    @foreach($plantas as $p)
                        <option value="{{$p->id_planta}}" {{$variedad->id_planta == $p->id_planta ? 'selected' : ''}}>
                            {{$p->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="siglas">Siglas</label>
                <input type="text" id="siglas" name="siglas" class="form-control" required maxlength="25" autocomplete="off"
                       value="{{$variedad->siglas}}">
            </div>
        </div>
        <div class="col-md-1" title="Color para los reportes">
            <div class="form-group">
                <label for="color">Color</label>
                <input type="color" id="color" name="color" class="form-control" required value="{{$variedad->color}}">
            </div>
        </div>
        <div class="col-md-2" title="Tipo">
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control" required>
                    <option value="P" {{$variedad->tipo == 'P' ? 'selected' : ''}}>Peso</option>
                    <option value="L" {{$variedad->tipo == 'L' ? 'selected' : ''}}>Longitud</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="minimo_apertura">Mínimo apertura</label>
                <input type="text" id="minimo_apertura" name="minimo_apertura" class="form-control" minlength="1"
                       maxlength="255" value="{{$variedad->minimo_apertura}}"
                       required onkeypress="return isNumber(event)">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="maximo_apertura">Máximo apertura</label>
                <input type="text" id="maximo_apertura" name="maximo_apertura" class="form-control" minlength="1"
                       maxlength="255" value="{{$variedad->maximo_apertura}}"
                       required onkeypress="return isNumber(event)">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="estandar">Estandar</label>
                <input type="text" id="estandar" name="estandar" class="form-control" minlength="1"
                       maxlength="255" value="{{$variedad->estandar_apertura}}" required onkeypress="return isNumber(event)">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="tallos_x_malla">Tallos por malla</label>
                <input type="number" id="tallos_x_malla" name="tallos_x_malla" class="form-control"
                       min="1" required onkeypress="return isNumber(event)" value="{{$variedad->tallos_x_malla}}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="tallos_x_ramo_estandar">Tallos por ramo</label>
                <input type="number" id="tallos_x_ramo_estandar" name="tallos_x_ramo_estandar" class="form-control"
                       min="1" onkeypress="return isNumber(event)" value="{{$variedad->tallos_x_ramo_estandar}}">
            </div>
        </div>
    </div>
    <legend class="text-center" style="font-size: 1em">Datos para proyecciones</legend>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="saldo_inicial">Saldo inicial</label>
                <input type="number" id="saldo_inicial" name="saldo_inicial" class="form-control" value="{{$variedad->saldo_inicial}}"
                       min="1">
            </div>
        </div>
    </div>

    <input type="hidden" id="id_variedad" name="id_variedad" value="{{$variedad->id_variedad}}">
</form>
