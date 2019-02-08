<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Listado de clasificaciones unitarias
        </h3>
    </div>
    <div class="box-body">
        <table class="table table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d; font-size: 0.8em">
            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                <th class="text-center" style="border-color: #9d9d9d">
                    Descripción
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Unidad de medida
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Clasificación estandar
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Clasificación real
                </th>
                <th class="text-center" style="border-color: #9d9d9d" width="10%">
                    Tallos x ramo
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Opciones
                </th>
            </tr>
            @foreach($unitarias as $unitaria)
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" id="nombre_{{$unitaria->id_clasificacion_unitaria}}" required class="text-center" maxlength="25"
                               name="nombre_{{$unitaria->id_clasificacion_unitaria}}" value="{{$unitaria->nombre}}" placeholder="nombre|factor">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select name="id_unidad_medida_{{$unitaria->id_clasificacion_unitaria}}" required
                                id="id_unidad_medida_{{$unitaria->id_clasificacion_unitaria}}"
                                onchange="seleccionar_unidad_medida('{{$unitaria->id_clasificacion_unitaria}}', 'estandar');seleccionar_unidad_medida('{{$unitaria->id_clasificacion_unitaria}}', 'real')">
                            @foreach(getUnidadesMedida() as $u)
                                <option value="{{$u->id_unidad_medida}}" {{$u->id_unidad_medida == $unitaria->id_unidad_medida ? 'selected' : ''}}>
                                    {{$u->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d"
                        id="td_clasificacion_estandar_{{$unitaria->id_clasificacion_unitaria}}">
                        Seleccione unidad de medida
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d"
                        id="td_clasificacion_real_{{$unitaria->id_clasificacion_unitaria}}">
                        Seleccione unidad de medida
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d"
                        id="td_clasificacion_real_{{$unitaria->id_clasificacion_unitaria}}">
                        <input type="number" id="tallos_x_ramo_{{$unitaria->id_clasificacion_unitaria}}" value="{{$unitaria->tallos_x_ramo}}"
                               name="tallos_x_ramo_{{$unitaria->id_clasificacion_unitaria}}" onkeypress="return isNumber(event)">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-success"
                                    onclick="update_unitaria('{{$unitaria->id_clasificacion_unitaria}}')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                        </div>
                    </td>
                    <script>
                        seleccionar_unidad_medida('{{$unitaria->id_clasificacion_unitaria}}', 'estandar');
                        seleccionar_unidad_medida('{{$unitaria->id_clasificacion_unitaria}}', 'real');
                    </script>
                </tr>
            @endforeach
        </table>
    </div>
</div>

<script>
    function update_unitaria(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_clasificacion_unitaria: id,
            nombre: $('#nombre_' + id).val(),
            id_unidad_medida: $('#id_unidad_medida_' + id).val(),
            id_clasificacion_ramo_estandar: $('#id_clasificacion_ramo_estandar_' + id).val(),
            id_clasificacion_ramo_real: $('#id_clasificacion_ramo_real_' + id).val(),
            tallos_x_ramo: $('#tallos_x_ramo_' + id).val(),
        };
        post_jquery('{{url('configuracion/update_unitaria')}}', datos, function () {
            cerrar_modals();
            admin_clasificacion_unitaria();
        })
    }

</script>