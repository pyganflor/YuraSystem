<table id="table_cosechas" style="width: 100%; border: 2px solid #9d9d9d; border-radius: 18px 18px 0 0" class="table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">
            Semana
        </th>
        <th class="text-center th_yura_green" style="border-color: white">
            Cama
        </th>
        <th class="text-center th_yura_green" style="border-color: white">
            Variedad
        </th>
        <th class="text-center th_yura_green" style="border-color: white">
            Plantas
        </th>
        <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 18px 0 0; width: 80px">
            Opciones
        </th>
    </tr>
    </thead>
    <tbody>
    @php
        $cosecha_total = 0;
    @endphp
    @foreach($cosechas as $pos_cos => $cos)
        @php
            $cosecha_total += $cos->cantidad;
        @endphp
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$cos->semana()->codigo}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <span class="field_show_cosecha_{{$cos->id_cosecha_plantas_madres}}">{{$cos->cama->nombre}}</span>
                <select id="cama_edit_{{$cos->id_cosecha_plantas_madres}}" style="width: 100%"
                        onchange="select_cama_edit('{{$cos->id_cosecha_plantas_madres}}')"
                        class="hidden field_edit_cosecha_{{$cos->id_cosecha_plantas_madres}}">
                    @foreach($camas as $cam)
                        <option value="{{$cam->id_cama}} - {{$cam->siglas}} - {{$cam->id_variedad}}" {{$cam->id_cama == $cos->id_cama ? 'selected' : ''}}>
                            {{$cam->nombre}}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center" style="border-color: #9d9d9d" id="td_variedad_listado_cosecha_{{$cos->id_cosecha_plantas_madres}}">
                {{$cos->variedad->siglas}}
            </td>
            <input type="hidden" id="id_variedad_edit_cosecha_{{$cos->id_cosecha_plantas_madres}}" value="{{$cos->id_variedad}}">
            <td class="text-center" style="border-color: #9d9d9d">
                <span class="field_show_cosecha_{{$cos->id_cosecha_plantas_madres}}">{{$cos->cantidad}}</span>
                <input type="number" id="cantidad_edit_{{$cos->id_cosecha_plantas_madres}}" style="width: 100%"
                       class="hidden text-center field_edit_cosecha_{{$cos->id_cosecha_plantas_madres}}" value="{{$cos->cantidad}}">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-yura_default" id="btn_edit_cosecha_{{$cos->id_cosecha_plantas_madres}}"
                            onclick="edit_cosecha('{{$cos->id_cosecha_plantas_madres}}')">
                        <i class="fa fa-fw fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_primary hidden" id="btn_update_cosecha_{{$cos->id_cosecha_plantas_madres}}"
                            onclick="update_cosecha('{{$cos->id_cosecha_plantas_madres}}')">
                        <i class="fa fa-fw fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_danger" onclick="eliminar_cosecha('{{$cos->id_cosecha_plantas_madres}}')">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<script>
    $('#total_cosecha_dia').val('{{$cosecha_total}} esquejes');
    $('#datos_cosecha_x_variedad').html('');
    @foreach($cosecha_x_variedad as $cos)
        $('#datos_cosecha_x_variedad').append('<option value="">{{$cos->siglas}} - {{$cos->cantidad}}</option>');
    @endforeach
</script>