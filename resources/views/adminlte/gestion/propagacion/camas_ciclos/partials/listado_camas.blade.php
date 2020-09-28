<table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d" id="table_camas">
    <thead>
    <tr>
        <th class="text-center th_yura_default" style="border-color: #9d9d9d;">Área</th>
        <th class="text-center th_yura_default" style="border-color: #9d9d9d">Cama</th>
        <th class="text-center th_yura_default" style="border-color: #9d9d9d; width: 60px">
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-default btn-yura_default" onclick="listar_camas()" title="Actualizar listado">
                    <i class="fa fa-fw fa-refresh"></i>
                </button>
                <button type="button" class="btn btn-xs btn-primary btn-yura_primary" onclick="add_cama()" title="Agregar cama">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </div>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($camas as $c)
        <tr class="{{$c->estado == 0 ? 'error' : ''}}">
            <td class="text-center td_yura_default" style="border-color: #9d9d9d">{{$c->area_trabajo}}</td>
            <td class="text-center td_yura_default" style="border-color: #9d9d9d">{{$c->nombre}}</td>
            <td class="text-center td_yura_default" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-warning" onclick="edit_cama('{{$c->id_cama}}')">
                        <i class="fa fa-fw fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" onclick="eliminar_cama('{{$c->id_cama}}')">
                        <i class="fa fa-fw fa-{{$c->estado == 1 ? 'lock' : 'unlock'}}"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    function eliminar_cama(id) {
        modal_quest('modal_quest-eliminar_cama',
            '<div class="alert alert-warning text-center">¿Desea activar/desactivar esta cama?</div>',
            '<i class="fa fa-fw fa-exclamation-triangle"></i> Pregunta de alerta', true, false, '50%', function () {
                cerrar_modals();
                datos = {
                    _token: '{{csrf_token()}}',
                    id_cama: id
                };
                $.LoadingOverlay('show');
                $.post('{{url('camas_ciclos/eliminar_cama')}}', datos, function (retorno) {
                    alerta(retorno.mensaje);
                    if (retorno.success) {
                        listar_camas();
                    }
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                }).always(function () {
                    $.LoadingOverlay('hide');
                })
            });
    }
</script>