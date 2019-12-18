<table class="table-responsive table-striped table-bordered table-hover tabla_master" width="100%" id="table_producto">
    <thead>
    <tr class="fila_fija">
        <th class="text-center" style="border-color: #9d9d9d">Producto</th>
        <th class="text-center" width="70px" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-primary" onclick="add_producto()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
            <button type="button" class="btn btn-xs btn-default" onclick="importar_producto()">
                <i class="fa fa-fw fa-upload"></i>
            </button>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($productos as $a)
        <tr id="tr_producto_{{$a->id_producto}}" class="{{$a->estado == 0 ? 'error' : ''}}">
            <td style="border-color: #9d9d9d" class="text-center">
                <input type="text" maxlength="50" id="n_producto_{{$a->id_producto}}" name="n_producto_{{$a->id_producto}}"
                       value="{{$a->nombre}}"
                       style="width: 100%" class="text-center">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-success" onclick="update_producto('{{$a->id_producto}}')"
                        id="btn_upd_producto_{{$a->id_producto}}">
                    <i class="fa fa-fw fa-edit"></i>
                </button>
                <button type="button" class="btn btn-xs btn-danger" onclick="delete_producto('{{$a->id_producto}}')"
                        id="btn_del_producto_{{$a->id_producto}}">
                    <i class="fa fa-fw fa-{{$a->estado == 1 ? 'trash' : 'unlock'}}"></i>
                </button>
                <i class="fa fa-fw fa-check hide" style="color: green" id="icon_producto_{{$a->id_producto}}"></i>
                <i class="fa fa-fw fa-trash-o hide" style="color: red" id="icon_del_producto_{{$a->id_producto}}"></i>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    var cant_forms_producto = 0;

    function add_producto() {
        cant_forms_producto++;
        $('#table_producto').append('<tr id="new_tr_producto_' + cant_forms_producto + '">' +
            '<td class="text-center">' +
            '<input type="text" maxlength="50" id="new_producto_' + cant_forms_producto + '" name="new_producto_' + cant_forms_producto + '" style="width: 100%" class="text-center">' +
            '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="btn btn-xs btn-success" id="btn_producto_' + cant_forms_producto + '" onclick="store_producto(' + cant_forms_producto + ')">' +
            '<i class="fa fa-fw fa-check"></i>' +
            '</button>' +
            '<i class="fa fa-fw fa-check hide" style="color: green" id="icon_new_producto_' + cant_forms_producto + '"></i>' +
            '</td>' +
            '</tr>');
        $('#new_producto_' + cant_forms_producto).focus();
    }

    function store_producto(num) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#new_producto_' + num).val()
        };
        $('#new_tr_producto_' + num).LoadingOverlay('show');
        $.post('{{url('costos_gestion/store_producto')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_producto_' + num).hide();
                $('#icon_new_producto_' + num).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#new_tr_producto_' + num).LoadingOverlay('hide');
        });
    }

    function update_producto(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_producto: id,
            nombre: $('#n_producto_' + id).val(),
        };
        $('#tr_producto_' + id).LoadingOverlay('show');
        $.post('{{url('costos_gestion/update_producto')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_upd_producto_' + id).hide();
                $('#icon_producto_' + id).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_producto_' + id).LoadingOverlay('hide');
        });
    }

    function importar_producto() {
        get_jquery('{{url('costos_gestion/importar_producto')}}', {}, function (retorno) {
            modal_view('modal-view_importar_producto', retorno, '<i class="fa fa-fw fa-upload"></i> Importar', true, false, '{{isPC() ? '75%' : ''}}')
        })
    }

    function importar_file_producto() {
        if ($('#form-importar_producto').valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form-importar_producto');
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

    function delete_producto(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_producto: id,
        };
        $('#tr_producto_' + id).LoadingOverlay('show');
        $.post('{{url('costos_gestion/delete_producto')}}', datos, function (retorno) {
            if (retorno.success) {
                $('#btn_upd_producto_' + id).hide();
                $('#btn_del_producto_' + id).hide();
                $('#icon_del_producto_' + id).removeClass('hide');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_producto_' + id).LoadingOverlay('hide');
        });
    }
</script>