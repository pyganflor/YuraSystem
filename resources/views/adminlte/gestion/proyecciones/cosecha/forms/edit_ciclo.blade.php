<legend style="font-size: 1em" class="text-center">
    <i class="fa fa-fw fa-info-circle"></i> Poda del módulo <strong>{{$modulo->nombre}}</strong> en la semana
    <strong>{{getSemanaByDate($ciclo->fecha_inicio)->codigo}}</strong>,
    variedad: <strong>{{$variedad->nombre}}</strong>
</legend>

@if($ciclo->activo == 1)
    @php
        $tallos_x_planta_default = $ciclo->poda_siembra == 'P' ? $ciclo->semana()->tallos_planta_poda : $ciclo->semana()->tallos_planta_siembra;
        $desecho_default = $ciclo->semana()->desecho;
        $area_default = $ciclo->area;
    @endphp
    <table class="table-bordered" style="width: 100%; border: 2px solid #9d9d9d;">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Tipo
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <select name="poda_siembra" id="poda_siembra" style="width: 100%" class="input-yura_white">
                    <option value="P" {{$ciclo->poda_siembra == 'P' ? 'selected' : ''}}>Poda</option>
                    <option value="S" {{$ciclo->poda_siembra == 'S' ? 'selected' : ''}}>Siembra</option>
                </select>
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Curva
                <input type="checkbox" class="pull-right" title="No recalcular curva"
                       id="no_recalcular_curva" {{$ciclo->no_recalcular_curva == 1 ? 'checked' : ''}}>
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="text" name="curva" id="curva" style="width: 100%" class="text-center input-yura_white" value="{{$ciclo->curva}}"
                       onchange="$('#no_recalcular_curva').prop('checked', true)">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Semana Cosecha
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="semana_poda_siembra" id="semana_poda_siembra" style="width: 100%" class="text-center input-yura_white"
                       value="{{$ciclo->semana_poda_siembra}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Plantas Iniciales
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="plantas_iniciales" id="plantas_iniciales" style="width: 100%" class="text-center input-yura_white"
                       value="{{$ciclo->plantas_iniciales}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Plantas Muertas
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="plantas_muertas" id="plantas_muertas" style="width: 100%" class="text-center input-yura_white"
                       value="{{$ciclo->plantas_muertas != '' ? $ciclo->plantas_muertas : 0}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                % Desecho
                <strong class="error" title="Dato correspondiente a la semana de inicio">{{$ciclo->desecho > 0 ? '' : '*'}}</strong>
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="desecho" id="desecho" style="width: 100%" class="text-center input-yura_white"
                       value="{{$ciclo->desecho > 0 ? $ciclo->desecho : $desecho_default}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Tallos x Planta
                <strong class="error" title="Dato correspondiente a la semana de inicio">{{$ciclo->conteo > 0 ? '' : '*'}}</strong>
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="conteo" id="conteo" style="width: 100%" class="text-center input-yura_white"
                       value="{{$ciclo->conteo > 0 ? $ciclo->conteo : $tallos_x_planta_default}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Área
                <strong class="error" title="Dato correspondiente al módulo">{{$ciclo->area > 0 ? '' : '*'}}</strong>
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="area" id="area" style="width: 100%" class="text-center input-yura_white"
                       value="{{$ciclo->area > 0 ? $ciclo->area : $area_default}}">
            </td>
        </tr>
    </table>

    <input type="hidden" id="id_ciclo" value="{{$ciclo->id_ciclo}}">
    <input type="hidden" id="modulo-edit_ciclo" value="{{$ciclo->id_modulo}}">

    <div class="text-center" style="margin-top: 10px">
        <button type="button" class="btn btn-yura_primary btn-xs" onclick="update_ciclo()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>

    <script>
        function update_ciclo() {
            datos = {
                _token: '{{csrf_token()}}',
                id_ciclo: $('#id_ciclo').val(),
                poda_siembra: $('#poda_siembra').val(),
                curva: $('#curva').val(),
                semana_poda_siembra: $('#semana_poda_siembra').val(),
                plantas_iniciales: $('#plantas_iniciales').val(),
                plantas_muertas: $('#plantas_muertas').val(),
                desecho: $('#desecho').val(),
                conteo: $('#conteo').val(),
                area: $('#area').val(),
                filtro_semana_hasta: $('#filtro_predeterminado_hasta').val(),
                no_recalcular_curva: $('#no_recalcular_curva').prop('checked')
            };
            mod = $('#modulo-edit_ciclo').val();

            post_jquery('{{url('proy_cosecha/update_ciclo')}}', datos, function () {
                get_row_byModulo(mod);
                cerrar_modals();
            });
        }
    </script>
@else
    <div class="well text-center">
        Este ciclo no se puede modificar debido a que ya se encuentra cerrado
    </div>
@endif