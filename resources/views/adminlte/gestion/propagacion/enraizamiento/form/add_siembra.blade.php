<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title text-color_yura">
            Ingresar
        </h3>
        <a href="javascript:void(0)" class="btn btn-xs btn-yura_primary pull-right" onclick="add_row_form_add()">
            <i class="fa fa-fw fa-plus"></i>
        </a>
    </div>
    <div class="box-body text-center">
        <table style="width: 100%; border: 1px solid #9d9d9d" id="table_form_add">
            <tr>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">Variedad</th>
                <th class="text-center th_yura_green" style="border-color: white;">Tipo</th>
                <th class="text-center th_yura_green" style="border-color: white; width: 80px;">Cantidad</th>
                <th class="text-center th_yura_green" style="border-color: white; width: 80px; border-radius: 0 18px 0 0">Semanas</th>
            </tr>
            <tr id="row_form_add_1">
                <td class="text-center">
                    <select name="form_add_planta_1" id="form_add_planta_1" style="width: 100%"
                            onchange="select_planta($(this).val(), 'form_add_variedad_1', 'div_cargar_variedades_1', '<option value=>Seleccione</option>', 0)">
                        <option value="">Seleccione</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center" id="div_cargar_variedades_1">
                    <select name="form_add_variedad_1" id="form_add_variedad_1" style="width: 100%" onchange="buscar_enraizamiento_semanal(1)">
                    </select>
                </td>
                <td class="text-center">
                    <input type="number" style="width: 100%" id="form_add_cantidad_1" class="text-center" min="0">
                </td>
                <td class="text-center" id="div_cargar_semanas_1">
                    <input type="number" style="width: 100%" id="form_add_semanas_1" class="text-center" min="0">
                </td>
            </tr>
        </table>

        <button type="button" class="btn btn-yura_primary" style="margin-top: 5px" onclick="store_enraizamiento()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>
</div>