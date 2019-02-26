<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Listado de especificaciones
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                       onclick="ver_especificaciones('{{$cliente->id_cliente}}',true)">
                        <i class="fa fa-user-plus" aria-hidden="true"></i> Añadir especificación
                    </a>
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                       onclick="add_especificacion('{{$cliente->id_cliente}}')">
                        <i class="fa fa-fw fa-plus"></i> Crear especificación
                    </a>
                </div>
            </div>
            <div class="col-md-9" id="div_content">
            </div>
        </div>
    </div>
    <input type="hidden" id="id_cliente" value="{{$cliente->id_cliente}}">
</div>

<script>

    function ver_especificaciones(id_cliente,listar_todas) {
        $.LoadingOverlay('show');
        datos = {
            id_cliente: id_cliente,
            listar_todas : listar_todas
        };
        get_jquery('{{url('clientes/ver_especificaciones')}}', datos, function (retorno) {

            $('#div_content').html(retorno);
        });
        $.LoadingOverlay('hide');
    }

   ver_especificaciones('{{$cliente->id_cliente}}');
</script>
