<form id="form_edit_variedad">
    <div class="row">
        <div class="col-md-4">
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
        <div class="col-md-4">
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
        <div class="col-md-4">
            <div class="form-group">
                <label for="siglas">Siglas</label>
                <input type="text" id="siglas" name="siglas" class="form-control" required maxlength="25" autocomplete="off"
                       value="{{$variedad->siglas}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="id_planta">Mínio apertura</label>
                <input type="text" id="minimo_apertura" name="minimo_apertura" class="form-control" minlength="1"
                       maxlength="255" value="{{$variedad->minimo_apertura}}"
                       required onkeypress="return isNumber(event)">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="id_planta">Máximo apertura</label>
                <input type="text" id="maximo_apertura" name="maximo_apertura" class="form-control" minlength="1"
                       maxlength="255" value="{{$variedad->maximo_apertura}}"
                       required onkeypress="return isNumber(event)">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="id_planta">Estandar</label>
                <input type="text" id="estandar" name="estandar" class="form-control" minlength="1"
                       maxlength="255" value="{{$variedad->estandar_apertura}}" required onkeypress="return isNumber(event)">
            </div>
        </div>
    </div>

    <input type="hidden" id="id_variedad" name="id_variedad" value="{{$variedad->id_variedad}}">
</form>
