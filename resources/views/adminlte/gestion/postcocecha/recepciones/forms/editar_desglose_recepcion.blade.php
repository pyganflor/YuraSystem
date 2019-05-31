<form id="form-update_desglose">
    <table class="table-striped table-responsive table-bordered" width="100%" style="border: 2px solid #9d9d9d;">
        <tr>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white">
                Módulo
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white">
                Variedad
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: white; width: 90px">
                Cantidad Mallas
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: white; width: 90px">
                Tallos x Mallas
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: white; width: 90px">
                Opciones
            </th>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d">
                <select name="id_modulo_desglose" id="id_modulo_desglose" style="width: 100%" required>
                    @foreach(getModulos() as $item)
                        <option value="{{$item->id_modulo}}" {{$item->id_modulo == $desglose->id_modulo ? 'selected' : ''}}>
                            {{$item->nombre}}
                        </option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <select name="id_variedad_desglose" id="id_variedad_desglose" style="width: 100%" required>
                    @foreach(getVariedades() as $item)
                        <option value="{{$item->id_variedad}}" {{$item->id_variedad == $desglose->id_variedad ? 'selected' : ''}}>
                            {{$item->nombre}}
                        </option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" id="cantidad_mallas_desglose" name="cantidad_mallas_desglose" required style="width: 100%"
                       value="{{$desglose->cantidad_mallas}}"
                       class="text-center" onkeypress="return isNumber(event)">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" id="tallos_x_malla_desglose" name="tallos_x_malla_desglose" required style="width: 100%"
                       value="{{$desglose->tallos_x_malla}}"
                       class="text-center" onkeypress="return isNumber(event)">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-success" title="Guardar"
                            onclick="update_desglose('{{$desglose->id_desglose_recepcion}}')">
                        <i class="fa fa-fw fa-save"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" title="Eliminar"
                            onclick="delete_desglose('{{$desglose->id_desglose_recepcion}}')">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </div>
            </th>
        </tr>
    </table>
</form>

<input type="hidden" id="id_recepcion_desglose" value="{{$desglose->id_recepcion}}">