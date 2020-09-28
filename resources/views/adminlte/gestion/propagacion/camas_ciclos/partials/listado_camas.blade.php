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