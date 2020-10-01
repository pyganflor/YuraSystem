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
    @foreach($cosechas as $cos)
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$cos->semana()->codigo}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <span class="field_show_cosecha_{{$cos->id_cosecha_plantas_madres}}">{{$cos->cama->nombre}}</span>
                <select id="cama_edit_{{$cos->id_cosecha_plantas_madres}}" style="width: 100%"
                        class="hidden field_edit_cosecha_{{$cos->id_cosecha_plantas_madres}}">
                    @foreach($camas as $cam)
                        <option value="{{$cam->id_cama}}" {{$cam->id_cama == $cos->id_cama ? 'selected' : ''}}>
                            {{$cam->nombre}} - {{$cam->siglas}}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$cos->variedad->siglas}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <span class="field_show_cosecha_{{$cos->id_cosecha_plantas_madres}}">{{$cos->cantidad}}</span>
                <input type="number" id="cantidad_edit_{{$cos->id_cosecha_plantas_madres}}" style="width: 100%"
                       class="hidden text-center field_edit_cosecha_{{$cos->id_cosecha_plantas_madres}}" value="{{$cos->cantidad}}">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-yura_default"
                            onclick="edit_cosecha('{{$cos->id_cosecha_plantas_madres}}')">
                        <i class="fa fa-fw fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_danger">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    function edit_cosecha(cos) {
        $('.field_edit_cosecha_' + cos).removeClass('hidden');
        $('.field_show_cosecha_' + cos).addClass('hidden');
    }
</script>