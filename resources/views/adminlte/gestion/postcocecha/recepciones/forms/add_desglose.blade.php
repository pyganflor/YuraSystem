<form id="form-store_desglose">
    <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d;">
        <tr>
            <th class="text-center th_yura_default" style="border-color: white">
                Módulo
            </th>
            <th class="text-center th_yura_default" style="border-color: white">
                Variedad
            </th>
            <th class="text-center th_yura_default" style="border-color: white; width: 90px">
                Cantidad Mallas
            </th>
            <th class="text-center th_yura_default" style="border-color: white; width: 90px">
                Tallos x Mallas
            </th>
            <th class="text-center th_yura_default" style="border-color: white; width: 90px">
                Opciones
            </th>
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d">
                <select name="id_modulo_desglose" class="select-yura_default" id="id_modulo_desglose" style="width: 100%" required>
                    @foreach(getModulos() as $item)
                        <option value="{{$item->id_modulo}}">
                            {{$item->nombre}}
                        </option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <select name="id_variedad_desglose" class="select-yura_default" id="id_variedad_desglose" style="width: 100%" required>
                    @foreach(getVariedades() as $item)
                        <option value="{{$item->id_variedad}}">
                            {{$item->nombre}}
                        </option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" id="cantidad_mallas_desglose" name="cantidad_mallas_desglose" required style="width: 100%"
                       value="" class="text-center input-yura_white" onkeypress="return isNumber(event)">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" id="tallos_x_malla_desglose" name="tallos_x_malla_desglose" required style="width: 100%"
                       value="" class="text-center input-yura_white" onkeypress="return isNumber(event)">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-yura_primary" title="Guardar"
                            onclick="store_desglose('{{$recepcion->id_recepcion}}')">
                        <i class="fa fa-fw fa-save"></i>
                    </button>
                </div>
            </th>
        </tr>
    </table>
</form>

<input type="hidden" id="id_recepcion_desglose" value="{{$recepcion->id_recepcion}}">