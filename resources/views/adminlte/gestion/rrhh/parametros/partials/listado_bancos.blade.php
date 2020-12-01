<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d; border-radius: 18px 18px 0 0" id="tabla_bancos">
    <tr>
        <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">Nombre</th>
        <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 18px 0 0; width: 80px">
            <button type="button" class="btn btn-xs btn-yura_default" onclick="add_banco()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    @foreach($listado as $item)
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$item->nombre}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-yura_primary">
                        <i class="fa fa-fw fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_danger">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</table>

<script>
    var cant_nuevos = 0;

    function add_banco() {
        cant_nuevos++;
        $('#tabla_bancos').append('<tr>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" class="text-center" id="new_banco_' + cant_nuevos + '" style="width: 100%">' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<button type="button" class="btn btn-xs btn-yura_primary" onclick="store_banco(' + cant_nuevos + ')">' +
            '<i class="fa fa-fw fa-save"></i>' +
            '</button>' +
            '</td>' +
            '</tr>');
    }

    function store_banco(num_nuevo) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#new_banco_' + num_nuevo).val(),
        };
        post_jquery('{{url('parametros/store_banco')}}', datos, function () {
            listar_parametro();
        });
    }
</script>