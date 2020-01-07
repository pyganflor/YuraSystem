<table class="table-responsive table-striped table-bordered table-hover tabla_master" width="100%" id="table_actividad">
    <thead>
    <tr class="fila_fija">
        <th class="text-center" style="border-color: #9d9d9d" colspan="2">Actividad</th>
        <th class="text-center" style="border-color: #9d9d9d" width="70px">
            <button type="button" class="btn btn-xs btn-primary" onclick="add_actividad()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
            <button type="button" class="btn btn-xs btn-default" onclick="importar_actividad()">
                <i class="fa fa-fw fa-upload"></i>
            </button>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($actividades as $a)
        <tr id="tr_actividad_{{$a->id_actividad}}" class="{{$a->estado == 0 ? 'error' : ''}}">
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="text" maxlength="50" id="n_actividad_{{$a->id_actividad}}" name="n_actividad_{{$a->id_actividad}}"
                       value="{{$a->nombre}}" title="Doble click para vincular manos de obra"
                       style="width: 100%" class="text-center" ondblclick="vincular_actividad_mano_obra('{{$a->id_actividad}}')">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <select name="area_actividad_{{$a->id_actividad}}" id="area_actividad_{{$a->id_actividad}}" style="width: 100%">
                    @foreach($areas as $item)
                        <option value="{{$item->id_area}}" {{$item->id_area == $a->id_area ? 'selected' : ''}}>
                            {{$item->nombre}}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-success" onclick="update_actividad('{{$a->id_actividad}}')"
                        id="btn_upd_actividad_{{$a->id_actividad}}">
                    <i class="fa fa-fw fa-edit"></i>
                </button>
                <button type="button" class="btn btn-xs btn-danger" onclick="delete_actividad('{{$a->id_actividad}}')"
                        id="btn_del_actividad_{{$a->id_actividad}}">
                    <i class="fa fa-fw fa-{{$a->estado == 1 ? 'trash' : 'unlock'}}"></i>
                </button>
                <i class="fa fa-fw fa-check hide" style="color: green" id="icon_actividad_{{$a->id_actividad}}"></i>
                <i class="fa fa-fw fa-trash-o hide" style="color: red" id="icon_del_actividad_{{$a->id_actividad}}"></i>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    var cant_forms_actividad = 0;

    function add_actividad() {
        cant_forms_actividad++;
        $('#table_actividad').append('<tr id="new_tr_actividad_' + cant_forms_actividad + '">' +
            '<td class="text-center">' +
            '<input type="text" maxlength="50" id="new_actividad_' + cant_forms_actividad + '" name="new_actividad_' + cant_forms_actividad + '" style="width: 100%" class="text-center">' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<select name="new_area_actividad_' + cant_forms_actividad + '" id="new_area_actividad_' + cant_forms_actividad + '" style="width: 100%">' +
            '@foreach($areas as $item)' +
            '<option value="{{$item->id_area}}">' +
            '{{$item->nombre}}' +
            '</option>' +
            '@endforeach' +
            '</select>' +
            '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="btn btn-xs btn-success" id="btn_actividad_' + cant_forms_actividad + '" onclick="store_actividad(' + cant_forms_actividad + ')">' +
            '<i class="fa fa-fw fa-check"></i>' +
            '</button>' +
            '<i class="fa fa-fw fa-check hide" style="color: green" id="icon_new_actividad_' + cant_forms_actividad + '"></i>' +
            '</td>' +
            '</tr>');
        $('#new_actividad_' + cant_forms_actividad).focus();
    }

    function store_actividad(num) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#new_actividad_' + num).val(),
            area: $('#new_area_actividad_' + num).val(),
        };
        $('#new_tr_actividad_' + num).LoadingOverlay('show');
        $.post('{{url('costos_gestion/store_actividad')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_actividad_' + num).hide();
                $('#icon_new_actividad_' + num).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#new_tr_actividad_' + num).LoadingOverlay('hide');
        });
    }

    function update_actividad(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_actividad: id,
            nombre: $('#n_actividad_' + id).val(),
            area: $('#area_actividad_' + id).val(),
        };
        $('#tr_actividad_' + id).LoadingOverlay('show');
        $.post('{{url('costos_gestion/update_actividad')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_upd_actividad_' + id).hide();
                $('#btn_del_actividad_' + id).hide();
                $('#icon_actividad_' + id).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_actividad_' + id).LoadingOverlay('hide');
        });
    }

    function delete_actividad(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_actividad: id,
        };
        $('#tr_actividad_' + id).LoadingOverlay('show');
        $.post('{{url('costos_gestion/delete_actividad')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_upd_actividad_' + id).hide();
                $('#btn_del_actividad_' + id).hide();
                $('#icon_del_actividad_' + id).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_actividad_' + id).LoadingOverlay('hide');
        });
    }

    function importar_actividad() {
        get_jquery('{{url('costos_gestion/importar_actividad')}}', {}, function (retorno) {
            modal_view('modal-view_importar_actividad', retorno, '<i class="fa fa-fw fa-upload"></i> Importar', true, false, '{{isPC() ? '75%' : ''}}')
        })
    }

    function importar_file_actividad() {
        if ($('#form-importar_actividad').valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form-importar_actividad');
            var formData = new FormData(formulario[0]);
            //hacemos la petición ajax
            $.ajax({
                url: formulario.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                //necesario para subir archivos via ajax
                cache: false,
                contentType: false,
                processData: false,

                success: function (retorno2) {
                    notificar('Se ha importado un archivo', '{{url('costos_gestion')}}');
                    if (retorno2.success) {
                        $.LoadingOverlay('hide');
                        alerta_accion(retorno2.mensaje, function () {
                            location.reload();
                        });
                    } else {
                        alerta(retorno2.mensaje);
                        $.LoadingOverlay('hide');
                    }
                },
                //si ha ocurrido un error
                error: function (retorno2) {
                    console.log(retorno2);
                    alerta(retorno2.responseText);
                    alert('Hubo un problema en el envío de la información');
                    $.LoadingOverlay('hide');
                }
            });
        }
    }

    function vincular_actividad_mano_obra(id) {
        datos = {
            id: id
        };
        get_jquery('{{url('gestion_mano_obra/vincular_actividad_mano_obra')}}', datos, function (retorno) {
            modal_view('modal-view_vincular_actividad_mano_obra', retorno, '<i class="fa fa-fw fa-exchange"></i> Vincular manos de obra a la actividad', true, false, '{{isPC() ? '75%' : ''}}')
        })
    }
</script>