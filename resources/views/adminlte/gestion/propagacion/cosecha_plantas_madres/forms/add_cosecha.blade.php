<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title text-color_yura">
            Cosecha
        </h3>
        <a href="javascript:void(0)" class="btn btn-xs btn-yura_primary pull-right" onclick="add_row_add_cosecha()">
            <i class="fa fa-fw fa-plus"></i>
        </a>
    </div>
    <div class="box-body text-center">
        <table class="table-bordered" id="table_form_add_cosecha" style="width: 100%; border: 1px solid #9d9d9d; border-radius: 18px 0 0 0">
            <tr>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0; width: 80px">
                    Cama
                </th>
                <th class="text-center th_yura_green" style="border-color: white;">
                    Variedad
                </th>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 18px 0 0">
                    Cantidad
                </th>
            </tr>
            <tr id="tr_add_cosecha_1">
                <td class="text-center" style="border-color: #9d9d9d;">
                    <select id="cama_add_cosecha_1" name="cama_add_cosecha_1" style="width: 100%" onchange="select_cama(1)">
                        @foreach($camas as $c)
                            <option value="{{$c->id_cama}}">{{$c->nombre}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;" id="td_variedad_add_cosecha_1">
                </td>
                <td class="text-center" style="border-color: #9d9d9d;">
                    <input type="number" onkeyup="return isNumber(event)" id="cantidad_add_cosecha_1" style="width: 100%" class="text-center">
                </td>
                <input type="hidden" id="variedad_add_cosecha_1">
            </tr>
        </table>
        <button type="button" class="btn btn-sm btn-yura_primary text-white" style="margin-top: 10px" onclick="store_cosechas()">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>
</div>