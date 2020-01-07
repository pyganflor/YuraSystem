<table class="table-responsive table-striped table-bordered table-hover tabla_master" width="100%" id="table_mano_obra">
    <thead>
    <tr class="fila_fija">
        <th class="text-center" style="border-color: #9d9d9d">Mano de Obra</th>
        <th class="text-center" width="70px" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-primary" onclick="add_mano_obra()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
            <button type="button" class="btn btn-xs btn-default" onclick="importar_mano_obra()">
                <i class="fa fa-fw fa-upload"></i>
            </button>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($manos_obra as $a)
        <tr id="tr_mano_obra_{{$a->id_mano_obra}}" class="{{$a->estado == 0 ? 'error' : ''}}">
            <td style="border-color: #9d9d9d" class="text-center">
                <input type="text" maxlength="50" id="n_mano_obra_{{$a->id_mano_obra}}" name="n_mano_obra_{{$a->id_mano_obra}}"
                       value="{{$a->nombre}}"
                       style="width: 100%" class="text-center">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-success" onclick="update_mano_obra('{{$a->id_mano_obra}}')"
                        id="btn_upd_mano_obra_{{$a->id_mano_obra}}">
                    <i class="fa fa-fw fa-edit"></i>
                </button>
                <button type="button" class="btn btn-xs btn-danger" onclick="delete_mano_obra('{{$a->id_mano_obra}}')"
                        id="btn_del_mano_obra_{{$a->id_mano_obra}}">
                    <i class="fa fa-fw fa-{{$a->estado == 1 ? 'trash' : 'unlock'}}"></i>
                </button>
                <i class="fa fa-fw fa-check hide" style="color: green" id="icon_mano_obra_{{$a->id_mano_obra}}"></i>
                <i class="fa fa-fw fa-trash-o hide" style="color: red" id="icon_del_mano_obra_{{$a->id_mano_obra}}"></i>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    var cant_forms_mano_obra = 0;

    function add_mano_obra() {
        cant_forms_mano_obra++;
        $('#table_mano_obra').append('<tr id="new_tr_mano_obra_' + cant_forms_mano_obra + '">' +
            '<td class="text-center">' +
            '<input type="text" maxlength="50" id="new_mano_obra_' + cant_forms_mano_obra + '" name="new_mano_obra_' + cant_forms_mano_obra + '" style="width: 100%" class="text-center">' +
            '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="btn btn-xs btn-success" id="btn_mano_obra_' + cant_forms_mano_obra + '" onclick="store_mano_obra(' + cant_forms_mano_obra + ')">' +
            '<i class="fa fa-fw fa-check"></i>' +
            '</button>' +
            '<i class="fa fa-fw fa-check hide" style="color: green" id="icon_new_mano_obra_' + cant_forms_mano_obra + '"></i>' +
            '</td>' +
            '</tr>');
        $('#new_mano_obra_' + cant_forms_mano_obra).focus();
    }

    function store_mano_obra(num) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#new_mano_obra_' + num).val()
        };
        $('#new_tr_mano_obra_' + num).LoadingOverlay('show');
        $.post('{{url('gestion_mano_obra/store_mano_obra')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_mano_obra_' + num).hide();
                $('#icon_new_mano_obra_' + num).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#new_tr_mano_obra_' + num).LoadingOverlay('hide');
        });
    }

    function update_mano_obra(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_mano_obra: id,
            nombre: $('#n_mano_obra_' + id).val(),
        };
        $('#tr_mano_obra_' + id).LoadingOverlay('show');
        $.post('{{url('gestion_mano_obra/update_mano_obra')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_upd_mano_obra_' + id).hide();
                $('#icon_mano_obra_' + id).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_mano_obra_' + id).LoadingOverlay('hide');
        });
    }

    function importar_mano_obra() {
        get_jquery('{{url('gestion_mano_obra/importar_mano_obra')}}', {}, function (retorno) {
            modal_view('modal-view_importar_mano_obra', retorno, '<i class="fa fa-fw fa-upload"></i> Importar', true, false, '{{isPC() ? '75%' : ''}}')
        })
    }

    function importar_file_mano_obra() {
        if ($('#form-importar_mano_obra').valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form-importar_mano_obra');
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

    function delete_mano_obra(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_mano_obra: id,
        };
        $('#tr_mano_obra_' + id).LoadingOverlay('show');
        $.post('{{url('gestion_mano_obra/delete_mano_obra')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_upd_mano_obra_' + id).hide();
                $('#btn_del_mano_obra_' + id).hide();
                $('#icon_del_mano_obra_' + id).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_mano_obra_' + id).LoadingOverlay('hide');
        });
    }
</script>