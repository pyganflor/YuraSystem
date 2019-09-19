<legend style="font-size: 1em" class="text-center">
    <i class="fa fa-fw fa-info-circle"></i> Proyección para el módulo <strong>{{$modulo->nombre}}</strong> en la semana
    <strong>{{$proyeccion->semana->codigo}}</strong>,
    variedad: <strong>{{$variedad->nombre}}</strong>
</legend>

<table class="table-bordered" style="width: 100%; border: 2px solid #9d9d9d;">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Tipo
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <select name="tipo" id="tipo" style="width: 100%">
                <option value="P" {{$proyeccion->tipo == 'P' ? 'selected' : ''}}>Poda</option>
                <option value="S" {{$proyeccion->tipo == 'S' ? 'selected' : ''}}>Siembra</option>
                <option value="C" {{$proyeccion->tipo == 'C' ? 'selected' : ''}}>Finalizar</option>
            </select>
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Semana Inicio
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" onkeypress="return isNumber(event)" name="semana" id="semana" style="width: 100%" class="text-center"
                   value="{{$proyeccion->semana->codigo}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Curva
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="text" name="curva" id="curva" style="width: 100%" class="text-center" value="{{$proyeccion->curva}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Semana Cosecha
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="semana_poda_siembra" id="semana_poda_siembra" style="width: 100%" class="text-center"
                   value="{{$proyeccion->semana_poda_siembra}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Plantas Iniciales
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="plantas_iniciales" id="plantas_iniciales" style="width: 100%" class="text-center"
                   value="{{$proyeccion->plantas_iniciales}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            % Desecho
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="desecho" id="desecho" style="width: 100%" class="text-center"
                   value="{{$proyeccion->desecho}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Tallos x Planta
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="tallos_planta" id="tallos_planta" style="width: 100%" class="text-center"
                   value="{{$proyeccion->tallos_planta}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Tallos x Ramo
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="tallos_ramo" id="tallos_ramo" style="width: 100%" class="text-center"
                   value="{{$proyeccion->tallos_ramo}}">
        </td>
    </tr>
</table>

<input type="hidden" id="id_proyeccion_modulo" value="{{$proyeccion->id_proyeccion_modulo}}">
<input type="hidden" id="semana_actual" value="{{$proyeccion->semana->codigo}}">

<div class="text-center" style="margin-top: 10px">
    <button type="button" class="btn btn-success btn-xs" onclick="update_proyeccion()">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>
</div>

<script>
    function update_proyeccion() {
        datos = {
            _token: '{{csrf_token()}}',
            id_proyeccion_modulo: $('#id_proyeccion_modulo').val(),
            tipo: $('#tipo').val(),
            semana: $('#semana').val(),
            curva: $('#curva').val(),
            semana_poda_siembra: $('#semana_poda_siembra').val(),
            plantas_iniciales: $('#plantas_iniciales').val(),
            desecho: $('#desecho').val(),
            tallos_planta: $('#tallos_planta').val(),
            tallos_ramo: $('#tallos_ramo').val(),
            semana_actual: $('#semana_actual').val(),
        };

        post_jquery('{{url('proy_cosecha/update_proyeccion')}}', datos, function () {
            listar_proyecciones();
            cerrar_modals();
        });
    }
</script>