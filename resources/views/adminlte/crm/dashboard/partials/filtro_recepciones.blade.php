<div class="row">
    <div class="col-md-6">
        <div class="well">
            <ul class="list-unstyled">
                <li>
                    <input type="checkbox" id="check_filtro_anual" class="check_filtro_recepciones"
                           onchange="select_checkbox_recepciones('check_filtro_anual')">
                    <label for="check_filtro_anual">Anual</label>
                </li>
                <legend style="margin-bottom: 0"></legend>
                <li>
                    <input type="checkbox" id="check_filtro_semestral" class="check_filtro_recepciones"
                           onchange="select_checkbox_recepciones('check_filtro_semestral')">
                    <label for="check_filtro_semestral">Semestral</label>
                </li>
                <li>
                    <input type="checkbox" id="check_filtro_trimestral" class="check_filtro_recepciones"
                           onchange="select_checkbox_recepciones('check_filtro_trimestral')">
                    <label for="check_filtro_trimestral">Trimestral</label>
                </li>
                <legend style="margin-bottom: 0"></legend>
                <li>
                    <input type="checkbox" id="check_filtro_mensual" class="check_filtro_recepciones"
                           onchange="select_checkbox_recepciones('check_filtro_mensual')">
                    <label for="check_filtro_mensual">Mensual</label>
                </li>
                <li>
                    <input type="checkbox" id="check_filtro_semanal" class="check_filtro_recepciones"
                           onchange="select_checkbox_recepciones('check_filtro_semanal')">
                    <label for="check_filtro_semanal">Semanal</label>
                </li>
                <li>
                    <input type="checkbox" id="check_filtro_diario" class="check_filtro_recepciones"
                           onchange="select_checkbox_recepciones('check_filtro_diario')">
                    <label for="check_filtro_diario">Diario</label>
                </li>
                <legend style="margin-bottom: 0"></legend>
                <li>
                    <input type="checkbox" id="check_filtro_x_variedad">
                    <label for="check_filtro_x_variedad">Por Variedad</label>
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
            <li style="margin-bottom: 10px" class="op_check_filtro_x_variedad">
                <label for="check_filtro_variedad">Variedad</label>
                <select name="check_filtro_variedad" id="check_filtro_variedad">
                    <option value="">Seleccione...</option>
                    @foreach(getVariedades() as $item)
                        <option value="{{$item->id_variedad}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </li>
            <li class="text-center">
                <button type="button" class="btn btn-xs btn-primary">
                    <i class="fa fa-fw fa-search"></i> Buscar
                </button>
            </li>
        </ul>
    </div>
</div>

<script>
    function select_checkbox_recepciones(id) {
        checks = $('.check_filtro_recepciones');
        for (i = 0; i < checks.length; i++) {
            if (checks[i].id != id) {
                checks[i].checked = false;
            }
        }
    }
</script>