<table class="table-responsive table-bordered table-striped" style="width: 100%; border: 1px solid #9d9d9d" id="edit_table">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; width: 40%">
            Actividad
        </th>
        <th class="text-center" style="border-color: #9d9d9d; width: 40%">
            Insumo
        </th>
        <th class="text-center" style="border-color: #9d9d9d; width: 10%">
            Semana
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            Valor
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-yura_dark" onclick="add_edit_form()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_edit_1">
        <td class="text-center" style="border-color: #9d9d9d">
            <select id="edit_actividad_1" style="width: 100%" onchange="buscar_insumosByActividad(1)">
                <option value="">Seleccione ...</option>
                @foreach($actividades as $act)
                    <option value="{{$act->id_actividad}}">{{$act->nombre}}</option>
                @endforeach
            </select>
        </td>
        <td class="text-center" style="border-color: #9d9d9d" id="edit_td_insumo_1">
        </td>
        <td class="text-center" style="border-color: #9d9d9d" id="edit_td_semana_1">
            <input type="number" id="edit_semana_1" style="width: 100%; display: none" class="text-center" onchange="select_semana(1)">
        </td>
        <td class="text-center" style="border-color: #9d9d9d">
            <input type="number" id="edit_valor_1" style="width: 100%" class="text-center">
        </td>
        <td class="text-center" style="border-color: #9d9d9d" id="edit_td_button_1">
            <button type="button" class="btn btn-xs btn-yura_primary" onclick="save_costo(1)" id="edit_btn_1">
                <i class="fa fa-fw fa-save"></i>
            </button>
            <i class="fa fa-fw fa-check text-color_yura" style="display: none" id="edit_icon_check_1"
               onclick="$(this).hide(); $('#edit_btn_1').show()"></i>
            <i class="fa fa-fw fa-ban text-red" style="display: none" id="edit_icon_ban_1"
               onclick="$(this).hide(); $('#edit_btn_1').show()"></i>
        </td>
    </tr>
</table>

<script>
    var num_form = 1;

    function add_edit_form() {
        num_form++;
        $('#edit_table').append('<tr id="tr_edit_' + num_form + '">' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<select id="edit_actividad_' + num_form + '" style="width: 100%" onchange="buscar_insumosByActividad(' + num_form + ')">' +
            $('#edit_actividad_1').html() +
            '</select>' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d" id="edit_td_insumo_' + num_form + '">' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d" id="edit_td_semana_' + num_form + '">' +
            '<input type="number" id="edit_semana_' + num_form + '" style="width: 100%; display: none" class="text-center" ' +
            'onchange="select_semana(' + num_form + ')">' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<input type="number" id="edit_valor_' + num_form + '" style="width: 100%" class="text-center">' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d" id="edit_td_button_' + num_form + '">' +
            '<button type="button" class="btn btn-xs btn-yura_primary" onclick="save_costo(' + num_form + ')" id="edit_btn_' + num_form + '">' +
            '<i class="fa fa-fw fa-save"></i>' +
            '</button>' +
            '<i class="fa fa-fw fa-check text-color_yura" style="display: none" id="edit_icon_check_' + num_form + '" ' +
            'onclick="$(this).hide(); $(\'#edit_btn_' + num_form + '\').show()"></i>' +
            '<i class="fa fa-fw fa-ban text-red" style="display: none" id="edit_icon_ban_' + num_form + '" ' +
            'onclick="$(this).hide(); $(\'#edit_btn_' + num_form + '\').show()"></i>' +
            '</td>' +
            '</tr>');
    }

    function buscar_insumosByActividad(form) {
        datos = {
            actividad: $('#edit_actividad_' + form).val(),
            form: form
        };
        get_jquery('{{url('costos_gestion/buscar_insumosByActividad')}}', datos, function (retorno) {
            $('#edit_td_insumo_' + form).html(retorno);
            $('#edit_semana_' + form).val('');
            $('#edit_semana_' + form).css('display', 'none');
            $('#edit_btn_' + form).show();
            $('#edit_icon_check_' + form).hide();
            $('#edit_icon_ban_' + form).hide();
        }, 'tr_edit_' + form);
    }

    function select_insumo(form) {
        $('#edit_semana_' + form).val('');
        $('#edit_semana_' + form).css('display', '');
        $('#edit_btn_' + form).show();
        $('#edit_icon_check_' + form).hide();
        $('#edit_icon_ban_' + form).hide();
    }

    function select_semana(form) {
        datos = {
            _token: '{{csrf_token()}}',
            actividad: $('#edit_actividad_' + form).val(),
            insumo: $('#edit_insumo_' + form).val(),
            semana: $('#edit_semana_' + form).val(),
        };
        $('#edit_btn_' + form).show();
        $('#edit_icon_check_' + form).hide();
        $('#edit_icon_ban_' + form).hide();
        $('#tr_edit_' + form).LoadingOverlay('show');
        $.post('{{url('costos_gestion/buscar_valorByActividadInsumoSemana')}}', datos, function (retorno) {
            $('#edit_valor_' + form).val(retorno.valor);
            if (!retorno.existe) {
                save_costo(form);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_edit_' + form).LoadingOverlay('hide');
        });
    }

    function save_costo(form) {
        datos = {
            _token: '{{csrf_token()}}',
            actividad: $('#edit_actividad_' + form).val(),
            insumo: $('#edit_insumo_' + form).val(),
            semana: $('#edit_semana_' + form).val(),
            valor: $('#edit_valor_' + form).val(),
        };
        $('#tr_edit_' + form).LoadingOverlay('show');
        $.post('{{url('costos_gestion/save_costo')}}', datos, function (retorno) {
            $('#edit_btn_' + form).hide();
            if (retorno.success) {
                $('#edit_icon_check_' + form).show();
                $('#edit_icon_ban_' + form).hide();
            } else {
                $('#edit_icon_check_' + form).hide();
                $('#edit_icon_ban_' + form).show();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_edit_' + form).LoadingOverlay('hide');
        });
    }
</script>