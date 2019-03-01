<form id="form-add-recepcion">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Fecha</span>
                <input type="datetime-local" id="fecha_ingreso" name="fecha_ingreso" required onchange="buscarCosechaByFecha()"
                       class="form-control text-center">
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
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_forms_tallos_mallas">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                Variedad
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                Mallas
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                Tallos por malla
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                Módulo
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                <div class="form-group">
                    <button type="button" class="btn btn-xs btn-default" title="Añadir" onclick="add_tallo_malla()">
                        <i class="fa fa-fw fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" title="Quitar" onclick="del_tallo_malla()"
                            id="btn_del_form" style="display: none">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                </div>
            </th>
        </tr>
        </thead>
        <tr>
            <td style="border-color: #9d9d9d" class="text-center">
                <div class="form-group">
                    <select id="id_variedad_1" name="id_variedad_1" required class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($variedades as $item)
                            <option value="{{$item->id_variedad}}" class="option_variedades_form">
                                {{$item->planta->nombre}} - {{$item->siglas}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td style="border-color: #9d9d9d" class="text-center">
                <div class="form-group">
                    <input type="number" id="cantidad_mallas_1" name="cantidad_mallas_1" required class="form-control"
                           min="1" max="1000">
                    {{-- Configurar el máximo permitido de cantidad de mallas segun la config de la empresa --}}
                </div>
            </td>
            <td style="border-color: #9d9d9d" class="text-center">
                <div class="form-group">
                    <input type="number" id="tallos_x_malla_1" name="tallos_x_malla_1" required class="form-control"
                           min="1" max="50">
                    {{-- Configurar el máximo permitido de cantidad de tallos por malla segun la config de la empresa --}}
                </div>
            </td>
            <td style="border-color: #9d9d9d" class="text-center" colspan="2">
                <div class="form-group">
                    <select id="id_modulo_1" name="id_modulo_1" required class="form-control">
                        @foreach(getModulos() as $item)
                            <option value="{{$item->id_modulo}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
        </tr>
    </table>
</form>

<script>
    cant_forms = 1;

    set_max_today($('#fecha_ingreso'));

    buscarCosechaByFecha();

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
</script>