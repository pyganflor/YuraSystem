<table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d; border-radius: 18px 18px 0 0" id="table_contenedores">
    <thead>
    <tr>
        <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">Contenedor</th>
        <th class="text-center th_yura_green" style="border-color: white;">Cantidad</th>
        <th class="text-center th_yura_green" style="border-color: white; width: 60px; border-radius: 0 18px 0 0">
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-default btn-yura_default" onclick="listar_contenedores()" title="Actualizar listado">
                    <i class="fa fa-fw fa-refresh"></i>
                </button>
                <button type="button" class="btn btn-xs btn-yura_dark" onclick="add_contenedor()" title="Agregar contenedor">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </div>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($contenedores as $c)
        <tr class="{{$c->estado == 0 ? 'error' : ''}}" onmouseover="$(this).addClass()">
            <td class="text-center td_yura_default" style="border-color: #9d9d9d">{{$c->nombre}}</td>
            <td class="text-center td_yura_default" style="border-color: #9d9d9d">{{$c->cantidad}}</td>
            <td class="text-center td_yura_default" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-warning" onclick="edit_contenedor('{{$c->id_contenedor_propag}}')">
                        <i class="fa fa-fw fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" onclick="eliminar_contenedor('{{$c->id_contenedor_propag}}')">
                        <i class="fa fa-fw fa-{{$c->estado == 1 ? 'lock' : 'unlock'}}"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>