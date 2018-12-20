<form id="form-add_grosor_ramo">
    <table class="table table-bordered table-responsive" width="100%" style="font-size: 0.8em"
           id="table_listado_grosor_ramo">
        <tr>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Nombre
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d"
                width="35%">
                Opciones
                <button type="button" class="btn btn-xs btn-danger pull-right" onclick="del_grosor_ramo()" style="display: none"
                        id="btn-del_form_grosor">
                    <i class="fa fa-fw fa-trash"></i>
                </button>
                <button type="button" class="btn btn-xs btn-default pull-right" onclick="add_grosor_ramo()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </th>
        </tr>
        @foreach($listado as $item)
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    <form id="form-update_grosor_{{$item->id_grosor_ramo}}">
                        <input type="text" class="text-center" id="nombre_grosor_{{$item->id_grosor_ramo}}" value="{{$item->nombre}}"
                               {{$item->estado == 1 ? '' : 'disabled readonly'}} name="nombre_grosor_{{$item->id_grosor_ramo}}" required
                               maxlength="250" width="100%">
                    </form>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <button type="button" class="btn btn-xs btn-success" onclick="update_grosor('{{$item->id_grosor_ramo}}')">
                        <i class="fa fa-fw fa-save"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" title="{{$item->estado == 1 ? 'Desactivar' : 'Activar'}}"
                            onclick="delete_grosor('{{$item->id_grosor_ramo}}', '{{$item->estado}}')">
                        <i class="fa fa-fw fa-{{$item->estado == 1 ? 'lock' : 'unlock'}}"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="text-center">
        <button type="button" class="btn btn-sm btn-success" onclick="store_grosor_ramo()" id="btn-guardar_grosor" style="display: none">
            <i class="fa fa-fw fa-save"></i> Guardar
        </button>
    </div>
</form>

<script>
    forms_grosor = 0;

    function add_grosor_ramo() {
        forms_grosor++;
        $('#table_listado_grosor_ramo').append('<tr id="row_form_' + forms_grosor + '">' +
            '<td class="text-center" style="border-color: #9d9d9d; padding: 5px">' +
            '<input type="text" class="text-center" id="nombre_grosor_' + forms_grosor + '" name="nombre_grosor_' + forms_grosor + '" required maxlength="250" width="100%"></td>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<i class="fa fa-fw fa-check"></i></td>' +
            '</tr>');
        $('#btn-del_form_grosor').show();
        $('#btn-guardar_grosor').show();
    }

    function del_grosor_ramo() {
        $('#btn-del_form_grosor').hide();
        $('#btn-guardar_grosor').hide();
        $('#row_form_' + forms_grosor).remove();
        forms_grosor--;
        if (forms_grosor > 0) {
            $('#btn-del_form_grosor').show();
            $('#btn-guardar_grosor').show();
        }
    }

    function store_grosor_ramo() {
        if (forms_grosor > 0) {
            if ($('#form-add_grosor_ramo').valid()) {
                arreglo = [];
                for (i = 1; i <= forms_grosor; i++) {
                    data = {
                        nombre: $('#nombre_grosor_' + i).val()
                    };
                    arreglo.push(data);
                }
                datos = {
                    _token: '{{csrf_token()}}',
                    arreglo: arreglo
                };
                post_jquery('{{url('configuracion/store_grosor_ramo')}}', datos, function () {
                    cerrar_modals();
                    admin_grosor_ramo();
                });
            }
        }
    }

    function update_grosor(id) {
        if ($('#form-update_grosor_' + id).valid()) {
            datos = {
                _token: '{{csrf_token()}}',
                id_grosor_ramo: id,
                nombre: $('#nombre_grosor_' + id).val()
            };
            post_jquery('{{url('configuracion/update_grosor_ramo')}}', datos, function () {
                cerrar_modals();
                admin_grosor_ramo();
            });
        }
    }

    function delete_grosor(id, estado) {
        mensaje = {
            title: estado == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar grosor' : '<i class="fa fa-fw fa-unlock"></i> Activar grosor',
            mensaje: estado == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este grosor?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este grosor?</div>',
        };
        modal_quest('modal_delete_grosor_ramo', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_grosor_ramo: id,
                estado: estado == 1 ? 0 : 1,
            };
            post_jquery('{{url('configuracion/delete_grosor_ramo')}}', datos, function () {
                cerrar_modals();
                admin_grosor_ramo();
            });
        });
    }
</script>