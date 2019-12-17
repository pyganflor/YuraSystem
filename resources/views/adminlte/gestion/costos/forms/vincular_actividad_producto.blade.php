<legend class="text-center" style="font-size: 1em; margin-bottom: 5px">
    <strong>Área: </strong>"{{$actividad->area->nombre}}" -
    <strong>Actividad: </strong>"{{$actividad->nombre}}"
</legend>
<form id="form-importar_act_producto" action="{{url('costos_gestion/importar_file_act_producto')}}" method="POST">
    {!! csrf_field() !!}
    <div class="input-group">
        <span class="input-group-addon" style="background-color: #e9ecef">
            Archivo
        </span>
        <input type="file" id="file_act_producto" name="file_act_producto" required class="form-control input-group-addon"
               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">

        <input type="hidden" id="id_actividad" name="id_actividad" value="{{$actividad->id_actividad}}">

        <span class="input-group-btn">
            <button type="button" class="btn btn-primary" onclick="importar_file_act_producto()">
                <i class="fa fa-fw fa-check"></i>
            </button>
        </span>
    </div>
</form>
<div style="overflow-y: scroll; max-height: 400px">
    <table class="table-hover table-bordered table-striped" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.9em"
           id="table_vincular_act_prod">
        <thead>
        <tr class="fila_fija">
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Producto
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($productos as $p)
            <tr id="tr_vinc_producto_{{$p->id_producto}}"
                style="background-color: {{in_array($p->id_producto, $productos_vinc) ? '#6dfd85' : ''}}">
                <td class="text-center mouse-hand" style="border-color: #9d9d9d" onclick="store_actividad_producto('{{$p->id_producto}}')">
                    {{$p->nombre}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    estructura_tabla('table_vincular_act_prod', false, false);

    function importar_file_act_producto() {
        if ($('#form-importar_act_producto').valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form-importar_act_producto');
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
                            for (i = 0; i < retorno2.ids.length; i++) {
                                $('#tr_vinc_producto_' + retorno2.ids[i]).css('background-color', '#6dfd85');
                            }
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

    function store_actividad_producto(producto) {
        datos = {
            _token: '{{csrf_token()}}',
            actividad: $('#id_actividad').val(),
            producto: producto,
        };
        $('#tr_vinc_producto_' + producto).LoadingOverlay('show');
        $.post('{{url('costos_gestion/store_actividad_producto')}}', datos, function (retorno) {
            if (retorno.success) {
                if (retorno.estado == 1)
                    $('#tr_vinc_producto_' + producto).css('background-color', '#6dfd85');
                else
                    $('#tr_vinc_producto_' + producto).css('background-color', '');
            } else {
                alerta(retorno.mensaje)
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_vinc_producto_' + producto).LoadingOverlay('hide');
        });
    }
</script>