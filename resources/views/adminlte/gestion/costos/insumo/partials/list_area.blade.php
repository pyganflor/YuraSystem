<table class="table-responsive table-striped table-bordered table-hover tabla_master" width="100%" id="table_area">
    <thead>
    <tr class="fila_fija">
        <th class="text-center" style="border-color: #9d9d9d">Área</th>
        <th class="text-center" width="20px" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-primary" onclick="add_area()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($areas as $a)
        <tr id="tr_area_{{$a->id_area}}">
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="text" maxlength="50" id="n_area_{{$a->id_area}}" name="n_area_{{$a->id_area}}" value="{{$a->nombre}}"
                       style="width: 100%" class="text-center">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-success" onclick="update_area('{{$a->id_area}}')" id="btn_upd_area_{{$a->id_area}}">
                    <i class="fa fa-fw fa-edit"></i>
                </button>
                <i class="fa fa-fw fa-check hide" style="color: green" id="icon_area_{{$a->id_area}}"></i>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    var cant_forms_area = 0;

    function add_area() {
        cant_forms_area++;
        $('#table_area').append('<tr id="new_tr_area_' + cant_forms_area + '">' +
            '<td class="text-center">' +
            '<input type="text" maxlength="50" id="new_area_' + cant_forms_area + '" name="new_area_' + cant_forms_area + '" style="width: 100%" class="text-center">' +
            '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="btn btn-xs btn-success" id="btn_area_' + cant_forms_area + '" onclick="store_area(' + cant_forms_area + ')">' +
            '<i class="fa fa-fw fa-check"></i>' +
            '</button>' +
            '<i class="fa fa-fw fa-check hide" style="color: green" id="icon_new_area_' + cant_forms_area + '"></i>' +
            '</td>' +
            '</tr>');
        $('#new_area_' + cant_forms_area).focus();
    }

    function store_area(num) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#new_area_' + num).val()
        };
        $('#new_tr_area_' + num).LoadingOverlay('show');
        $.post('{{url('costos_gestion/store_area')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_area_' + num).hide();
                $('#icon_new_area_' + num).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#new_tr_area_' + num).LoadingOverlay('hide');
        });
    }

    function update_area(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_area: id,
            nombre: $('#n_area_' + id).val(),
        };
        $('#tr_area_' + id).LoadingOverlay('show');
        $.post('{{url('costos_gestion/update_area')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_upd_area_' + id).hide();
                $('#icon_area_' + id).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_area_' + id).LoadingOverlay('hide');
        });
    }
</script>