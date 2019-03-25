<div class="row">
    <div class="col-md-6">
        <div class="well">
            <ul class="list-unstyled">
                <li>
                    <input type="checkbox" id="check_filtro_anual" class="check_filtro_cosecha"
                           onchange="select_checkbox_cosecha('check_filtro_anual')">
                    <label for="check_filtro_anual" class="mouse-hand">Anual</label>
                </li>
                <legend style="margin-bottom: 0"></legend>
                <li>
                    <input type="checkbox" id="check_filtro_mensual" class="check_filtro_cosecha"
                           onchange="select_checkbox_cosecha('check_filtro_mensual')">
                    <label for="check_filtro_mensual" class="mouse-hand">Mensual</label>
                </li>
                <li>
                    <input type="checkbox" id="check_filtro_semanal" class="check_filtro_cosecha"
                           onchange="select_checkbox_cosecha('check_filtro_semanal')">
                    <label for="check_filtro_semanal" class="mouse-hand">Semanal</label>
                </li>
                <li>
                    <input type="checkbox" id="check_filtro_diario" class="check_filtro_cosecha"
                           onchange="select_checkbox_cosecha('check_filtro_diario')" checked>
                    <label for="check_filtro_diario" class="mouse-hand">Diario</label>
                </li>
                <legend style="margin-bottom: 0"></legend>
                <li>
                    <input type="checkbox" id="check_filtro_x_variedad" onchange="select_checkbox_cosecha_variedad('check_filtro_x_variedad')"
                           class="check_filtro_cosecha_variedad">
                    <label for="check_filtro_x_variedad" class="mouse-hand">Por Variedad</label>
                </li>
                <li>
                    <input type="checkbox" id="check_filtro_todas_variedad"
                           onchange="select_checkbox_cosecha_variedad('check_filtro_todas_variedad')"
                           class="check_filtro_cosecha_variedad">
                    <label for="check_filtro_todas_variedad" class="mouse-hand">Todas</label>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-6 text-center">
        <ul class="list-unstyled">
            <li style="margin-bottom: 10px">
                <label for="check_filtro_desde">Desde</label>
                <input type="date" id="check_filtro_desde">
            </li>
            <li style="margin-bottom: 10px">
                <label for="check_filtro_hasta">Hasta</label>
                <input type="date" id="check_filtro_hasta">
            </li>
            <li style="margin-bottom: 10px; display: none" class="op_check_filtro_x_variedad opciones_filtro">
                <label for="check_filtro_variedad">Variedad</label>
                <select name="check_filtro_variedad" id="check_filtro_variedad">
                    <option value="">Seleccione...</option>
                    @foreach(getVariedades() as $item)
                        <option value="{{$item->id_variedad}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </li>
            <li class="text-center">
                <button type="button" class="btn btn-xs btn-primary" onclick="buscar_reporte_cosecha()">
                    <i class="fa fa-fw fa-search"></i> Buscar
                </button>
            </li>
        </ul>
    </div>
</div>

<script>
    function select_checkbox_cosecha(id) {
        checks = $('.check_filtro_cosecha');
        for (i = 0; i < checks.length; i++) {
            if (checks[i].id != id) {
                checks[i].checked = false;
            }
        }
    }

    function select_checkbox_cosecha_variedad(id) {
        checks = $('.check_filtro_cosecha_variedad');
        for (i = 0; i < checks.length; i++) {
            if (checks[i].id != id) {
                checks[i].checked = false;
            }
        }

        $('.opciones_filtro').hide();
        if ($('#' + id).prop('checked')) {
            $('.op_' + id).show();
        }
    }

    function buscar_reporte_cosecha() {
        datos = {
            _token: '{{csrf_token()}}',
            anual: $('#check_filtro_anual').prop('checked'),
            mensual: $('#check_filtro_mensual').prop('checked'),
            semanal: $('#check_filtro_semanal').prop('checked'),
            diario: $('#check_filtro_diario').prop('checked'),
            x_variedad: $('#check_filtro_x_variedad').prop('checked'),
            total: $('#check_filtro_todas_variedad').prop('checked'),
            desde: $('#check_filtro_desde').val(),
            hasta: $('#check_filtro_hasta').val(),
            id_variedad: $('#check_filtro_variedad').val(),
        };
        /* ============= INDICADORES ===========*/
        get_jquery('{{url('crm_postcosecha/buscar_reporte_cosecha_indicadores')}}', datos, function (retorno) {
            $('#div_indicadores').html(retorno);
        });
        /* ============= COMPARACION ===========*/
        get_jquery('{{url('crm_postcosecha/buscar_reporte_cosecha_comparacion')}}', datos, function (retorno) {
            $('#div_cosecha_x_variedad_cosecha').html(retorno);
        });
        /* ============= CHART ===========*/
        get_jquery('{{url('crm_postcosecha/buscar_reporte_cosecha_chart')}}', datos, function (retorno) {
            $('#div_chart_cosecha').html(retorno);
        });

        //select_option_cosecha('cosecha_x_variedad');
    }
</script>