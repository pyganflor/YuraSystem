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
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="text" maxlength="50" id="nombre_area_{{$a->id_area}}" value="{{$a->nombre}}" style="width: 100%">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-success">
                    <i class="fa fa-fw fa-edit"></i>
                </button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    var cant_forms_area = 0;

    function add_area() {
        cant_forms_area++;
        $('#table_area').append('<tr>' +
            '<td class="text-center">' +
            '<input type="text" maxlength="50" id="nombre_area_' + cant_forms_area + '" style="width: 100%">' +
            '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="btn btn-xs btn-success">' +
            '<i class="fa fa-fw fa-edit"></i>' +
            '</button>' +
            '</td>' +
            '</tr>');
    }
</script>