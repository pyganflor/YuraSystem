<form id="form-add-recepcion">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Fecha</span>
                <input type="datetime-local" id="fecha_ingreso" name="fecha_ingreso" required onchange="buscarCosechaByFecha()"
                       class="form-control text-center">
                <span class="input-group-addon" style="background-color: #e9ecef">
                    <input type="checkbox" id="check_fecha_ingreso_pasada" name="check_fecha_ingreso_pasada">
                    <label for="check_fecha_ingreso_pasada">Fecha-hora pasada</label>
                </span>
            </div>
        </div>
    </div>
    <input type="hidden" id="id_cosecha" name="id_cosecha">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Personal</span>
                <input type="number" class="form-control" id="personal" name="personal" required min="1">
                <span class="input-group-addon" style="background-color: #e9ecef">Hora inicio</span>
                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                <span class="input-group-btn">
            <button type="button" class="btn btn-success" title="Guardar" onclick="store_cosecha()">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Rendimiento</span>
                <span class="input-group-addon" id="html_rendimiento"></span>
                <span class="input-group-btn">
            <button type="button" class="btn btn-default" title="Ver rendimiento" onclick="ver_rendimiento()">
                <i class="fa fa-fw fa-eye"></i>
            </button>
        </span>
            </div>
        </div>
    </div>
    <table class="table-striped table-bordered table-hover" width="100%" style="border: 3px solid #9d9d9d" id="table_forms_tallos_mallas">
        <thead>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Módulo
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Variedad
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Mallas
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Tallos x malla
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <button type="button" class="btn btn-xs btn-primary" title="Añadir" onclick="add_tallo_malla()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
                <button type="button" class="btn btn-xs btn-danger" title="Quitar" onclick="del_tallo_malla()" id="btn_del_form"
                        style="display: none">
                    <i class="fa fa-fw fa-times"></i>
                </button>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr id="row_form_1">
            <td class="text-center" style="border-color: #9d9d9d">
                <select name="id_modulo_1" id="id_modulo_1" style="width: 100%" onchange="select_modulo_recepcion(1)" required>
                    @foreach($modulos as $mod)
                        <option value="{{$mod->id_modulo}}">{{$mod->nombre}}</option>
                    @endforeach
                </select>
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="text" class="text-center" readonly id="nombre_variedad_1" name="nombre_variedad_1" style="width: 100%" required>
                <input type="hidden" class="text-center" readonly id="id_variedad_1" name="id_variedad_1" style="width: 100%" required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" min="1" max="200" class="text-center" id="cantidad_mallas_1" name="cantidad_mallas_1" style="width: 100%"
                       required>
            </td>
            <td class="text-center" style="border-color: #9d9d9d" colspan="2">
                <input type="number" min="1" max="200" class="text-center" id="tallos_x_malla_1" name="tallos_x_malla_1" style="width: 100%"
                       required>
            </td>
        </tr>
        </tbody>
    </table>
</form>

<script>
    cant_forms = 1;

    set_max_today($('#fecha_ingreso'));

    buscarCosechaByFecha();

    select_modulo_recepcion(1);

    function buscarCosechaByFecha() {
        datos = {
            fecha: $('#fecha_ingreso').val()
        };
        $('#html_rendimiento').html('');

        get_jquery('{{url('recepcion/buscarCosechaByFecha')}}', datos, function (retorno) {
            $('#id_cosecha').val(retorno.id_cosecha);
            $('#personal').val(retorno.personal);
            $('#hora_inicio').val(retorno.hora_inicio);
            $('#html_rendimiento').html(retorno.rendimiento);

            if(datos['fecha'] < '{{date('Y-m-d')}}:00:00')
                $('#check_fecha_ingreso_pasada').prop('checked', true);
            else
                $('#check_fecha_ingreso_pasada').prop('checked', false);
        });
    }

    function store_cosecha() {
        datos = {
            _token: '{{csrf_token()}}',
            id_cosecha: $('#id_cosecha').val(),
            personal: $('#personal').val(),
            hora_inicio: $('#hora_inicio').val(),
            fecha_ingreso: $('#fecha_ingreso').val(),
        };
        if (datos['personal'] != '' && datos['hora_inicio'] != '') {
            post_jquery('{{url('recepcion/store_cosecha')}}', datos, function () {
                buscarCosechaByFecha();
            });
        }
    }

    function ver_rendimiento() {
        if ($('#id_cosecha').val() != '') {
            datos = {
                id_cosecha: $('#id_cosecha').val()
            };
            get_jquery('{{url('recepcion/ver_rendimiento')}}', datos, function (retorno) {
                modal_view('modal_view_ver_rendimiento', retorno, '<i class="fa fa-fw fa-balance-scale"></i> Rendimiento', true, false,
                    '{{isPC() ? '65%' : ''}}');
            });
        }
    }

    function select_modulo_recepcion(pos) {
        datos = {
            _token: '{{csrf_token()}}',
            modulo: $('#id_modulo_' + pos).val()
        };
        if (datos['modulo'] != '') {
            $.post('{{url('recepcion/select_modulo_recepcion')}}', datos, function (retorno) {
                $('#id_variedad_' + pos).val(retorno.id_variedad);
                $('#nombre_variedad_' + pos).val(retorno.nombre_variedad);
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
            });
        } else {
            $('#id_variedad_' + pos).val('');
            $('#nombre_variedad_' + pos).val('');
        }
    }
</script>
