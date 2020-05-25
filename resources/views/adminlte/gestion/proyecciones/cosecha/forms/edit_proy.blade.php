<legend style="font-size: 1em" class="text-center">
    <i class="fa fa-fw fa-info-circle"></i> Programación para el módulo <strong>{{$modulo->nombre}}</strong> en la semana
    <strong>{{$proyeccion->semana->codigo}}</strong>,
    variedad: <strong>{{$variedad->nombre}}</strong>
</legend>

@php
    $tallos_x_planta_default = $proyeccion->tipo == 'P' ? $proyeccion->semana->tallos_planta_poda : $proyeccion->semana->tallos_planta_siembra;
    $desecho_default = $proyeccion->semana->desecho;
    $tallos_x_ramo_default = $proyeccion->tipo == 'P' ? $proyeccion->semana->tallos_ramo_poda : $proyeccion->semana->tallos_ramo_siembra;
    $plantas_iniciales_default = $last_ciclo != '' ? $last_ciclo->plantas_iniciales : 0;
    $area_default = $last_ciclo != '' ? $last_ciclo->area : 0;
@endphp
<table class="table-bordered" style="width: 100%; border: 2px solid #9d9d9d;">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Tipo
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <select name="tipo" id="tipo" style="width: 100%" class="input-yura_white">
                <option value="P" {{$proyeccion->tipo == 'P' ? 'selected' : ''}}>Poda</option>
                <option value="S" {{$proyeccion->tipo == 'S' ? 'selected' : ''}}>Siembra</option>
                <option value="C" {{$proyeccion->tipo == 'C' ? 'selected' : ''}}>Finalizar</option>
            </select>
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #495054; color: white">
            Fecha inicio
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="date" name="fecha_inicio" id="fecha_inicio" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->fecha_inicio}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #495054; color: white">
            Fecha fin anterior
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="date" name="fecha_fin" id="fecha_fin" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->fecha_inicio}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #00b388; color: white" colspan="2">
            <button type="button" class="btn btn-xs btn-block btn-yura_primary" onclick="store_nuevo_ciclo()">
                <i class="fa fa-fw fa-check"></i> Crear nuevo ciclo
            </button>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Semana Inicio
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" onkeypress="return isNumber(event)" name="semana" id="semana" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->semana->codigo}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Curva
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="text" name="curva" id="curva" style="width: 100%" class="text-center input-yura_white" value="{{$proyeccion->curva}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Semana Cosecha
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="semana_poda_siembra" id="semana_poda_siembra" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->semana_poda_siembra}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Plantas Iniciales
            <strong class="error" title="Dato correspondiente al ciclo anterior">{{$proyeccion->plantas_iniciales > 0 ? '' : '*'}}</strong>
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="plantas_iniciales" id="plantas_iniciales" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->plantas_iniciales > 0 ? $proyeccion->plantas_iniciales : $plantas_iniciales_default}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            % Desecho
            <strong class="error" title="Dato correspondiente a la semana de inicio">{{$proyeccion->desecho > 0 ? '' : '*'}}</strong>
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="desecho" id="desecho" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->desecho > 0 ? $proyeccion->desecho : $desecho_default}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Tallos x Planta
            <strong class="error" title="Dato correspondiente a la semana de inicio">{{$proyeccion->tallos_planta > 0 ? '' : '*'}}</strong>
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="tallos_planta" id="tallos_planta" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->tallos_planta > 0 ? $proyeccion->tallos_planta : $tallos_x_planta_default}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Tallos x Ramo
            <strong class="error" title="Dato correspondiente a la semana de inicio">{{$proyeccion->tallos_ramo > 0 ? '' : '*'}}</strong>
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="tallos_ramo" id="tallos_ramo" style="width: 100%" class="text-center input-yura_white"
                   value="{{$proyeccion->tallos_ramo > 0 ? $proyeccion->tallos_ramo : $tallos_x_ramo_default}}">
        </td>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Área
        </th>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" name="area" id="area" style="width: 100%" class="text-center input-yura_white" value="{{$area_default}}" readonly>
        </td>
    </tr>
</table>

<input type="hidden" id="id_proyeccion_modulo" value="{{$proyeccion->id_proyeccion_modulo}}">
<input type="hidden" id="semana_actual" value="{{$proyeccion->semana->codigo}}">
<input type="hidden" id="modulo-edit_proy" value="{{$proyeccion->id_modulo}}">

<div class="text-center" style="margin-top: 10px">
    <button type="button" class="btn btn-yura_primary btn-xs" onclick="update_proyeccion()">
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
            area: $('#area').val(),
            semana_poda_siembra: $('#semana_poda_siembra').val(),
            plantas_iniciales: $('#plantas_iniciales').val(),
            desecho: $('#desecho').val(),
            tallos_planta: $('#tallos_planta').val(),
            tallos_ramo: $('#tallos_ramo').val(),
            semana_actual: $('#semana_actual').val(),
        };
        mod = $('#modulo-edit_proy').val();
        post_jquery('{{url('proy_cosecha/update_proyeccion')}}', datos, function () {
            get_row_byModulo(mod);
            cerrar_modals();
        });
    }

    function store_nuevo_ciclo() {
        datos = {
            _token: '{{csrf_token()}}',
            id_modulo: $('#modulo-edit_proy').val(),
            id_proyeccion_modulo: $('#id_proyeccion_modulo').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val(),
            poda_siembra: $('#tipo').val(),
            area: $('#area').val(),
            plantas_iniciales: $('#plantas_iniciales').val(),
            curva: $('#curva').val(),
            conteo: $('#tallos_planta').val(),
            semana_poda_siembra: $('#semana_poda_siembra').val(),
            desecho: $('#desecho').val(),
        };
        if (datos['fecha_inicio'] <= datos['fecha_fin']) {
            modal_quest('modal-quest_store_nuevo_ciclo', '<div class="alert alert-warning text-center">¿Estás seguro de cerrar el ciclo anterior y crear uno nuevo?</div>',
                '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de confirmación', true, false, '', function () {
                    post_jquery('{{url('proy_cosecha/store_nuevo_ciclo')}}', datos, function () {
                        cerrar_modals();
                        restaurar_proyeccion(datos['id_modulo']);
                        //get_row_byModulo(datos['id_modulo']);
                    });
                });
        } else {
            alerta_errores('La fecha inicio debe ser menor o igual que la fecha fin.');
        }
    }
</script>