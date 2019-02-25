<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Listado de clasificaciones de ramos
        </h3>
    </div>
    <div class="box-body" style="overflow-x: scroll">
        <table class="table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d; font-size: 0.8em;">
            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                <th class="text-center" style="border-color: #9d9d9d">
                    Descripción
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Unidad de medida
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Opciones
                </th>
            </tr>
            @foreach($ramos as $ramo)
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" onkeypress="return isNumber(event)" required id="nombre_{{$ramo->id_clasificacion_ramo}}"
                               name="nombre_{{$ramo->id_clasificacion_ramo}}" value="{{$ramo->nombre}}" class="text-center" style="width: 100%">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <select id="id_unidad_medida_{{$ramo->id_clasificacion_ramo}}" style="width: 100%"
                                name="id_unidad_medida_{{$ramo->id_clasificacion_ramo}}" required>
                            @foreach($unidades as $unidad)
                                <option value="{{$unidad->id_unidad_medida}}" {{$unidad->id_unidad_medida == $ramo->id_unidad_medida ? 'selected' : ''}}>
                                    {{$unidad->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-success"
                                    onclick="update_ramo('{{$ramo->id_clasificacion_ramo}}')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

<script>
    function update_ramo(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_clasificacion_ramo: id,
            nombre: $('#nombre_' + id).val(),
            id_unidad_medida: $('#id_unidad_medida_' + id).val(),
        };
        post_jquery('{{url('configuracion/update_ramo')}}', datos, function () {
            cerrar_modals();
            admin_clasificacion_ramo();
        });
    }
</script>