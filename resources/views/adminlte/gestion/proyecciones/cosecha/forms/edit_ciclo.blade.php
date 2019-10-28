<legend style="font-size: 1em" class="text-center">
    <i class="fa fa-fw fa-info-circle"></i> Poda del módulo <strong>{{$modulo->nombre}}</strong> en la semana
    <strong>{{getSemanaByDate($ciclo->fecha_inicio)->codigo}}</strong>,
    variedad: <strong>{{$variedad->nombre}}</strong>
</legend>

@if($ciclo->activo == 1)
    <table class="table-bordered" style="width: 100%; border: 2px solid #9d9d9d;">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Tipo
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <select name="poda_siembra" id="poda_siembra" style="width: 100%">
                    <option value="P" {{$ciclo->poda_siembra == 'P' ? 'selected' : ''}}>Poda</option>
                    <option value="S" {{$ciclo->poda_siembra == 'S' ? 'selected' : ''}}>Siembra</option>
                </select>
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Curva
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="text" name="curva" id="curva" style="width: 100%" class="text-center" value="{{$ciclo->curva}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Semana Cosecha
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="semana_poda_siembra" id="semana_poda_siembra" style="width: 100%" class="text-center"
                       value="{{$ciclo->semana_poda_siembra}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Plantas Iniciales
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="plantas_iniciales" id="plantas_iniciales" style="width: 100%" class="text-center"
                       value="{{$ciclo->plantas_iniciales}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                % Desecho
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="desecho" id="desecho" style="width: 100%" class="text-center"
                       value="{{$ciclo->desecho}}">
            </td>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Tallos x Planta
            </th>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" name="conteo" id="conteo" style="width: 100%" class="text-center"
                       value="{{$ciclo->conteo}}">
            </td>
        </tr>
    </table>

    <input type="hidden" id="id_ciclo" value="{{$ciclo->id_ciclo}}">
    <input type="hidden" id="modulo-edit_ciclo" value="{{$ciclo->id_modulo}}">

    <div class="text-center" style="margin-top: 10px">
        <button type="button" class="btn btn-success btn-xs" onclick="update_ciclo()">
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
                desecho: $('#desecho').val(),
                conteo: $('#conteo').val(),
                filtro_semana_hasta: $('#filtro_predeterminado_hasta').val(),
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