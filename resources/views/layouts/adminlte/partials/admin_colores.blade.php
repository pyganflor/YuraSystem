<table class="table-bordered" width="100%" style="border: 2px solid #9d9d9d" id="table_admin_colores">
    <tr style="background-color: #357ca5; color: white">
        <th class="text-center" style="border-color: #9d9d9d">
            NOMBRE
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            FONDO
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            TEXTO
        </th>
        <th class="text-center" style="border-color: #9d9d9d" width="65px">
            <button type="button" class="btn btn-xs btn-default" title="Añadir color" onclick="add_color()" id="btn_add_color">
                <i class="fa fa-fw fa-plus"></i>
            </button>
            <button type="button" class="btn btn-xs btn-primary" title="Añadir color" onclick="cerrar_modals(); admin_colores()">
                <i class="fa fa-fw fa-refresh"></i>
            </button>
        </th>
    </tr>
    @foreach($colores as $pos_c => $c)
        <tr>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="text" id="nombre_{{$c->id_color}}" name="nombre_{{$c->id_color}}" value="{{$c->nombre}}"
                       class="form-control text-center">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="color" id="fondo_{{$c->id_color}}" name="fondo_{{$c->id_color}}" value="{{$c->fondo}}"
                       class="form-control text-center">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="color" id="texto_{{$c->id_color}}" name="texto_{{$c->id_color}}" value="{{$c->texto}}"
                       class="form-control text-center">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <button type="button" class="btn btn-xs btn-warning" title="Editar" onclick="update_color('{{$c->id_color}}')">
                    <i class="fa fa-fw fa-pencil"></i>
                </button>
            </th>
        </tr>
    @endforeach
</table>

<script>
    function add_color() {
        row = $('#table_admin_colores').find('tr')[0];
        $('<tr>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" id="nombre_new" name="nombre_new" value=""' +
            ' class="form-control text-center" autofocus>' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="color" id="fondo_new" name="fondo_new" value=""' +
            ' class="form-control text-center">' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="color" id="texto_new" name="texto_new" value=""' +
            ' class="form-control text-center">' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d" id="celda_opcion_new">' +
            '<button type="button" class="btn btn-xs btn-success" title="Guardar" onclick="store_color()">' +
            '<i class="fa fa-fw fa-save"></i>' +
            '</button>' +
            '</th>' +
            '</tr>').insertAfter(row);
        $('#nombre_new').focus();
        $('#btn_add_color').hide();
    }

    function store_color() {
        if ($('#nombre_new').val() != '') {
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre_new').val(),
                fondo: $('#fondo_new').val(),
                texto: $('#texto_new').val(),
            };
            post_jquery('{{url('admin_colores/store_color')}}', datos, function () {
                cerrar_modals();
                admin_colores();
            });
        }
    }

    function update_color(c) {
        if ($('#nombre_' + c).val() != '') {
            datos = {
                _token: '{{csrf_token()}}',
                id_color: c,
                nombre: $('#nombre_' + c).val(),
                fondo: $('#fondo_' + c).val(),
                texto: $('#texto_' + c).val(),
            };
            post_jquery('{{url('admin_colores/update_color')}}', datos, function () {
                cerrar_modals();
                admin_colores();
            });
        }
    }
</script>