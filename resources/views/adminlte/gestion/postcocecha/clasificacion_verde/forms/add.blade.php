<div class="row">
    <div class="col-md-6">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Fecha de recepciones</span>
            <input type="date" id="fecha_recepciones" name="fecha_recepciones" required class="form-control text-center"
                   value="{{$fecha != '' ? $fecha : ''}}" onchange="buscar_recepciones_byFecha()">
            <span class="input-group-btn" title="Buscar recepciones">
                <button type="button" class="btn btn-default" onclick="buscar_recepciones_byFecha()">
                    <i class="fa fa-fw fa-search"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Fecha de Clasf. Verde</span>
            <input type="date" id="fecha_ingreso" name="fecha_ingreso" required class="form-control text-center">
        </div>
    </div>
</div>

<div id="form-add_clasificacion_verde">
    <div id="div_formulario_add">

    </div>
</div>
<script>
    @if($fecha == '')
    set_max_today($('#fecha_recepciones'));

    @endif

    function buscar_recepciones_byFecha() {
        datos = {
            fecha: $('#fecha_recepciones').val()
        };
        get_jquery('{{url('clasificacion_verde/buscar_recepciones_byFecha')}}', datos, function (retorno) {
            $('#div_formulario_add').html(retorno);
            $('#fecha_ingreso').val($('#fecha_recepciones').val());
        });
    }

    if ($('#fecha_recepciones').val() != '') {
        buscar_recepciones_byFecha();
    }

    function seleccionar_variedad(id_variedad, li) {
        datos = {
            id_variedad: id_variedad,
            id_clasificacion_verde: $('#id_clasificacion_verde').val()
        };
        get_jquery('{{url('clasificacion_verde/add/cargar_tabla_variedad')}}', datos, function (retorno) {
            $('#div_table_x_variedad').html(retorno);
            $('.list-group-item').removeClass('active');
            li.addClass('active');

            $('#html_total_tallos').html($('#total_tallos').val());
            $('#html_total_ramos').html($('#total_ramos').val());
            $('#html_desechos').html($('#desechos').val() + '%');

            variedades = $('.ids_variedad');
            for (i = 0; i < variedades.length; i++) {
                $('#badge_tallos_clasificados_x_variedad_' + variedades[i].id.substr(20)).html($('#tallos_clasificados_' + variedades[i].id.substr(20)).val());
            }
        });
    }
</script>